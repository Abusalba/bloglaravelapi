<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::post('/login', [App\Http\Controllers\API\AuthController::class, 'login']);
Route::post('/register', [App\Http\Controllers\API\AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::resource('/posts', PostController::class);
});

