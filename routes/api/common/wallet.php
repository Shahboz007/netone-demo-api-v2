<?php

use App\Http\Controllers\Finance\WalletController;
use Illuminate\Support\Facades\Route;

Route::apiResource('wallets', WalletController::class)->middleware('auth:sanctum');
