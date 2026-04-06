<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\SeientSessio;
use App\Models\Sessio;
use App\Models\PreuTarifa;
use App\Services\SocketService;
use App\Services\PeliculaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            'usuari_id' => 'nullable|integer|exists:usuaris,id',
            'guest_id' => 'nullable|string',
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
            'usuari_id' => 'nullable|integer|exists:usuaris,id',
            'guest_id' => 'nullable|string',
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

    /**
     * Obté els seients d'una sessió amb el seu estat.
     */
    public function getSeientsSessio($sessioId)
    {
        $sessio = Sessio::with('sala', 'tarifa')->find($sessioId);

        if (!$sessio) {
            return response()->json(['error' => 'Sessió no trobada'], 404);
        }

        // 1. Extraiem l'algorisme d'obtenir i endreçar i formatejar els seients
        $seientsFormatejats = $this->ponerBonicoSeientsSessio($sessio);

        // 2. Obtenim el preu bàsic per mostrar-lo al client des d'un inici
        $preuTarifaDefault = $this->obtenirPreuClientEstandard($sessio->tarifa_id);

        // 3. Obtenim el nom de la pel·lícula desde Redis
        $peliculaService = app(PeliculaService::class);
        $pelicula = $peliculaService->getByIdFromRedis($sessio->pellicula_id);
        $peliculaNom = $pelicula['titol'] ?? 'Sense títol';

        return response()->json([
            'sessio_id' => $sessio->id,
            'sala_id' => $sessio->sala_id,
            'sala_nom' => $sessio->sala->nom,
            'data_hora' => $sessio->data_hora,
            'tarifa_nom' => $sessio->tarifa->nom,
            'preu_tarifa' => $preuTarifaDefault,
            'pelicula_nom' => $peliculaNom,
            'seients' => $seientsFormatejats,
        ], 200);
    }

    /**
     * Consulta els seients a la BD, els agrupa per fila i en neteja les dades.
     */
    private function ponerBonicoSeientsSessio($sessio)
    {
        $seients = $sessio->seientsSessio()
            ->orderBy('fila')
            ->orderBy('numero')
            ->get()
            ->groupBy('fila');

        // Mapeig: extraiem només els atributs rellevants amagant dades sensibles de la BD
        return $seients->map(function ($filSeients) {
            return $filSeients->map(function ($seient) {
                return [
                    'id' => $seient->id,
                    'fila' => $seient->fila,
                    'numero' => $seient->numero,
                    'estat' => $seient->estat,
                ];
            });
        })->values();
    }

    /**
     * Cerca de preus (tipus de client = 1: base/estàndard)
     */
    private function obtenirPreuClientEstandard($tarifaId)
    {
        $preuTarifa = PreuTarifa::where('tarifa_id', $tarifaId)
            ->where('tipus_client_id', 1)
            ->first();

        return $preuTarifa ? floatval($preuTarifa->preu) : 0;
    }

    /**
     * Crea una reserva amb seients seleccionats (Síncron).
     */
    public function reservarSeients(Request $request, SocketService $socketService)
    {
        try {
            // Validem les dades que ens arriben de la petició
            $validated = $this->validarPeticioSeients($request);

            $isGuest = is_string($request->usuari_id) && str_starts_with($request->usuari_id, 'guest_');
            $finalUsuariId = $isGuest ? null : $validated['usuari_id'];
            $finalGuestId = $isGuest ? $validated['usuari_id'] : null;

            return DB::transaction(function () use ($validated, $finalUsuariId, $finalGuestId, $socketService) {

                $seients = $this->comprovarIBloquejarSeients($validated['seient_ids'], $validated['sessio_id']);

                // Calculem el preu segons la tarifa i tipus de client
                $preuPerSeient = $this->calcularPreuSeient($validated['sessio_id'], $validated['tipus_client_id']);
                $preuTotal = $preuPerSeient * count($validated['seient_ids']);

                // Creem el registre de la reserva (i la confirmem localment directament)
                $reserva = Reserva::create([
                    'usuari_id' => $finalUsuariId,
                    'guest_id' => $finalGuestId,
                    'sessio_id' => $validated['sessio_id'],
                    'preu_total' => $preuTotal,
                    'estat' => 'confirmada',
                ]);

                // Enllacem els seients a la reserva i els marquem definitivament com a ocupats/reservats
                $this->assignarSeientsAReserva($seients, $reserva, $validated['tipus_client_id'], $preuPerSeient);

                // Sockets
                $socketService->broadcastSeientsReservats($validated['sessio_id'], $validated['seient_ids']);

                return response()->json($reserva->load('seientsSessio'), 201);
            });
        } catch (\Exception $e) {
            $codiError = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 400;
            return response()->json(['error' => $e->getMessage()], $codiError);
        }
    }

    /**
     * Desocupa (allibera) seients d'una reserva
     */
    public function desocuparSeients(Request $request, SocketService $socketService)
    {
        // Validem les dades que ens arriben de la petició
        $validated = $request->validate([
            'reserva_id' => 'required|integer|exists:reserva,id',
            'seient_ids' => 'required|array|min:1',
            'seient_ids.*' => 'integer|exists:seients_sessio,id',
        ]);

        try {
            return DB::transaction(function () use ($validated, $socketService) {

                $reserva = Reserva::findOrFail($validated['reserva_id']);

                $seients = SeientSessio::whereIn('id', $validated['seient_ids'])
                    ->lockForUpdate()
                    ->get();

                if ($seients->count() !== count($validated['seient_ids'])) {
                    throw new \Exception('Alguns seients no són vàlids', 400);
                }

                $this->desvincularSeientsReserva($seients, $reserva);

                // Eliminem la reserva
                $sessioId = $reserva->sessio_id;
                $reserva->delete();

                // Socket
                $socketService->broadcastSeientsAlliberats($sessioId, $validated['seient_ids']);

                return response()->json([
                    'message' => 'Seients desocupats correctament i reserva eliminada',
                    'reserva_id' => $reserva->id,
                    'seients_alliberats' => $validated['seient_ids']
                ], 200);

            });
        } catch (\Exception $e) {
            $codiError = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 400;
            return response()->json(['error' => $e->getMessage()], $codiError);
        }
    }

    /**
     * Desvincula seients d'una reserva i els marca com a lliures
     */
    private function desvincularSeientsReserva($seients, $reserva)
    {
        foreach ($seients as $seient) {
            // Desvinculem del pivot
            $reserva->seientsSessio()->detach($seient->id);

            // Marquem com a lliure
            $seient->update([
                'estat' => 'lliure',
                'reservat_at' => null,
            ]);
        }
    }

    /**
     * Valida que els paràmetres enviats existeixin i siguin coherents.
     */
    private function validarPeticioSeients(Request $request)
    {
        // \Log::info('Request body:', $request->all());

        $isGuest = is_string($request->usuari_id) && str_starts_with($request->usuari_id, 'guest_');

        $rules = [
            'sessio_id' => 'required|integer|exists:sessions_cine,id',
            'seient_ids' => 'required|array|min:1',
            'seient_ids.*' => 'integer|exists:seients_sessio,id',
            'tipus_client_id' => 'required|integer',
        ];

        if ($isGuest) {
            $rules['usuari_id'] = 'required|string';
        } else {
            $rules['usuari_id'] = 'required|integer|exists:usuaris,id';
        }

        return $request->validate($rules);
    }

    /**
     * Comprova si els seients estan totalment lliures i els bloqueja temporalment.
     */
    private function comprovarIBloquejarSeients(array $seientIds, $sessioId)
    {
        $seients = SeientSessio::whereIn('id', $seientIds)
            ->where('sessio_id', $sessioId)
            ->lockForUpdate() // Pessimistic Lock
            ->get();

        if ($seients->count() !== count($seientIds)) {
            throw new \Exception('Alguns seients no són vàlids per a aquesta sessió', 400);
        }

        foreach ($seients as $seient) {
            if ($seient->estat !== 'lliure') {
                throw new \Exception("El seient {$seient->fila}{$seient->numero} ja no està disponible", 409);
            }
        }

        return $seients;
    }

    /**
     * Cerca a la BD quant val aquest seient per la tarifa de la sessió.
     */
    private function calcularPreuSeient($sessioId, $tipusClientId)
    {
        $sessio = Sessio::find($sessioId);
        $preuTarifa = PreuTarifa::where('tarifa_id', $sessio->tarifa_id)
            ->where('tipus_client_id', $tipusClientId)
            ->first();

        if (!$preuTarifa) {
            throw new \Exception("Preu no trobat per aquesta tarifa i tipus de client", 400);
        }

        return $preuTarifa->preu;
    }

    /**
     * Relaciona cada seient amb la Reserva (Taula Pivot) i ho marca com reservat.
     */
    private function assignarSeientsAReserva($seients, $reserva, $tipusClientId, $preuAplicat)
    {
        foreach ($seients as $seient) {
            $reserva->seientsSessio()->attach(
                $seient->id,
                [
                    'tipus_client_id' => $tipusClientId,
                    'preu_aplicat' => $preuAplicat,
                ]
            );

            // Marquem l'estat del seient
            $seient->update([
                'estat' => 'reservat',
                'reservat_at' => now(),
            ]);
        }
    }

    /**
     * Expira reserves temporals (per scheduler)
     */
    public function expirarReservesTemporals()
    {
        try {
            // Alliberar seients reservats fa més de 5 minuts
            $seients = SeientSessio::where('estat', 'reservat')
                ->where('reservat_at', '<', now()->subMinutes(5))
                ->get();

            foreach ($seients as $seient) {
                $seient->update([
                    'estat' => 'lliure',
                    'reservat_at' => null
                ]);
            }

            return response()->json([
                'message' => 'Reserves expirades',
                'seients_alliberats' => $seients->count()
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Obté les reserves de l'usuari per a una sessió específica
     */
    public function lesMevesReserves($usuarioId, $sessioId)
    {
        try {
            $isGuest = is_string($usuarioId) && str_starts_with($usuarioId, 'guest_');

            $reserves = Reserva::where(function ($query) use ($usuarioId, $isGuest) {
                if ($isGuest) {
                    // Si és guest, buscar només per guest_id
                    $query->where('guest_id', $usuarioId);
                } else {
                    // Si és usuari, buscar per usuari_id
                    $query->where('usuari_id', intval($usuarioId));
                }
            })
                ->where('sessio_id', $sessioId)
                ->with('seientsSessio')
                ->get();

            // Extraem els seients de les reserves
            $seients = $reserves->flatMap(function ($reserva) {
                return $reserva->seientsSessio->map(function ($seient) {
                    return [
                        'id' => $seient->id,
                        'fila' => $seient->fila,
                        'numero' => $seient->numero,
                        'estat' => $seient->estat,
                        'reserva_id' => $seient->pivot->reserva_id ?? null,
                    ];
                });
            });

            return response()->json($seients->values(), 200);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
