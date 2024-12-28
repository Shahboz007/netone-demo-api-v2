<?php

use App\Http\Controllers\Common\RawMaterialController;
use Illuminate\Support\Facades\Route;

Route::apiResource('/raw-materials', RawMaterialController::class)->middleware('auth:sanctum');