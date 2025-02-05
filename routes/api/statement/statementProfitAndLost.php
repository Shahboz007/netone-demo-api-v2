<?php


use App\Http\Controllers\Statement\StatementProfitAndLostController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'statement-profit-and-lost', 'middleware' => ['auth:sanctum']], function () {
    Route::get('/', [StatementProfitAndLostController::class, 'index']);
});
