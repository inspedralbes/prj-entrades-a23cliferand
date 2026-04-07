<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SocketService
{
    protected string $socketUrl;

    public function __construct()
    {
        $this->socketUrl = env('SOCKET_SERVER_URL');
    }
    /**
     * Envia un missatge de reserva de seients al servidor de sockets.
     */
    public function broadcastSeientsReservats(int $sessionId, array $seientsReservats): bool
    {
        try {
            error_log("Enviament al socket: " . $this->socketUrl);
            error_log("Dades: sessionId=$sessionId, seats=" . json_encode($seientsReservats));

            $response = Http::timeout(2)->post($this->socketUrl . '/api/broadcast', [
                'event' => 'seats-reserved',
                'session_id' => $sessionId,
                'seats' => $seientsReservats
            ]);

            error_log("Resposta: " . $response->status() . " - " . $response->body());

            if ($response->successful()) {
                error_log("Broadcast enviat correctament");
                return true;
            }

            error_log("Error en resposta: " . $response->body());
            Log::warning('Socket broadcast error: ' . $response->body());
            return false;

        } catch (\Exception $e) {
            error_log("Excepció: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Envia un missatge de lliberació de seients al servidor de sockets.
     */
    public function broadcastSeientsAlliberats(int $sessionId, array $seientsAlliberats): bool
    {
        try {
            error_log("Enviament al socket: " . $this->socketUrl);
            error_log("Dades: sessionId=$sessionId, seats=" . json_encode($seientsAlliberats));

            $response = Http::timeout(2)->post($this->socketUrl . '/api/broadcast', [
                'event' => 'seats-released',
                'session_id' => $sessionId,
                'seats' => $seientsAlliberats
            ]);

            error_log("Resposta: " . $response->status() . " - " . $response->body());

            if ($response->successful()) {
                error_log("Broadcast seients-alliberats enviat correctament");
                return true;
            }

            error_log("Error en resposta: " . $response->body());
            return false;

        } catch (\Exception $e) {
            error_log("Excepció: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Envia un missatge de venda de seients al servidor de sockets.
     */
    public function broadcastSeientsVenuts(int $sessionId, array $seientsVenuts): bool
    {
        try {
            $response = Http::timeout(2)->post($this->socketUrl . '/api/broadcast', [
                'event' => 'seats-sold',
                'session_id' => $sessionId,
                'seats' => $seientsVenuts
            ]);

            return $response->successful();

        } catch (\Exception $e) {
            error_log("Excepció: " . $e->getMessage());
            return false;
        }
    }
}
