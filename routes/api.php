<?php

use App\Http\Controllers\Api\AuthController;
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
// Route::get('/testPermission/{id}', function($id) {
//     $user = User::find($id);
//     $getPermission = Permission::all();
//     $user->syncPermissions($getPermission);
//     $permissions = $user->getAllPermissions(); // Mendapatkan semua izin pengguna
//     return response()->json(['permissions' => $permissions]);
// });

// Route::post('/auth/createUser', [AuthController::class, 'create']);

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::prefix('v1')->group(function () {
    // Group Guest
    Route::prefix('guest')->group(function () {
        Route::post('signIn', [AuthController::class, 'signIn']);
        Route::post('createUser', [AuthController::class, 'createMember']);
    });

    // Group Middleware Sanctum
    Route::middleware('auth:sanctum')->group(function () {
        // Prefix V1
        Route::prefix('auth')->group(function () {
            Route::post('createUser', [AuthController::class, 'createCustomUser']);
        });
    });
});
