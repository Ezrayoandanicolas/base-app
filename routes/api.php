<?php

use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use Spatie\Permission\Models\Permission;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {
    // Group Guest
    Route::prefix('guest')->group(function () {
        // Auth
        Route::post('signIn', [AuthController::class, 'signIn']);
        Route::post('createUser', [AuthController::class, 'createMember']);

        // Article Umum
        Route::get('getArticle', [ArticleController::class, 'index']);
    });

    // Group Middleware Sanctum
    Route::middleware('auth:sanctum')->group(function () {
        // Prefix V1
        Route::prefix('auth')->group(function () {
            // Check User
            Route::get('checkUser', [UserController::class, 'checkUser']);
            Route::get('checkRole', [UserController::class, 'checkRole']);
            Route::get('checkPermission', [UserController::class, 'checkPermission']);

            // Create New User Custom
            Route::post('createUser', [AuthController::class, 'createCustomUser']);

            // Post Article
            Route::post('createArticle', [ArticleController::class, 'store'])->middleware('permission:create-post');
        });
    });
});
