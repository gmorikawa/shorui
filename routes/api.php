<?php

use App\Http\Controllers\AttributeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DocumentTypeController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => '/auth'], function () {
    Route::get('/me', [AuthController::class, 'me'])->middleware('auth:sanctum');
    Route::post('/first-access', [AuthController::class, 'registerAdmin']);
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

Route::group(['prefix' => '/folders', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [FolderController::class, 'getByParent']);
    Route::post('/', [FolderController::class, 'create']);
});

Route::group(['prefix' => '/documents', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [DocumentController::class, 'getAll']);
    Route::get('/{id}', [DocumentController::class, 'getById']);
    Route::get('/folder/{folderId}', [DocumentController::class, 'getByFolder']);
    Route::post('/', [DocumentController::class, 'create']);
    Route::put('/{id}', [DocumentController::class, 'update']);
    Route::delete('/{id}', [DocumentController::class, 'delete']);
});

Route::group(['prefix' => '/document-types', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [DocumentTypeController::class, 'getAll']);
    Route::get('/{id}', [DocumentTypeController::class, 'getById']);
    Route::post('/', [DocumentTypeController::class, 'create']);
    Route::put('/{id}', [DocumentTypeController::class, 'update']);
    Route::delete('/{id}', [DocumentTypeController::class, 'delete']);
});

Route::group(['prefix' => '/files', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/{id}/download', [FileController::class, 'download']);
    Route::post('/upload', [FileController::class, 'upload']);
    Route::delete('/{id}', [FileController::class, 'delete']);
});

Route::group(['prefix' => '/attributes', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [AttributeController::class, 'getAll']);
    Route::get('/{key}', [AttributeController::class, 'getById']);
    Route::post('/', [AttributeController::class, 'create']);
    Route::put('/{key}', [AttributeController::class, 'update']);
    Route::delete('/{key}', [AttributeController::class, 'delete']);
});
