<?php

use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'orders', 'middleware' => ['auth:sanctum']], function () {
    Route::get('/', [OrderController::class, 'index']);
    Route::post('/', [OrderController::class, 'store']);
    Route::get('/{id}', [OrderController::class, 'show']);
    Route::put('/{id}/confirm', [OrderController::class, 'confirm']);
    Route::put('/{id}/completed', [OrderController::class, 'completed']);
    Route::put('/{id}/submitted', [OrderController::class, 'submitted']);
});
