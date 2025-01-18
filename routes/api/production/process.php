<?php

use App\Http\Controllers\ProductionProcessController;
use Illuminate\Support\Facades\Route;

Route::apiResource('production-processes', ProductionProcessController::class)->middleware('auth:sanctum');
