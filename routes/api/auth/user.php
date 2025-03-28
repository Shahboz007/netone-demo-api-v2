<?php

use App\Http\Controllers\Auth\UserProfileController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'user', 'middleware' => ['auth:sanctum']], function () {
  Route::get('profile', [UserProfileController::class, 'profile']);
});
