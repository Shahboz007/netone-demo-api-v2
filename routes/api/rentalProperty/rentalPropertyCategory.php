<?php

use App\Http\Controllers\RentalPropertyCategoryController;
use Illuminate\Support\Facades\Route;

Route::apiResource('rental-property-categories', RentalPropertyCategoryController::class)->middleware('auth:sanctum');