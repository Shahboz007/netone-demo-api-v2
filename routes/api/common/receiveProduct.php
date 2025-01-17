<?php

use App\Http\Controllers\ReceiveProductController;
use Illuminate\Support\Facades\Route;

Route::apiResource('receive-products', ReceiveProductController::class)->middleware('auth:sanctum');
