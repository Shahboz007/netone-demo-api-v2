<?php

use App\Http\Controllers\Finance\PaymentGetMoneyController;
use Illuminate\Support\Facades\Route;

Route::group(["prefix" => "payments/get-money", 'middleware' => ['auth:sanctum']], function () {
    Route::get('/', [PaymentGetMoneyController::class, 'index']);
    Route::get('/{paymentGetMoneyId}', [PaymentGetMoneyController::class, 'show']);
    Route::post('/', [PaymentGetMoneyController::class, 'store']);
});
