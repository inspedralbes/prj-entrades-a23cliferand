<?php

namespace App\Http\Controllers;

use App\Models\Sessio;
use Illuminate\Http\Request;

class SessioController extends Controller
{
    /**
     * Mostra una llista de totes les sessions amb dades d'ocupació.
     */
    public function index()
    {
        $sessions = Sessio::with('sala', 'seientsSessio')
            ->get()
            ->map(function ($sessio) {
                return $this->sessionWithOccupancy($sessio);
            });

        return response()->json($sessions, 200);
    }

    /**
     * Emmagatzema una nova sessió a la base de dades.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pellicula_id' => 'required|string',
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
        $sessio = Sessio::with('sala', 'seientsSessio')->find($id);

        if (!$sessio) {
            return response()->json(['error' => 'Sessió no trobada'], 404);
        }

        return response()->json($this->sessionWithOccupancy($sessio), 200);
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
            'pellicula_id' => 'sometimes|string',
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

    /**
     * Obté les sessions d'una pel·lícula específica amb dades d'ocupació.
     */
    public function getByPelicula($peliculaId)
    {
        $sessions = Sessio::where('pellicula_id', $peliculaId)
            ->with('sala', 'seientsSessio')
            ->get()
            ->map(function ($sessio) {
                return $this->sessionWithOccupancy($sessio);
            });

        return response()->json($sessions, 200);
    }

    /**
     * Formatea una sessió amb informació d'ocupació.
     */
    private function sessionWithOccupancy($sessio)
    {
        $seients = $sessio->seientsSessio;
        $totalSeients = $seients->count();
        $seientLliures = $seients->where('estat', 'lliure')->count();
        $seientReservats = $seients->where('estat', 'reservat')->count();
        $seientVenuts = $seients->where('estat', 'venut')->count();

        $percentatge = $totalSeients > 0
            ? round((($seientReservats + $seientVenuts) / $totalSeients) * 100, 2)
            : 0;

        return [
            'id' => $sessio->id,
            'pellicula_id' => $sessio->pellicula_id,
            'sala_id' => $sessio->sala_id,
            'tarifa_id' => $sessio->tarifa_id,
            'data_hora' => $sessio->data_hora->format('Y-m-d H:i:s'),
            'created_at' => $sessio->created_at,
            'updated_at' => $sessio->updated_at,
            'sala' => $sessio->sala,
            'occupancy' => [
                'total_seients' => $totalSeients,
                'seients_lliures' => $seientLliures,
                'seients_reservats' => $seientReservats,
                'seients_venuts' => $seientVenuts,
                'percentatge_ocupat' => $percentatge,
            ],
        ];
    }
}
