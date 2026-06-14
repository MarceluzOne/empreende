<?php

/*
|--------------------------------------------------------------------------
| Rotas de API do site Empreende Vitória
|--------------------------------------------------------------------------
|
| Carregadas pelo App\Providers\SiteServiceProvider com:
|   - prefixo de URL:   /api/empreendevitoria
|   - grupo middleware: api
|
*/

use App\Http\Controllers\Api\EventoApiController;
use App\Http\Controllers\Api\ServiceApiController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:30,1')->group(function () {
    Route::post('/services/external-register', [ServiceApiController::class, 'store']);
    Route::get('/services/grouped', [ServiceApiController::class, 'getGroupedProviders']);
});

Route::middleware('throttle:60,1')->group(function () {
    Route::get('/eventos', [EventoApiController::class, 'index']);
});
