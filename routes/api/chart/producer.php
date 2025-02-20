<?php

use App\Http\Controllers\Chart\ProducerDashboardController;
use Illuminate\Support\Facades\Route;

Route::group(["prefix" => "producer", "middleware" => ["auth:sanctum"]], function () {
    Route::get('dashboard', [ProducerDashboardController::class, 'index']);
});
