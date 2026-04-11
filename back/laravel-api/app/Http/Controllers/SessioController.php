<?php

namespace App\Http\Controllers;

use App\Models\Sessio;
use App\Models\SeientSessio;
use App\Models\Sala;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SessioController extends Controller
{
    /**
     * Mostra una llista de totes les sessions amb dades d'ocupació.
     */
    public function index()
    {
        $sessions = Sessio::with('sala', 'tarifa', 'seientsSessio')
            ->get()
            ->map(function ($sessio) {
                return $this->sessionWithOccupancy($sessio);
            });

        return response()->json($sessions, 200);
    }

    /**
     * Emmagatzema una o varies sessions a la base de dades.
     */
    public function store(Request $request)
    {
        $data = $request->all();

        if (isset($data['sessions']) && is_array($data['sessions'])) {
            $sessions = $data['sessions'];
            $created = [];
            $errors = [];

            foreach ($sessions as $index => $session) {
                $result = $this->crearSessio($session, $index);
                if (isset($result['error'])) {
                    $errors[] = $result['error'];
                } else {
                    $created[] = $result['sessio'];
                }
            }

            return response()->json([
                'message' => 'Sessions processades',
                'created_count' => count($created),
                'created' => $created,
                'errors' => $errors
            ], 201);
        }

        $result = $this->crearSessio($data, 0);
        if (isset($result['error'])) {
            return response()->json($result['error'], 400);
        }
        return response()->json($result['sessio'], 201);
    }

    private function crearSessio($sessionData, $index = 0)
    {
        $validator = validator($sessionData, [
            'pellicula_id' => 'required|string',
            'sala_id' => 'required|integer|exists:sales,id',
            'tarifa_id' => 'required|integer|exists:tarifes,id',
            'data_hora' => 'required|date_format:Y-m-d H:i:s',
            'preu_base' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return ['error' => [
                'index' => $index,
                'errors' => $validator->errors()->toArray()
            ]];
        }

        try {
            $sessio = Sessio::create($validator->validated());

            $this->crearSeientsSessio($sessio);

            return ['sessio' => $sessio];
        } catch (\Exception $e) {
            return ['error' => [
                'index' => $index,
                'error' => $e->getMessage()
            ]];
        }
    }

    private function crearSeientsSessio(Sessio $sessio)
    {
        $sala = Sala::with('seients')->find($sessio->sala_id);
        
        if (!$sala || $sala->seients->isEmpty()) {
            return;
        }

        $seients = $sala->seients->map(function ($seient) use ($sessio) {
            return [
                'sessio_id' => $sessio->id,
                'fila' => $seient->etiqueta_fila,
                'numero' => $seient->num_seient,
                'estat' => 'lliure',
            ];
        });

        SeientSessio::insert($seients->toArray());
    }

    /**
     * Mostra la sessió especificada.
     */
    public function show($id)
    {
        $sessio = Sessio::with('sala', 'tarifa', 'seientsSessio')->find($id);

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
            'tarifa_id' => 'sometimes|integer|exists:tarifes,id',
            'data_hora' => 'sometimes|date_format:Y-m-d H:i:s',
            'preu_base' => 'sometimes|numeric|min:0',
        ]);

        try {
            $sessio->update($validated);

            if (isset($validated['sala_id']) && $validated['sala_id'] != $sessio->getOriginal('sala_id')) {
                SeientSessio::where('sessio_id', $sessio->id)->delete();
                $this->crearSeientsSessio($sessio);
            }

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

        SeientSessio::where('sessio_id', $sessio->id)->delete();
        $sessio->delete();
        return response()->json(['message' => 'Sessió eliminada correctament'], 200);
    }

    /**
     * Obté les sessions d'una pel·lícula específica amb dades d'ocupació.
     */
    public function getByPelicula($peliculaId)
    {
        $sessions = Sessio::where('pellicula_id', $peliculaId)
            ->with('sala', 'tarifa', 'seientsSessio')
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
            'tarifa' => $sessio->tarifa,
            'pellicula' => ['imdb_id' => $sessio->pellicula_id],
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
