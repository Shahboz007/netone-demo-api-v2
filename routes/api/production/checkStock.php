<?php

use App\Http\Controllers\Production\CheckStockController;
use Illuminate\Support\Facades\Route;

Route::group(["prefix" => "/production/check-stock", "middleware" => ['auth:sanctum']], function () {
    Route::get('/', [CheckStockController::class, 'index']);
});
