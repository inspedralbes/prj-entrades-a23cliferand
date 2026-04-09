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
use Illuminate\Validation\ValidationException;

class ReservaController extends Controller
{
    public function index()
    {
        return response()->json(Reserva::with('usuari', 'seientsSessio')->get(), 200);
    }

    public function show($id)
    {
        $reserva = Reserva::with('seientsSessio', 'sessio.sala')->find($id);
        if (!$reserva)
            return response()->json(['error' => 'Reserva no trobada'], 404);

        // Transformem la resposta per incloure dades de la película i sessió
        $response = $reserva->toArray();

        if ($reserva->sessio) {
            // Afegim dades de la sessió
            $response['sessio_data'] = [
                'data_hora' => $reserva->sessio->data_hora,
                'sala_nom' => $reserva->sessio->sala->nom ?? null,
                'sala_id' => $reserva->sessio->sala_id,
            ];

            // Intentem obtenir dades de la película del servei extern
            try {
                $peliculaService = app('App\Services\PeliculaService');
                $pelicula = $peliculaService->getByIdFromRedis($reserva->sessio->pellicula_id);
                $response['pelicula'] = [
                    'titol' => $pelicula['titol'] ?? 'Desconegut',
                    'cartell' => $pelicula['cartell'] ?? null,
                    'imdbId' => $reserva->sessio->pellicula_id,
                ];
            } catch (\Exception $e) {
                $response['pelicula'] = [
                    'titol' => 'Desconegut',
                    'cartell' => null,
                    'imdbId' => $reserva->sessio->pellicula_id,
                ];
            }
        }

        return response()->json($response, 200);
    }

