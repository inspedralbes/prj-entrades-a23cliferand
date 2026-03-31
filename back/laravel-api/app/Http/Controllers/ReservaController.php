<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use Illuminate\Http\Request;

class ReservaController extends Controller
{
    /**
     * Mostra una llista de totes les reserves.
     */
    public function index()
    {
        return response()->json(Reserva::with('usuari', 'seients')->get(), 200);
    }

    /**
     * Emmagatzema una nova reserva a la base de dades.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'usuari_id' => 'required|integer|exists:usuaris,id',
            'sessio_id' => 'required|integer|exists:sessions_cine,id',
            'preu_total' => 'required|numeric|min:0',
            'estat' => 'required|in:pendent,confirmada,caducada',
        ]);

        try {
            $reserva = Reserva::create($validated);
            return response()->json($reserva, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Mostra la reserva especificada.
     */
    public function show($id)
    {
        $reserva = Reserva::with('usuari', 'seients')->find($id);

        if (!$reserva) {
            return response()->json(['error' => 'Reserva no trobada'], 404);
        }

        return response()->json($reserva, 200);
    }

    /**
     * Actualitza la reserva especificada a la base de dades.
     */
    public function update(Request $request, $id)
    {
        $reserva = Reserva::find($id);

        if (!$reserva) {
            return response()->json(['error' => 'Reserva no trobada'], 404);
        }

        $validated = $request->validate([
            'usuari_id' => 'sometimes|integer|exists:usuaris,id',
            'sessio_id' => 'sometimes|integer|exists:sessions_cine,id',
            'preu_total' => 'sometimes|numeric|min:0',
            'estat' => 'sometimes|in:pendent,confirmada,caducada',
        ]);

        try {
            $reserva->update($validated);
            return response()->json($reserva, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Elimina la reserva especificada de la base de dades.
     */
    public function destroy($id)
    {
        $reserva = Reserva::find($id);

        if (!$reserva) {
            return response()->json(['error' => 'Reserva no trobada'], 404);
        }

        $reserva->delete();
        return response()->json(['message' => 'Reserva eliminada correctament'], 200);
    }
}
