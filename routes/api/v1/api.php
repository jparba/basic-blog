<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::apiResource('profile', ProfileController::class);
    Route::apiResource('post', PostController::class);
    Route::apiResource('comment', CommentController::class);
});

// public route
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);