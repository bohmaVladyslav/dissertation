<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BooksArchiveController;
use App\Http\Controllers\CollectionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/user', 301);
})->name('index');

Route::get('/dashboard', function () {
    return redirect('/user', 301);
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

Route::middleware('auth:sanctum')->group(function() {
    Route::get('/books/archive', [BooksArchiveController::class, 'create'])->name('books.archive.create');
    Route::post('/books/archive', [BooksArchiveController::class, 'store'])->name('books.archive.store');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/books/meta', [BookController::class, 'meta']);
    Route::post('/books/{book}/progress', [BookController::class, 'saveProgress']);
    Route::get('/books/{book}/read', [BookController::class, 'read'])->name('books.read');
    Route::Resource('books', BookController::class);
    
    Route::apiResource('user', UserController::class);
    
    Route::delete('/collections/{collection}/all', [CollectionController::class, 'destroyAll'])->name('collections.destroyAll');
    Route::delete('/collection/{collection}/books/{book}', [CollectionController::class, 'deleteBook'])->name('collection.deleteBook');
    Route::get('/collections/{collection}/download', [CollectionController::class, 'download'])->name('collections.download');
    Route::Resource('collections', CollectionController::class);
});
