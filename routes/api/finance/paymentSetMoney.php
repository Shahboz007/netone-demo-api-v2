<?php

use App\Http\Controllers\PaymentSetMoneyController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' =>'payments/set-money', 'middleware' => ['auth:sanctum']],function () {
    Route::get('/', [PaymentSetMoneyController::class, 'index']);
    Route::post('/', [PaymentSetMoneyController::class, 'store']);
    Route::get('/{id}', [PaymentSetMoneyController::class, 'show']);
});
