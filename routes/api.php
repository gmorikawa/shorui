<?php

use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DocumentTypeController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::group(['prefix' => '/users'], function () {
    Route::get('/', [UserController::class, 'getAll']);
    Route::get('/{id}', [UserController::class, 'getById']);
    Route::post('/', [UserController::class, 'create']);
    Route::put('/{id}', [UserController::class, 'update']);
    Route::delete('/{id}', [UserController::class, 'delete']);
});

Route::group(['prefix' => '/documents'], function () {
    Route::get('/', [DocumentController::class, 'getAll']);
    Route::get('/{id}', [DocumentController::class, 'getById']);
    Route::post('/', [DocumentController::class, 'create']);
    Route::put('/{id}', [DocumentController::class, 'update']);
    Route::delete('/{id}', [DocumentController::class, 'delete']);
});

Route::group(['prefix' => '/document-types'], function () {
    Route::get('/', [DocumentTypeController::class, 'getAll']);
    Route::get('/{id}', [DocumentTypeController::class, 'getById']);
    Route::post('/', [DocumentTypeController::class, 'create']);
    Route::put('/{id}', [DocumentTypeController::class, 'update']);
    Route::delete('/{id}', [DocumentTypeController::class, 'delete']);
});

Route::group(['prefix' => '/files'], function () {
    Route::get('/', [FileController::class, 'getAll']);
    Route::get('/{id}', [FileController::class, 'getById']);
    Route::post('/', [FileController::class, 'create']);
    Route::put('/{id}', [FileController::class, 'update']);
    Route::delete('/{id}', [FileController::class, 'delete']);
});
