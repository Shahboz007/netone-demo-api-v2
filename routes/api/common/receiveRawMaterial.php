<?php

use App\Http\Controllers\ReceiveRawMaterialController;
use Illuminate\Support\Facades\Route;

Route::apiResource('receive-raw-materials', ReceiveRawMaterialController::class)->middleware('auth:sanctum');