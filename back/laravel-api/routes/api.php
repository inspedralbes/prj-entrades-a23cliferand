<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PeliculaController;
use App\Http\Controllers\UsuariController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\SessioController;
use App\Http\Controllers\SalaController;
use App\Http\Controllers\TarifaController;
use App\Http\Controllers\AuthController;


// Aquí pots definir els teus endpoints API.
// Aquestes rutes són carregades amb el middleware 'api'.

// Auth
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Pelicules sync (fusionado en PeliculaController)
Route::post('/pelicules/sync/all', [PeliculaController::class, 'syncAll']);
Route::post('/pelicules/sync/{imdbId}', [PeliculaController::class, 'syncSingle']);

// CRUD Routes
Route::apiResource('usuaris', UsuariController::class);
Route::apiResource('pelicules', PeliculaController::class);
Route::apiResource('reserves', ReservaController::class);
Route::apiResource('sessions', SessioController::class);
Route::apiResource('sales', SalaController::class);
Route::apiResource('tarifes', TarifaController::class);

// Reserves
Route::get('/sessions/{sessioId}/seients', [ReservaController::class, 'getSeientsSessio']);
Route::get('/reserves/usuario/{usuarioId}/sessio/{sessioId}', [ReservaController::class, 'lesMevesReserves'])
    ->where('usuarioId', '.*');
Route::post('/reserves/seient_reservar', [ReservaController::class, 'reservarSeients']);
Route::post('/reserves/seient_desocupar', [ReservaController::class, 'desocuparSeients']);
Route::post('/reserves/confirmar', [ReservaController::class, 'confirmarCompraFinal']);

// Scheduler - Expirar reserves temporals (per cron job)
Route::post('/seients/expirar-reserves-temporals', [ReservaController::class, 'expirarReservesTemporals']);

