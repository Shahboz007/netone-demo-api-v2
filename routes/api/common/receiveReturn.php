<?php

use App\Http\Controllers\ReturnReceiveController;
use Illuminate\Support\Facades\Route;

Route::group(["prefix" => "receive-returns", 'middleware' => ['auth:sanctum']], function () {
    Route::get("/", [ReturnReceiveController::class, 'index']);
    Route::get("/{id}", [ReturnReceiveController::class, 'show']);
    Route::post("/", [ReturnReceiveController::class, 'store']);
});
