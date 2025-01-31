<?php

use App\Http\Controllers\UserControlController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'user-controls', 'middleware' => ['auth:sanctum']], function(){
    Route::get('/', [UserControlController::class, 'index']);
    Route::post('/', [UserControlController::class, 'store']);
    Route::get('{userControl}', [UserControlController::class, 'show']);
    Route::put('{userControl}', [UserControlController::class, 'update']);
    Route::put('{userControl}/password', [UserControlController::class, 'updatePassword']);
    Route::put('{userControl}/status', [UserControlController::class, 'updateStatus']);
    Route::delete('{userControl}', [UserControlController::class, 'destroy']);
});
