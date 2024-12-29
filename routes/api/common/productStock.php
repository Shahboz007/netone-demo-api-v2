<?php

use App\Http\Controllers\ProductStockController;
use Illuminate\Support\Facades\Route;

Route::apiResource('product-stocks', ProductStockController::class)->middleware('auth:sanctum');