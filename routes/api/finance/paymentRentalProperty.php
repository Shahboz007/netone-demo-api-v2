<?php

use App\Http\Controllers\Payment\PaymentRentalPropertyController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => '/payments/rental-property', 'middleware' => ['auth:sanctum']], function () {
    Route::get('/', [PaymentRentalPropertyController::class, 'index']);
    Route::post('/', [PaymentRentalPropertyController::class, 'store']);
    Route::get('/{id}', [PaymentRentalPropertyController::class, 'show']);
});
