<?php

use App\Http\Controllers\DepartController;
use Illuminate\Support\Facades\Route;

Route::apiResource('departments', DepartController::class)->middleware('auth:sanctum');
