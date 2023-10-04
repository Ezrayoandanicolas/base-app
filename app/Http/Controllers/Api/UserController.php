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
                'status' => true,
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
                'status' => true,
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
                'status' => true,
                'message' => $th->getMessage()
            ], 200);
        }
    }
}
