<?php

namespace App\Http\Controllers;

use App\Models\Tarifa;
use Illuminate\Http\Request;

class TarifaController extends Controller
{
    /**
     * Mostra una llista de totes les tarifes.
     */
    public function index()
    {
        return response()->json(Tarifa::all(), 200);
    }

    /**
     * Emmagatzema una nova tarifa a la base de dades.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'descripcio' => 'nullable|string',
            'activa' => 'boolean',
        ]);

        try {
            $tarifa = Tarifa::create($validated);
            return response()->json($tarifa, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Mostra la tarifa especificada.
     */
    public function show($id)
    {
        $tarifa = Tarifa::with('preus')->find($id);

        if (!$tarifa) {
            return response()->json(['error' => 'Tarifa no trobada'], 404);
        }

        return response()->json($tarifa, 200);
    }

    /**
     * Actualitza la tarifa especificada a la base de dades.
     */
    public function update(Request $request, $id)
    {
        $tarifa = Tarifa::find($id);

        if (!$tarifa) {
            return response()->json(['error' => 'Tarifa no trobada'], 404);
        }

        $validated = $request->validate([
            'nom' => 'sometimes|string|max:255',
            'descripcio' => 'sometimes|string|nullable',
            'activa' => 'sometimes|boolean',
        ]);

        try {
            $tarifa->update($validated);
            return response()->json($tarifa, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Elimina la tarifa especificada de la base de dades.
     */
    public function destroy($id)
    {
        $tarifa = Tarifa::find($id);

        if (!$tarifa) {
            return response()->json(['error' => 'Tarifa no trobada'], 404);
        }

        $tarifa->delete();
        return response()->json(['message' => 'Tarifa eliminada correctament'], 200);
    }
}
