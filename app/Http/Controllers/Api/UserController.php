<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserController extends Controller
{
    /**
     * Get All Users (Token)
     */
    public function getUsers()
    {
        try {
            $user = User::with('roles')->get();
            if ($user) :
                return response()->json([
                    'status' => true,
                    'message' => __('user.check_success'),
                    'data' => $user,
                ], 200);
            endif;
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 200);
        }
    }

    /**
     * Count Users (Token)
     */
    public function countUsers()
    {
        try {
            $user = User::with('roles')->get();
            $userCount = $user->count();
            if ($user) :
                return response()->json([
                    'status' => true,
                    'message' => __('user.check_success'),
                    'count' => $userCount,
                ], 200);
            endif;
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 200);
        }
    }

    /**
     * Get All Roles (Token)
     */
    public function getRoles()
    {
        try {
            $role = Role::all();
            if ($role) :
                return response()->json([
                    'status' => true,
                    'message' => __('user.role_success'),
                    'data' => $role,
                ], 200);
            endif;
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 200);
        }
    }

    /**
     * Get All Permissions (Token)
     */
    public function getPermissions()
    {
        try {
            $role = Permission::all();
            if ($role) :
                return response()->json([
                    'status' => true,
                    'message' => __('user.permissions_success'),
                    'data' => $role,
                ], 200);
            endif;
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 200);
        }
    }

    /**
     * Check User (Token)
     */
    public function checkUser()
    {
        try {
            $user = Auth()->user();
            if ($user) :
                return response()->json([
                    'status' => true,
                    'message' => __('user.check_success'),
                    'data' => $user,
                ], 200);
            endif;
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 200);
        }
    }

    /**
     * Check Role (Token)
     */
    public function checkRole()
    {
        try {
            $user = Auth()->user();
            $user->getRoleNames();
            if ($user) :
                return response()->json([
                    'status' => true,
                    'message' => __('user.check_success'),
                    'data' => $user,
                ], 200);
            endif;
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 200);
        }
    }

    /**
     * Check Permission (Token)
     */
    public function checkPermission()
    {
        try {
            $user = Auth()->user();
            $user->getAllPermissions();
            if ($user) :
                return response()->json([
                    'status' => true,
                    'message' => __('user.check_success'),
                    'data' => $user,
                ], 200);
            endif;
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 200);
        }
    }
}
