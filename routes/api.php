<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Middleware\AdminAccess;
use App\Http\Middleware\UserComment;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/logout', [AuthenticationController::class, 'logout']);
    Route::get('/me', [AuthenticationController::class, 'me']);

    Route::post('/menus', [MenuController::class, 'store'])->middleware(AdminAccess::class);
    Route::patch('/menus/{id}', [MenuController::class, 'update'])->middleware(AdminAccess::class);
    Route::delete('/menus/{id}', [MenuController::class, 'destroy'])->middleware(AdminAccess::class);

    Route::get('/bookmarks', [BookmarkController::class, 'index']);
    Route::post('/bookmarks/{menu_id}', [BookmarkController::class, 'store']);
    Route::delete('/bookmarks/{id}', [BookmarkController::class, 'destroy']);

    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::patch('/orders/{id}', [OrderController::class, 'update']);
    Route::delete('/orders/{id}', [OrderController::class, 'destroy']);

    Route::post('/comments', [CommentController::class, 'store']);
    Route::delete('/comments/{id}', [CommentController::class, 'destroy'])->middleware(UserComment::class);
});

Route::get('/menus', [MenuController::class, 'index']);

Route::post('/login', [AuthenticationController::class, 'login']);