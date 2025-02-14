<?php

use App\Http\Controllers\Statement\StatementProfitAndLostController;
use App\Http\Controllers\StatementPaymentReconciliation;
use App\Http\Controllers\StatementReconciliation;
use App\Http\Middleware\StatementMiddleware;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'statements', 'middleware' => ['auth:sanctum']], function () {
    Route::get('/profit-and-lost', [StatementProfitAndLostController::class, 'index']);
    Route::get('/reconciliations', [StatementReconciliation::class, 'index']);
})->middleware(StatementMiddleware::class);
