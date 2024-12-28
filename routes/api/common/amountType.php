<?php

use App\Http\Controllers\AmountTypeController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'amount-types', ['middleware' => 'auth:sanctum']], function(){
  Route::get('/', [AmountTypeController::class, 'index']);
  Route::get('/{amount_type}', [AmountTypeController::class, 'show']);
});