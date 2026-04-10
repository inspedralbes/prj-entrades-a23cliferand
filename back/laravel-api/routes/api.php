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
use App\Http\Controllers\AdminController;


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

// CRUD Routes - USUARIS
Route::get('/usuaris', [UsuariController::class, 'index']);
Route::get('/usuaris/{id}', [UsuariController::class, 'show']);
Route::post('/usuaris', [UsuariController::class, 'store'])->middleware('auth:sanctum');
Route::put('/usuaris/{id}', [UsuariController::class, 'update'])->middleware('auth:sanctum');
Route::delete('/usuaris/{id}', [UsuariController::class, 'destroy'])->middleware('auth:sanctum');

// CRUD Routes - PELICULES
Route::get('/pelicules', [PeliculaController::class, 'index']);
Route::get('/pelicules/{id}', [PeliculaController::class, 'show']);
Route::post('/pelicules', [PeliculaController::class, 'store'])->middleware('auth:sanctum');
Route::put('/pelicules/{id}', [PeliculaController::class, 'update'])->middleware('auth:sanctum');
Route::delete('/pelicules/{id}', [PeliculaController::class, 'destroy'])->middleware('auth:sanctum');

// CRUD Routes - RESERVES (sin auth para permitir guests)
Route::get('/reserves', [ReservaController::class, 'index']);
Route::get('/reserves/{id}', [ReservaController::class, 'show']);
Route::post('/reserves', [ReservaController::class, 'store']);
Route::put('/reserves/{id}', [ReservaController::class, 'update']);
Route::delete('/reserves/{id}', [ReservaController::class, 'destroy']);

// CRUD Routes - SESSIONS
Route::get('/sessions', [SessioController::class, 'index']);
Route::get('/sessions/{id}', [SessioController::class, 'show']);
Route::get('/sessions/pelicula/{peliculaId}', [SessioController::class, 'getByPelicula']);
Route::post('/sessions', [SessioController::class, 'store'])->middleware('auth:sanctum');
Route::put('/sessions/{id}', [SessioController::class, 'update'])->middleware('auth:sanctum');
Route::delete('/sessions/{id}', [SessioController::class, 'destroy'])->middleware('auth:sanctum');

// CRUD Routes - SALES
Route::get('/sales', [SalaController::class, 'index']);
Route::get('/sales/{id}', [SalaController::class, 'show']);
Route::post('/sales', [SalaController::class, 'store'])->middleware('auth:sanctum');
Route::put('/sales/{id}', [SalaController::class, 'update'])->middleware('auth:sanctum');
Route::delete('/sales/{id}', [SalaController::class, 'destroy'])->middleware('auth:sanctum');

// CRUD Routes - TARIFES
Route::get('/tarifes', [TarifaController::class, 'index']);
Route::get('/tarifes/{id}', [TarifaController::class, 'show']);
Route::post('/tarifes', [TarifaController::class, 'store'])->middleware('auth:sanctum');
Route::put('/tarifes/{id}', [TarifaController::class, 'update'])->middleware('auth:sanctum');
Route::delete('/tarifes/{id}', [TarifaController::class, 'destroy'])->middleware('auth:sanctum');

// Reserves
Route::get('/sessions/{sessioId}/seients', [ReservaController::class, 'getSeientsSessio']);
Route::get('/reserves/usuari/{usuariId}/sessio/{sessioId}', [ReservaController::class, 'lesMevesReserves'])->where('usuariId', '.*');
Route::post('/reserves/seient_reservar', [ReservaController::class, 'reservarSeients']);
Route::post('/reserves/seient_desocupar', [ReservaController::class, 'desocuparSeients']);
Route::post('/reserves/confirmar', [ReservaController::class, 'confirmarCompraFinal']);
Route::post('/reserves/transferir-guest', [ReservaController::class, 'transferirReservesGuest'])->middleware('auth:sanctum');
Route::post('/reserves/expirar', [ReservaController::class, 'expirarReservesTemporals']);

// Admin
Route::get('/admin/stats', [AdminController::class, 'stats']);

