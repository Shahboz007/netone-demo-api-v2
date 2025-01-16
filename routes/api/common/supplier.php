<?php

use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

Route::apiResource('suppliers', SupplierController::class)->middleware('auth:sanctum');
