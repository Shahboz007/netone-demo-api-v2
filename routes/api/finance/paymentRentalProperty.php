<?php

use App\Http\Controllers\RentalPropertyController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => '/payments/rental-property', 'middleware' => ['auth:sanctum']], function () {
    Route::get('/', [RentalPropertyController::class, 'index']);
    Route::post('/', [RentalPropertyController::class, 'store']);
    Route::get('/{id}', [RentalPropertyController::class, 'show']);
});
