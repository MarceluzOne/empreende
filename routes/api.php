<?php

use App\Http\Controllers\Api\ServiceApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/services/external-register', [ServiceApiController::class, 'store']);
Route::get('/services/grouped', [ServiceApiController::class, 'getGroupedProviders']);
