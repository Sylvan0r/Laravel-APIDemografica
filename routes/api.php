<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiIslaController;
use App\Http\Controllers\Api\ApiMunicipioController;

Route::get('/status', function () {
    return response()->json([
        'status' => 'ok',
        'laravel' => app()->version(),
    ]);
});

Route::get('/municipios/{gdc}/population', [ApiMunicipioController::class, 'show']);

Route::apiResource('isla', ApiIslaController::class)->only(['index', 'show']);
