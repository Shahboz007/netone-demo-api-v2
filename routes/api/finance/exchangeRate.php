<?php

use App\Http\Controllers\ExchangeRateController;
use Illuminate\Support\Facades\Route;

Route::group(["prefix" => "exchange-rates", 'middleware' => ['auth:sanctum']], function () {
    Route::get('', [ExchangeRateController::class, 'index']);
    Route::get('{exchangeRate}', [ExchangeRateController::class, 'show']);
    Route::put('{exchangeRate}', [ExchangeRateController::class, 'update']);
});
