<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PeliculaController;
use App\Http\Controllers\UsuariController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\SessioController;
use App\Http\Controllers\SalaController;
use App\Http\Controllers\TarifaController;


// Aquí pots definir els teus endpoints API.
// Aquestes rutes són carregades amb el middleware 'api'.

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Pelicules sync (fusionado en PeliculaController)
Route::post('/pelicules/sync/all', [PeliculaController::class, 'syncAll']);
Route::post('/pelicules/sync/{imdbId}', [PeliculaController::class, 'syncSingle']);

// CRUD Routes
Route::apiResource('usuaris', UsuariController::class);
Route::apiResource('pelicules', PeliculaController::class);
Route::apiResource('reservas', ReservaController::class);
Route::apiResource('sessions', SessioController::class);
Route::apiResource('sales', SalaController::class);
Route::apiResource('tarifes', TarifaController::class);
