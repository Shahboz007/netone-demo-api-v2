<?php

use App\Http\Controllers\Statement\BalanceController;
use App\Http\Controllers\Statement\ProfitAndLostController;
use App\Http\Controllers\Statement\ReconciliationCustomerController;
use App\Http\Controllers\Statement\ReconciliationSupplierController;
use App\Http\Controllers\Statement\StatementRentalPropertyController;
use App\Http\Middleware\StatementMiddleware;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'statements', 'middleware' => ['auth:sanctum']], function () {
    // Profit And Lost
    Route::get('/profit-and-lost', [ProfitAndLostController::class, 'index'])->middleware(StatementMiddleware::class);

    // Reconciliations
    Route::group(['prefix' => '/reconciliations'], function () {
        // Route::get('/customers/', [StatementReconciliationCustomerController::class, 'index']);
        Route::get('/customers/{customerId}', [ReconciliationCustomerController::class, 'show']);
        Route::get('/suppliers/{supplierId}', [ReconciliationSupplierController::class, 'show']);
    });

    // Balance
    Route::get('/balance', [BalanceController::class, 'index']);

    // Rental Property
    Route::get('/rental-property', [StatementRentalPropertyController::class, 'index']);
});
