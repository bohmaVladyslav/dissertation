<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::controller(AuthController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', 'logout');
    });

    });
    
Route::prefix('/user')->middleware('auth:sanctum')->group(function () {
    
});


Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('user', UserController::class);
});

