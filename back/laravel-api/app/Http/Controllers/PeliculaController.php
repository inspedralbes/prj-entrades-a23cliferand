<?php

namespace App\Http\Controllers;

use App\Models\Sessio;
use App\Services\PeliculaService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PeliculaController extends Controller
{
    protected PeliculaService $peliculaService;

    public function __construct(PeliculaService $peliculaService)
    {
        $this->peliculaService = $peliculaService;
    }

    /**
     * Mostra una llista de totes les pel·lícules.
     */
    public function index()
    {
        $pelicules = $this->peliculaService->getAllPelicules();
        return response()->json($pelicules, 200);
    }

    /**
     * Emmagatzema una nova pel·lícula a la base de dades.
     */
    public function store(Request $request)
    {
        return response()->json(['error' => 'Les pel·lícules es sincronitzen automàticament des de Redis'], 400);
    }

    /**
     * Mostra la pel·lícula especificada.
     */
    public function show($id)
    {
        $pellicula = $this->peliculaService->getByIdFromRedis($id);

        if (!$pellicula) {
            return response()->json(['error' => 'Pel·lícula no trobada'], 404);
        }

        return response()->json($pellicula, 200);
    }

    /**
     * Actualitza la pel·lícula especificada a la base de dades.
     */
    public function update(Request $request, $id)
    {
        return response()->json(['error' => 'No es permeten actualitzacions de pel·lícules'], 400);
    }

    /**
     * Elimina la pel·lícula especificada de la base de dades.
     */
    public function destroy($id)
    {
        return response()->json(['error' => 'No es permeten eliminar pel·lícules'], 400);
    }

    /**
     * Sincronitza una pel·lícula en concret donant el seu ID.
     */
    public function syncSingle(string $imdbId): JsonResponse
    {
        $success = $this->peliculaService->syncMovie($imdbId);

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
            $success = $this->peliculaService->syncMovie((string) $imdbId);
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
