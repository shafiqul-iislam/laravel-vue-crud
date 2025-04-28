<?php

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/posts', [PostController::class, 'index']);
Route::post('/add-post', [PostController::class, 'add']);
Route::put('/update-post/{post}', [PostController::class, 'update']);
Route::delete('/delete-post/{id}', [PostController::class, 'delete']);
