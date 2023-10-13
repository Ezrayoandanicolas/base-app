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

Route::get('/testPermission/{id}', function($id) {
    $user = User::find($id);
    // foreach ($user->getAllPermissions() as $permission) {
    //     $user->revokePermissionTo($permission);
    // }
    // $user->givePermissionTo(['edit-post']);
    // $user->syncPermissions([]);
    // $getPermission = Permission::all();
    // $user->syncPermissions($getPermission);
    $permissions = $user->getAllPermissions(); // Mendapatkan semua izin pengguna
    return response()->json(['permissions' => $permissions]);
});

Route::prefix('v1')->group(function () {
    // Group Guest
    Route::prefix('guest')->group(function () {
        // Auth
        Route::post('signIn', [AuthController::class, 'signIn']);
        Route::post('createUser', [AuthController::class, 'createMember']);

        // Article
        Route::get('getArticle', [ArticleController::class, 'index']);
        Route::get('getArticle/{slug}', [ArticleController::class, 'readArticle']);
    });

    // Group Middleware Sanctum
    Route::middleware('auth:sanctum')->group(function () {
        // Prefix V1
        Route::prefix('auth')->group(function () {
            // Logout
            Route::post('logout', [AuthController::class, 'logout']);

            // Count
            Route::get('countUsers', [UserController::class, 'countUsers']);
            Route::get('countArticles', [ArticleController::class, 'countArticles']);

            // Get All Users
            Route::get('getUsers', [UserController::class, 'getUsers']);
            Route::get('getRoles', [UserController::class, 'getRoles']);
            Route::get('getPermissions', [UserController::class, 'getPermissions']);

            // Check User
            Route::get('checkUser', [UserController::class, 'checkUser']);
            Route::get('checkRole', [UserController::class, 'checkRole']);
            Route::get('checkPermission', [UserController::class, 'checkPermission']);

            // Create New User Custom
            Route::post('createUser', [AuthController::class, 'createCustomUser']);
            Route::post('updateUser/{id}', [AuthController::class, 'updateUser']);
            Route::delete('deleteUser/{id}', [AuthController::class, 'destroy']);

            // Post Article
            Route::post('createArticle', [ArticleController::class, 'store'])->middleware('permission:create-post');
            Route::post('updateArticle/{slug}', [ArticleController::class, 'update'])->middleware('permission:edit-post');
            Route::delete('deleteArticle/{slug}', [ArticleController::class, 'destroy'])->middleware('permission:delete-post');
        });
    });
});
