<?php

use App\Http\Controllers\Finance\PaymentCustomerController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'payment-customers', 'middleware' => ['auth:sanctum']], function(){
    Route::get('/', [PaymentCustomerController::class, 'index']);
    Route::get('{paymentCustomer}', [PaymentCustomerController::class, 'show']);
    Route::post('/', [PaymentCustomerController::class, 'store']);
});
