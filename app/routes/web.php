<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CollectionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/register', function () {
    return view('register');
})->name('register.page');

Route::get('/login', function () {
    return view('login');
})->name('login.page');

Route::controller(AuthController::class)->group(function () {
    Route::post('/register', 'register')->name('register');
    Route::post('/login', 'login')->name('login');

    Route::middleware('auth:sanctum')->group(function () {
        Route::any('/logout', 'logout')->name('logout');
    });

});

Route::post('/books/epub-meta', [BookController::class, 'epubMeta']);
Route::get('/books/{book}/read', [BookController::class, 'read'])->name('books.read');
Route::post('/books/{book}/progress', [BookController::class, 'saveProgress']);

Route::middleware('auth:sanctum')->group(function () {
    Route::Resource('books', BookController::class);
    Route::apiResource('user', UserController::class);
    Route::Resource('collections', CollectionController::class);
});
