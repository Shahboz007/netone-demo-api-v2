<?php

use App\Http\Controllers\CurrencyController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'currencies', 'middleware' => ['auth:sanctum']], function () {
    Route::get('/', [CurrencyController::class, 'index']);
    Route::get('{id}', [CurrencyController::class, 'show']);
});
