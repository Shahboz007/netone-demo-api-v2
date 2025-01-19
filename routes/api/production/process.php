<?php

use App\Http\Controllers\ProductionProcessController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'production-processes', 'middleware' => ['auth:sanctum']], function(){
    Route::get('', [ProductionProcessController::class,'index']);
    Route::get('{id}', [ProductionProcessController::class,'show']);
    Route::put('{id}/finish', [ProductionProcessController::class,'finish']);
    Route::delete('{id}/cancel', [ProductionProcessController::class,'cancel']);
});
