<?php

use App\Http\Controllers\AmountSettingsController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'amount-type-settings', 'middleware' => ['auth:sanctum']], function () {
    Route::get('/', [AmountSettingsController::class, 'index']);
    Route::get('/{id}', [AmountSettingsController::class, 'show']);
    Route::get('/{fromId}/{toId}', [AmountSettingsController::class, 'showByAmountTypes']);
});
