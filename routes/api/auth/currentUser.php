<?php

use App\Http\Controllers\Auth\AuthUserController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' =>'user', 'middleware' => ['auth:sanctum']], function(){
  Route::get('/', [AuthUserController::class, 'user']);
});