<?php

use App\Http\Controllers\Finance\UserWalletController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix'=> 'user-wallets', 'middleware'=> ['auth:sanctum']], function (){
    Route::get('/', [UserWalletController::class, 'index']);
    Route::get('{userWallet}', [UserWalletController::class, 'show']);
    Route::post('/', [UserWalletController::class, 'store']);
});
