<?php

use App\Http\Controllers\ProductionRecipeController;
use Illuminate\Support\Facades\Route;

Route::apiResource("/production-recipes", ProductionRecipeController::class)->middleware('auth:sanctum');
