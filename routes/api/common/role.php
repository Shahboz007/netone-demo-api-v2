<?php

use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'roles', 'middleware' =>['auth:sanctum']], function (){
   Route::get('/', [RoleController::class, 'index']);
   Route::get('{role}', [RoleController::class, 'show']);
});
