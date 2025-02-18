<?php

use App\Http\Controllers\Statement\ProfitAndLostController;
use App\Http\Controllers\Statement\ReconciliationCustomerController;
use App\Http\Controllers\Statement\ReconciliationSupplierController;
use App\Http\Middleware\StatementMiddleware;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'statements', 'middleware' => ['auth:sanctum']], function () {
    Route::get('/profit-and-lost', [ProfitAndLostController::class, 'index']);

    Route::group(['prefix' => '/reconciliations'], function(){
        // Route::get('/customers/', [StatementReconciliationCustomerController::class, 'index']);
        Route::get('/customers/{customerId}', [ReconciliationCustomerController::class, 'show']);
        Route::get('/suppliers/{supplierId}', [ReconciliationSupplierController::class, 'show']);
    });
})->middleware(StatementMiddleware::class);
