<?php

use App\Http\Controllers\RentalPropertyController;
use Illuminate\Support\Facades\Route;

Route::apiResource('rental-property', RentalPropertyController::class)->middleware('auth:sanctum');
