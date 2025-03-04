<?php

use App\Http\Controllers\GetMoneyController;
use Illuminate\Support\Facades\Route;

Route::apiResource('get-money', GetMoneyController::class)->middleware('auth:sanctum');
