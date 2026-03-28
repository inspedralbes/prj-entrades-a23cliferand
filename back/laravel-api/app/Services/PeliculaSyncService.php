<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;

class PeliculaSyncService
{
    /**
     * Sincronitza una pel·lícula des de la IMDb API a Redis per la seva ID.
     * Retorna true si ha anat bé, false si hi ha hagut un error.
     */
    public function syncMovie(string $imdbId): bool
    {
        $response = Http::timeout(20)->retry(2, 300)->get("https://api.imdbapi.dev/titles/{$imdbId}");

        if (!$response->successful()) {
            // No podem escriure al command directament, podem llançar una excepció o retornar false
            return false;
        }

        $raw = $response->json();
        $data = $this->extractMovieNode(is_array($raw) ? $raw : []);

        $posterUrl = (string) $this->pick($data, [
            'poster',
            'poster.url',
            'primaryImage.url',
            'image.url',
        ], '');

        [$posterB64, $posterMime] = $this->downloadImageAsBase64($posterUrl);

        $payload = [
            'imdb_id'      => $imdbId,
            'titol'        => $this->asString($this->pick($data, [
                'title',
                'primaryTitle',
                'name',
                'titleText.text',
                'primary_title',
            ], '')),
            'titol_original' => $this->asString($this->pick($data, [
                'originalTitle',
                'original_title',
                'originalTitleText.text',
            ], '')),
            'tipus'        => $this->asString($this->pick($data, [
                'type',
                'titleType.text',
                'titleType.id',
            ], '')),
            'any'          => $this->asString($this->pick($data, [
                'year',
                'startYear',
                'releaseYear.year',
                'release_date.year',
            ], '')),
            'rating'       => $this->asString($this->pick($data, [
                'rating.aggregateRating',
                'rating.aggregate_rating',
                'ratingsSummary.aggregateRating',
                'aggregateRating',
                'rating',
            ], '')),
            'vots'         => $this->asString($this->pick($data, [
                'voteCount',
                'rating.voteCount',
                'vote_count',
                'rating.vote_count',
                'ratingsSummary.voteCount',
                'numVotes',
            ], '')),
            'duracio'      => $this->normalizeRuntime($this->pick($data, [
                'runtime',
                'runtimeSeconds',
                'runtime.seconds',
                'runtimeMinutes',
                'runtime.displayableProperty.value.plainText',
            ], '')),
            'generes'      => $this->asCsv($this->pick($data, [
                'genres',
                'genres.genres',
                'genre',
            ], [])),
            'sinopsi'      => $this->asString($this->pick($data, [
                'synopsis',
                'plot',
                'plot.plotText.plainText',
                'description',
            ], '')),
            'cartell'      => $posterUrl,                 // URL original
            'cartell_b64'  => $posterB64 ?? '',           // imagen en base64 (DB Redis)
            'cartell_mime' => $posterMime ?? '',
            'imatges'      => $this->asCsv($this->extractImageUrls($this->pick($data, [
                'images',
                'images.items',
                'photos',
            ], []))),
        ];

        $key = "pelicula:{$imdbId}";
        
        // Provem de recuperar la data en què es va crear inicialment a Redis
        $dataCreacio = Redis::hget($key, 'data_creacio');
        
        $payload['data_sync'] = now()->toDateTimeString();
        $payload['data_creacio'] = $dataCreacio ?: now()->toDateTimeString();

        Redis::del($key);

        foreach ($payload as $field => $value) {
            Redis::hset($key, $field, (string) $value);
        }

        return true;
    }

    private function extractMovieNode(array $raw): array
    {
        if (isset($raw['result']) && is_array($raw['result'])) {
            return $raw['result'];
        }

        if (isset($raw['results'][0]) && is_array($raw['results'][0])) {
            return $raw['results'][0];
        }

        return $raw;
    }

    private function pick(array $data, array $paths, mixed $default = ''): mixed
    {
        foreach ($paths as $path) {
            $value = data_get($data, $path);
            if ($this->filled($value)) {
                return $value;
            }
        }

        return $default;
    }

    private function filled(mixed $value): bool
    {
        if ($value === null) return false;
        if (is_string($value) && trim($value) === '') return false;
        if (is_array($value) && count($value) === 0) return false;
        return true;
    }

    private function asString(mixed $value): string
    {
        if (is_array($value)) {
            return $this->asCsv($value);
        }

        return trim((string) $value);
    }

    private function asCsv(mixed $value): string
    {
        if (!is_array($value)) {
            return trim((string) $value);
        }

        $items = [];
        foreach ($value as $item) {
            if (is_array($item)) {
                $items[] = (string) ($item['name'] ?? $item['text'] ?? $item['title'] ?? $item['url'] ?? '');
            } else {
                $items[] = (string) $item;
            }
        }

        $items = array_values(array_filter(array_map('trim', $items), fn($v) => $v !== ''));
        return implode(',', $items);
    }

    private function extractImageUrls(mixed $value): array
    {
        if (!is_array($value)) return [];

        $urls = [];
        foreach ($value as $item) {
            if (is_string($item)) {
                $urls[] = $item;
                continue;
            }

            if (is_array($item)) {
                $url = $item['url'] ?? ($item['image']['url'] ?? null);
                if (is_string($url) && $url !== '') {
                    $urls[] = $url;
                }
            }
        }

        return array_values(array_unique($urls));
    }

    private function normalizeRuntime(mixed $runtime): string
    {
        if (is_numeric($runtime)) {
            $n = (int) $runtime;
            if ($n > 500) {
                return (string) intdiv($n, 60);
            }
            return (string) $n;
        }

        if (is_array($runtime) && isset($runtime['seconds']) && is_numeric($runtime['seconds'])) {
            return (string) intdiv((int) $runtime['seconds'], 60);
        }

        return trim((string) $runtime);
    }

    private function downloadImageAsBase64(string $url): array
    {
        if ($url === '') {
            return [null, null];
        }

        $img = Http::timeout(20)->retry(1, 200)->get($url);
        if (!$img->successful()) {
            return [null, null];
        }

        $body = $img->body();
        if ($body === '') {
            return [null, null];
        }

        if (strlen($body) > 5 * 1024 * 1024) {
            return [null, null]; // Ignorem advertències per consola en el servei
        }

        $mime = $img->header('Content-Type') ?: 'application/octet-stream';
        return [base64_encode($body), $mime];
    }
}
