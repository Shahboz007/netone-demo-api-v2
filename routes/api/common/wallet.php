<?php

use App\Http\Controllers\WalletController;
use Illuminate\Support\Facades\Route;

Route::apiResource('wallets', WalletController::class)->middleware('auth:sanctum');