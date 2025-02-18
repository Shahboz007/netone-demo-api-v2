<?php

use App\Http\Controllers\Statement\StatementProfitAndLostController;
use App\Http\Controllers\StatementReconciliationController;
use App\Http\Middleware\StatementMiddleware;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'statements', 'middleware' => ['auth:sanctum']], function () {
    Route::get('/profit-and-lost', [StatementProfitAndLostController::class, 'index']);
    Route::get('/reconciliations/{customerId}', [StatementReconciliationController::class, 'show']);
})->middleware(StatementMiddleware::class);
