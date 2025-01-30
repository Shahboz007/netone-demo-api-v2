<?php

use App\Http\Controllers\Finance\PaymentExpenseController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix'=>'payment-expenses', 'middleware'=> ['auth:sanctum']], function(){
   Route::get('/', [PaymentExpenseController::class, 'index']);
   Route::get('{paymentExpense}', [PaymentExpenseController::class, 'show']);
   Route::post('/', [PaymentExpenseController::class, 'store']);
});
