<?php

use App\Http\Controllers\CustomerRentalPropertyController;
use Illuminate\Support\Facades\Route;

Route::apiResource('customer-rental-property', CustomerRentalPropertyController::class)->middleware('auth:sanctum');
