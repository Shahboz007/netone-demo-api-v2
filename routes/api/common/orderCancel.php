<?php

use App\Http\Controllers\OrderCancelController;
use Illuminate\Support\Facades\Route;

Route::apiResource('order-cancels', OrderCancelController::class)->middleware('auth:sanctum');