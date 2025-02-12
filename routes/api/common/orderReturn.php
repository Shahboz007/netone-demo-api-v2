<?php

use App\Http\Controllers\OrderReturnController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'order-returns', 'middleware' => ['auth:sanctum']], function () {
    Route::get('/', [OrderReturnController::class, 'index']);
    Route::post('/', [OrderReturnController::class, 'store']);
    Route::get('{orderReturn}', [OrderReturnController::class, 'show']);
});
