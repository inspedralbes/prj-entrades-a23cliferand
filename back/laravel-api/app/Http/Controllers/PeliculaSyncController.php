<?php

namespace App\Http\Controllers;

use App\Models\Sessio;
use App\Services\PeliculaSyncService;
use Illuminate\Http\JsonResponse;

class PeliculaSyncController extends Controller
{
    protected PeliculaSyncService $syncService;

    public function __construct(PeliculaSyncService $syncService)
    {
        $this->syncService = $syncService;
    }

    /**
     * Sincronitza una pel·lícula en concret donant el seu ID.
     */
    public function syncSingle(string $imdbId): JsonResponse
    {
        $success = $this->syncService->syncMovie($imdbId);

        if (!$success) {
            return response()->json(['message' => "Error al fetchejar o desar l'ID {$imdbId}"], 500);
        }

        return response()->json(['message' => "La pel·lícula {$imdbId} s'ha sincronitzat correctament a Redis!"]);
    }

    /**
     * Sincronitza totes les pel·lícules basant-se en les sessions actuals.
     */
    public function syncAll(): JsonResponse
    {
        $sessions = Sessio::distinct()->pluck('pellicula_id')->filter();
        
        $count = 0;
        $errors = [];

        foreach ($sessions as $imdbId) {
            $success = $this->syncService->syncMovie((string) $imdbId);
            if ($success) {
                $count++;
            } else {
                $errors[] = $imdbId;
            }
        }

        return response()->json([
            'message' => "Sincronització completada.",
            'synced_count' => $count,
            'errors' => $errors
        ]);
    }
}
