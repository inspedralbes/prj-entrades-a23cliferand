<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;

class RedisMovieService
{
    public function getAllFromRedis(): array
    {
        $keys = Redis::keys('pelicula:*');
        $movies = [];

        foreach ($keys as $key) {
            $data = Redis::hgetall($key);
            if ($data) {
                $movies[] = $data;
            }
        }

        return $movies;
    }

    public function getByIdFromRedis(string $imdbId): ?array
    {
        $key = "pelicula:{$imdbId}";
        $data = Redis::hgetall($key);

        return $data ?: null;
    }

    public function getPelicula(string $imdbId): ?array
    {
        $redisData = $this->getByIdFromRedis($imdbId);

        if ($redisData) {
            return $redisData;
        }

        return $this->fetchFromApi($imdbId);
    }

    public function fetchFromApi(string $imdbId): ?array
    {
        $response = Http::get("https://imdbapi.dev/api/v1/title/{$imdbId}");
        
        if (!$response->successful()) {
            return null;
        }

        $data = $response->json();
        
        return [
            'imdb_id' => $imdbId,
            'titol' => $data['title'] ?? '',
            'tipus' => $data['type'] ?? '',
            'any' => $data['year'] ?? '',
            'rating' => $data['rating'] ?? '',
            'duracio' => $data['runtime'] ?? '',
            'generes' => implode(',', $data['genres'] ?? []),
            'sinopsi' => $data['synopsis'] ?? '',
            'cartell' => $data['poster'] ?? '',
        ];
    }

    public function getAllPelicules(): array
    {
        return $this->getAllFromRedis();
    }
}
