<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiIslaController;
use App\Http\Controllers\Api\ApiMunicipioController;
use App\Http\Controllers\Api\ApiPopulationController;

Route::get('/status', function () {
    return response()->json([
        'status' => 'ok',
        'laravel' => app()->version(),
    ]);
});

Route::get('/isla/population', [ApiIslaController::class, 'population']);
Route::get('/isla/search', [ApiIslaController::class, 'search']);

Route::get('/municipios/{gdc}/population', [ApiMunicipioController::class, 'population']);
Route::get('/municipios/search', [ApiMunicipioController::class, 'search']);

Route::get('/population/evolution', [ApiPopulationController::class, 'evolution']);
