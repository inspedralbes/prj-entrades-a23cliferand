<?php

namespace App\Http\Controllers;

use App\Models\Sessio;
use Illuminate\Http\Request;

class SessioController extends Controller
{
    /**
     * Mostra una llista de totes les sessions.
     */
    public function index()
    {
        return response()->json(Sessio::with('pellicula', 'sala')->get(), 200);
    }

    /**
     * Emmagatzema una nova sessió a la base de dades.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pellicula_id' => 'required|integer|exists:pellicules,id',
            'sala_id' => 'required|integer|exists:sales,id',
            'data_hora' => 'required|date_format:Y-m-d H:i:s',
            'preu_base' => 'required|numeric|min:0',
        ]);

        try {
            $sessio = Sessio::create($validated);
            return response()->json($sessio, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Mostra la sessió especificada.
     */
    public function show($id)
    {
        $sessio = Sessio::with('pellicula', 'sala', 'seients')->find($id);

        if (!$sessio) {
            return response()->json(['error' => 'Sessió no trobada'], 404);
        }

        return response()->json($sessio, 200);
    }

    /**
     * Actualitza la sessió especificada a la base de dades.
     */
    public function update(Request $request, $id)
    {
        $sessio = Sessio::find($id);

        if (!$sessio) {
            return response()->json(['error' => 'Sessió no trobada'], 404);
        }

        $validated = $request->validate([
            'pellicula_id' => 'sometimes|integer|exists:pellicules,id',
            'sala_id' => 'sometimes|integer|exists:sales,id',
            'data_hora' => 'sometimes|date_format:Y-m-d H:i:s',
            'preu_base' => 'sometimes|numeric|min:0',
        ]);

        try {
            $sessio->update($validated);
            return response()->json($sessio, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Elimina la sessió especificada de la base de dades.
     */
    public function destroy($id)
    {
        $sessio = Sessio::find($id);

        if (!$sessio) {
            return response()->json(['error' => 'Sessió no trobada'], 404);
        }

        $sessio->delete();
        return response()->json(['message' => 'Sessió eliminada correctament'], 200);
    }
}
