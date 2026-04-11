<?php

namespace App\Http\Controllers;

use App\Models\Tarifa;
use App\Models\TipusClient;
use App\Models\PreuTarifa;
use Illuminate\Http\Request;

class TarifaController extends Controller
{
    /**
     * Obté tots els tipus de client.
     */
    public function getTipusClient()
    {
        return response()->json(TipusClient::all(), 200);
    }

    /**
     * Mostra una llista de totes les tarifes amb els seus preus.
     */
    public function index()
    {
        $tarifes = Tarifa::with('preus.tarifa', 'preus.tipusClient')->get();
        return response()->json($tarifes, 200);
    }

    /**
     * Emmagatzema una nova tarifa a la base de dades.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'descripcio' => 'nullable|string',
            'preus' => 'required|array',
            'preus.*.tipus_client_id' => 'required|integer|exists:tipus_client,id',
            'preus.*.preu' => 'required|numeric|min:0',
        ]);

        try {
            $tarifa = Tarifa::create([
                'nom' => $validated['nom'],
                'descripcio' => $validated['descripcio'] ?? null,
            ]);

            foreach ($validated['preus'] as $preu) {
                PreuTarifa::create([
                    'tarifa_id' => $tarifa->id,
                    'tipus_client_id' => $preu['tipus_client_id'],
                    'preu' => $preu['preu'],
                ]);
            }

            return response()->json($tarifa->fresh('preus'), 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Mostra la tarifa especificada.
     */
    public function show($id)
    {
        $tarifa = Tarifa::with('preus.tipusClient')->find($id);

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
            'preus' => 'sometimes|array',
            'preus.*.tipus_client_id' => 'required_with:preus|integer|exists:tipus_client,id',
            'preus.*.preu' => 'required_with:preus|numeric|min:0',
        ]);

        try {
            $tarifa->update([
                'nom' => $validated['nom'] ?? $tarifa->nom,
                'descripcio' => $validated['descripcio'] ?? $tarifa->descripcio,
            ]);

            if (isset($validated['preus'])) {
                PreuTarifa::where('tarifa_id', $tarifa->id)->delete();
                
                foreach ($validated['preus'] as $preu) {
                    PreuTarifa::create([
                        'tarifa_id' => $tarifa->id,
                        'tipus_client_id' => $preu['tipus_client_id'],
                        'preu' => $preu['preu'],
                    ]);
                }
            }

            return response()->json($tarifa->fresh('preus'), 200);
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

        PreuTarifa::where('tarifa_id', $tarifa->id)->delete();
        $tarifa->delete();
        return response()->json(['message' => 'Tarifa eliminada correctament'], 200);
    }
}
