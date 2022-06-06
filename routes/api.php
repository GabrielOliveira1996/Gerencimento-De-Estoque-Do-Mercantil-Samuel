<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//Route::post('/auth', [App\Http\Controllers\ApiAuthController::class, 'login']);

Route::prefix('/produtos')->group(function(){
    Route::get('/em-estoque', [App\Http\Controllers\ApiController::class, 'getAllProducts']);

    Route::get('/editar/{id}', [App\Http\Controllers\ApiController::class, 'getProduct']);

    Route::put('/editar/{id}', [App\Http\Controllers\ApiController::class, 'updateProduct']);

    Route::post('/adicionar', [App\Http\Controllers\ApiController::class, 'addProduct']);

    Route::delete('/apagar/{id}', [App\Http\Controllers\ApiController::class, 'deleteProduct']);
});




