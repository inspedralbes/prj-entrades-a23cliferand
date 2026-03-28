<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PeliculaSyncController;


// Aquí pots definir els teus endpoints API.
// Aquestes rutes són carregades amb el middleware 'api'.

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/pelicules/sync/all', [PeliculaSyncController::class, 'syncAll']);
Route::post('/pelicules/sync/{imdbId}', [PeliculaSyncController::class, 'syncSingle']);
