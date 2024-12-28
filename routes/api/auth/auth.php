<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'auth', ['middleware' => 'auth:sanctum']], function () {
  Route::post('/login', [AuthController::class, 'login'])->name('login');
});
