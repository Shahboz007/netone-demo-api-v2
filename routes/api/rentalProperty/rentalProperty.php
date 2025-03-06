<?php

use App\Http\Controllers\RentalPropertyController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'rental-property', 'middleware' => ['auth:sanctum']], function () {
    Route::get('/', [RentalPropertyController::class, 'index']);
    Route::get('/{id}', [RentalPropertyController::class, 'show']);
    Route::post('/', [RentalPropertyController::class, 'store']);
    Route::put('/{id}', [RentalPropertyController::class, 'update']);
    Route::delete('/{id}', [RentalPropertyController::class, 'destroy']);
});
