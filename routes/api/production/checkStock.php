<?php

use App\Http\Controllers\Production\CheckStockController;
use Illuminate\Support\Facades\Route;

Route::group(["prefix" => "/production/check-stock"], function () {
    Route::get('/', [CheckStockController::class, 'index']);
});
