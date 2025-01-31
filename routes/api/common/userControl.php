<?php

use App\Http\Controllers\UserControlController;
use Illuminate\Support\Facades\Route;

Route::apiResource('/user-controls', UserControlController::class)->middleware("auth:sanctum");
