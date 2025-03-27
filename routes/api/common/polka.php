<?php

use App\Http\Controllers\PolkaController;
use Illuminate\Support\Facades\Route;

Route::apiResource('polkas', PolkaController::class)->middleware(['auth:sanctum']);
