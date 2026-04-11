<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Reserva;
use App\Models\Sessio;
use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function stats(): JsonResponse
    {
        try {
            $stats = $this->getStats();
            $dailyReserves = $this->getDailyReserves();
            $reservesByState = $this->getReservesByState();

            return response()->json([
                'stats' => $stats,
                'daily_reserves' => $dailyReserves,
                'reserves_by_state' => $reservesByState
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error carregant estadístiques',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function getStats(): array
    {
        $sales = Reserva::whereDate('created_at', Carbon::today())->count();
        $sessions = Sessio::whereDate('data_hora', Carbon::today())->count();
        $reserves = Reserva::count();

        $pelicules = 0;
        try {
            $keys = Redis::keys('pelicula:*');
            $pelicules = count($keys);
        } catch (\Exception $e) {
            // Redis not available
        }

        return [
            'sales' => $sales,
            'sessions' => $sessions,
            'pelicules' => $pelicules,
            'reserves' => $reserves
        ];
    }

    private function getDailyReserves(): array
    {
        $days = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $count = Reserva::whereDate('created_at', $date)->count();
            $dayName = Carbon::now()->subDays($i)->format('D');
            $days[] = [
                'day' => $dayName,
                'count' => $count
            ];
        }

        return $days;
    }

    private function getReservesByState(): array
    {
        $pendent = Reserva::where('estat', 'pendent')->count();
        $confirmada = Reserva::where('estat', 'confirmada')->count();
        $caducada = Reserva::where('estat', 'caducada')->count();

        return [
            'pendent' => $pendent,
            'confirmada' => $confirmada,
            'caducada' => $caducada
        ];
    }
}
