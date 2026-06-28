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
    Route::patch('/orders/{order_id}/status', [OrderController::class, 'updateStatus']);

    Route::post('/comments', [CommentController::class, 'store']);
    Route::delete('/comments/{id}', [CommentController::class, 'destroy'])->middleware(UserComment::class);
});

Route::get('/menus', [MenuController::class, 'index']);

Route::post('/login', [AuthenticationController::class, 'login']);

Route::options('{any}', function () {
    return response()->noContent()
        ->header('Access-Control-Allow-Origin', 'http://localhost:5173')
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
})->where('any', '.*');