<?php

namespace App\Http\Controllers;

use App\Models\Sala;
use Illuminate\Http\Request;

class SalaController extends Controller
{
    /**
     * Mostra una llista de totes les sales.
     */
    public function index()
    {
        return response()->json(Sala::all(), 200);
    }

    /**
     * Emmagatzema una nova sala a la base de dades.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'capacitat' => 'required|integer|min:1',
            'files' => 'required|integer|min:1',
            'columnes' => 'required|integer|min:1',
        ]);

        try {
            $sala = Sala::create($validated);
            return response()->json($sala, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Mostra la sala especificada.
     */
    public function show($id)
    {
        $sala = Sala::with('sessions')->find($id);

        if (!$sala) {
            return response()->json(['error' => 'Sala no trobada'], 404);
        }

        return response()->json($sala, 200);
    }

    /**
     * Actualitza la sala especificada a la base de dades.
     */
    public function update(Request $request, $id)
    {
        $sala = Sala::find($id);

        if (!$sala) {
            return response()->json(['error' => 'Sala no trobada'], 404);
        }

        $validated = $request->validate([
            'nom' => 'sometimes|string|max:255',
            'capacitat' => 'sometimes|integer|min:1',
            'files' => 'sometimes|integer|min:1',
            'columnes' => 'sometimes|integer|min:1',
        ]);

        try {
            $sala->update($validated);
            return response()->json($sala, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Elimina la sala especificada de la base de dades.
     */
    public function destroy($id)
    {
        $sala = Sala::find($id);

        if (!$sala) {
            return response()->json(['error' => 'Sala no trobada'], 404);
        }

        $sala->delete();
        return response()->json(['message' => 'Sala eliminada correctament'], 200);
    }
}
