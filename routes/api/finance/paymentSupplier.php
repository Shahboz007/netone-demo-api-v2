<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentSupplierController;

Route::group(['prefix' => 'payment-suppliers', 'middleware' => ['auth:sanctum']], function () {
    Route::get('/', [PaymentSupplierController::class, 'index']);
    Route::post('/', [PaymentSupplierController::class, 'store']);
    Route::get('{paymentSupplier}', [PaymentSupplierController::class, 'show']);
});
