<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DocumentTypeController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => '/auth'], function () {
    Route::post('/first-access', [AuthController::class, 'registerAdmin']);
    Route::post('/register', [AuthController::class, 'registerUser']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});

Route::group(['prefix' => '/users'], function () {
    Route::get('/', [UserController::class, 'getAll'])->middleware('auth:sanctum');
    Route::get('/{id}', [UserController::class, 'getById'])->middleware('auth:sanctum');
    Route::post('/', [UserController::class, 'create'])->middleware('auth:sanctum');
    Route::put('/{id}', [UserController::class, 'update'])->middleware('auth:sanctum');
    Route::delete('/{id}', [UserController::class, 'delete'])->middleware('auth:sanctum');
});

Route::group(['prefix' => '/documents'], function () {
    Route::get('/', [DocumentController::class, 'getAll']);
    Route::get('/{id}', [DocumentController::class, 'getById']);
    Route::post('/', [DocumentController::class, 'create']);
    Route::put('/{id}', [DocumentController::class, 'update']);
    Route::delete('/{id}', [DocumentController::class, 'delete']);
})->middleware('auth:sanctum');

Route::group(['prefix' => '/document-types'], function () {
    Route::get('/', [DocumentTypeController::class, 'getAll']);
    Route::get('/{id}', [DocumentTypeController::class, 'getById']);
    Route::post('/', [DocumentTypeController::class, 'create']);
    Route::put('/{id}', [DocumentTypeController::class, 'update']);
    Route::delete('/{id}', [DocumentTypeController::class, 'delete']);
})->middleware('auth:sanctum');

Route::group(['prefix' => '/files'], function () {
    Route::get('/', [FileController::class, 'getAll']);
    Route::get('/{id}', [FileController::class, 'getById']);
    Route::post('/', [FileController::class, 'create']);
    Route::put('/{id}', [FileController::class, 'update']);
    Route::delete('/{id}', [FileController::class, 'delete']);
})->middleware('auth:sanctum');