    /**
     * Bloc temporal d'un seient a la taula seients_sessio (SENSE crear Reserva)
     */
    public function reservarSeients(Request $request, SocketService $socketService)
    {
        try {
            $validated = $request->validate([
                'sessio_id' => 'required|integer|exists:sessions_cine,id',
                'seient_ids' => 'required|array|min:1',
                'seient_ids.*' => 'integer|exists:seients_sessio,id',
                'usuari_id' => 'required|string',
            ]);

            $isGuest = str_starts_with($validated['usuari_id'], 'guest_');
            $finalUsuariId = $isGuest ? null : intval($validated['usuari_id']);
            $finalGuestId = $isGuest ? $validated['usuari_id'] : null;

            return DB::transaction(function () use ($validated, $finalUsuariId, $finalGuestId, $socketService) {

                $seients = SeientSessio::whereIn('id', $validated['seient_ids'])
                    ->where('sessio_id', $validated['sessio_id'])
                    ->lockForUpdate()
                    ->get();

                foreach ($seients as $seient) {
                    if ($seient->estat !== 'lliure') {
                        throw new \Exception("El seient {$seient->fila}{$seient->numero} ja no està disponible", 409);
                    }

                    $seient->update([
                        'estat' => 'reservat',
                        'reservat_at' => now(),
                        'usuari_id' => $finalUsuariId,
                        'guest_id' => $finalGuestId
                    ]);
                }

                $socketService->broadcastSeientsReservats($validated['sessio_id'], $validated['seient_ids']);

                return response()->json(['message' => 'Seients bloquejats temporalment'], 200);
            });
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Allibera els seients de seients_sessio
     */
    public function desocuparSeients(Request $request, SocketService $socketService)
    {
        $validated = $request->validate([
            'sessio_id' => 'required|integer',
            'seient_ids' => 'required|array|min:1',
            'seient_ids.*' => 'integer|exists:seients_sessio,id',
        ]);

        try {
            return DB::transaction(function () use ($validated, $socketService) {
                $seients = SeientSessio::whereIn('id', $validated['seient_ids'])->get();

                foreach ($seients as $seient) {
                    $seient->update([
                        'estat' => 'lliure',
                        'reservat_at' => null,
                        'usuari_id' => null,
                        'guest_id' => null
                    ]);
                }

                $socketService->broadcastSeientsAlliberats($validated['sessio_id'], $validated['seient_ids']);

                return response()->json(['message' => 'Seients alliberats'], 200);
            });
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Transfereix els seients reservats temporalment d'un guest a l'usuari autenticat
     */
    public function transferirReservesGuest(Request $request)
    {
        $validated = $request->validate([
            'guest_id' => 'required|string|starts_with:guest_',
        ]);

        try {
            $usuari = $request->user();

            $actualitzats = SeientSessio::where('guest_id', $validated['guest_id'])
                ->where('estat', 'reservat')
                ->update([
                    'usuari_id' => $usuari->id,
                    'guest_id' => null,
                ]);

            return response()->json([
                'message' => 'Reserves transferides correctament',
                'updated_count' => $actualitzats,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Crea la reserva final i les línies a la pivot
     */
    public function confirmarCompraFinal(Request $request, SocketService $socketService)
    {
        $validated = $request->validate([
            'sessio_id' => 'required|integer|exists:sessions_cine,id',
            'usuari_id' => 'required|string',
            'email' => 'nullable|email',
            'seients' => 'required|array|min:1',
            'seients.*.id' => 'required|integer|exists:seients_sessio,id',
            'seients.*.tipus_client_id' => 'required|integer|exists:tipus_client,id',
            'seients.*.preu_aplicat' => 'required|numeric',
            'total' => 'required|numeric',
        ]);

        try {
            $isGuest = str_starts_with($validated['usuari_id'], 'guest_');
            $finalEmail = $isGuest ? ($validated['email'] ?? null) : null;

            if ($isGuest && !$finalEmail) {
                throw ValidationException::withMessages([
                    'email' => 'Cal indicar un correu electrònic vàlid per completar la compra.'
                ]);
            }

            return DB::transaction(function () use ($validated, $socketService, $isGuest, $finalEmail) {

                $reserva = Reserva::create([
                    'usuari_id' => $isGuest ? null : intval($validated['usuari_id']),
                    'guest_id' => $isGuest ? $validated['usuari_id'] : null,
                    'email' => $finalEmail,
                    'sessio_id' => $validated['sessio_id'],
                    'preu_total' => $validated['total'],
                    'estat' => 'confirmada',
                ]);

                $seientIds = [];
                foreach ($validated['seients'] as $item) {
                    $reserva->seientsSessio()->attach($item['id'], [
                        'tipus_client_id' => $item['tipus_client_id'],
                        'preu_aplicat' => $item['preu_aplicat'],
                    ]);

                    SeientSessio::where('id', $item['id'])->update([
                        'estat' => 'venut',
                        'reservat_at' => null,
                        'usuari_id' => null,
                        'guest_id' => null
                    ]);
                    $seientIds[] = $item['id'];
                }

                // Emetem el socket perquè tothom vegi que ja estan venuts
                $socketService->broadcastSeientsVenuts($validated['sessio_id'], $seientIds);

                return response()->json($reserva->load('seientsSessio'), 201);
            });
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Obté les seients d'una sessió
     */
    public function getSeientsSessio($sessioId)
    {
        $sessio = Sessio::with('sala', 'tarifa')->find($sessioId);

        if (!$sessio) {
            return response()->json(['error' => 'Sessió no trobada'], 404);
        }

        $seientsFormatejats = $this->ponerBonicoSeientsSessio($sessio);
        $preuTarifaDefault = $this->obtenirPreuClientEstandard($sessio->tarifa_id);

        $peliculaService = app(PeliculaService::class);
        $pelicula = $peliculaService->getByIdFromRedis($sessio->pellicula_id);

        return response()->json([
            'sessio_id' => $sessio->id,
            'sala_id' => $sessio->sala_id,
            'sala_nom' => $sessio->sala->nom,
            'data_hora' => $sessio->data_hora,
            'tarifa_id' => $sessio->tarifa_id,
            'tarifa_nom' => $sessio->tarifa->nom,
            'preu_tarifa' => $preuTarifaDefault,
            'pelicula_nom' => $pelicula['titol'] ?? 'Sense títol',
            'seients' => $seientsFormatejats,
        ], 200);
    }

    /**
     * Formatea els seients d'una sessió
     */
    private function ponerBonicoSeientsSessio($sessio)
    {
        $seients = $sessio->seientsSessio()
            ->orderBy('fila')
            ->orderBy('numero')
            ->get()
            ->groupBy('fila');

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
     * Obté el preu estàndard per a un client
     */
    private function obtenirPreuClientEstandard($tarifaId)
    {
        $preuTarifa = PreuTarifa::where('tarifa_id', $tarifaId)
            ->where('tipus_client_id', 1)
            ->first();
        return $preuTarifa ? floatval($preuTarifa->preu) : 0;
    }

    /**
     * Expira les reserves temporals
     */
    public function expirarReservesTemporals()
    {
        try {
            $seients = SeientSessio::where('estat', 'reservat')
                ->where('reservat_at', '<', now()->subMinutes(5))
                ->get();

            foreach ($seients as $seient) {
                $seient->update([
                    'estat' => 'lliure',
                    'reservat_at' => null,
                    'usuari_id' => null,
                    'guest_id' => null
                ]);
            }
            return response()->json(['message' => 'Bloquejos expirats'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Obté les reserves d'un usuari per a una sessió
     */
    public function lesMevesReserves($usuariId, $sessioId)
    {
        try {
            $isGuest = str_starts_with($usuariId, 'guest_');
            $seients = SeientSessio::where('sessio_id', $sessioId)
                ->where(function ($q) use ($usuariId, $isGuest) {
                    if ($isGuest)
                        $q->where('guest_id', $usuariId);
                    else
                        $q->where('usuari_id', intval($usuariId));
                })
                ->where('estat', 'reservat')
                ->get();

            return response()->json($seients->map(fn($s) => [
                'id' => $s->id,
                'fila' => $s->fila,
                'numero' => $s->numero,
                'estat' => $s->estat,
            ]), 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
