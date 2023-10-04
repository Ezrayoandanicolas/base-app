<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    /**
     * Create Member User
     */
    public function createMember(Request $request)
    {
        try {
            // Cek apakah ada data yang belum terisi
            $validate = Validator::make($request->all(),
            [
                'username' => 'required|unique:users,username',
                'name' => 'required',
                'password' => 'required',
                'email' => 'email|unique:users,email'
            ]);

            if($validate->fails()){
                return response()->json([
                    'status'=>false,
                    'message' => $validate->errors()
                ], 401);
            }

            // Cek tidak boleh membuat username Administrator/Admin
            $username = strtolower($request->username);
            if ($username == 'administrator' || $username == 'admin') :
                return response()->json([
                    'status' => false,
                    'message' => __('auth.administrator')
                ], 401);
            endif;

            // Membuat User jika memenuhi Syarat
            $user = User::create([
                'username' => $request->username,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            if ($user) :
                $user->assignRole('member');
                return response()->json([
                    'status' => true,
                    'message' => __('auth.create_users_success')
                ], 200);
            else :
                return response()->json([
                    'status' => false,
                    'message' => __('auth.create_users_fails')
                ], 401);
            endif;

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Create Custom User (Token)
     */
    public function createCustomUser(Request $request)
    {
        try {
            // Cek apakah ada data yang belum terisi
            $validate = Validator::make($request->all(),
            [
                'username' => 'required|unique:users,username',
                'name' => 'required',
                'password' => 'required',
                'email' => 'email|unique:users,email'
            ]);

            if($validate->fails()){
                return response()->json([
                    'status'=>false,
                    'message' => $validate->errors()
                ], 401);
            }

            // Cek tidak boleh membuat username Administrator/Admin
            $username = strtolower($request->username);
            if ($username == 'administrator' || $username == 'admin') :
                return response()->json([
                    'status' => false,
                    'message' => __('auth.administrator')
                ], 401);
            endif;

            // Membuat User jika memenuhi Syarat
            $user = User::create([
                'username' => $request->username,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            if ($user) :
                $user->assignRole('member');
                return response()->json([
                    'status' => true,
                    'message' => __('auth.create_users_success')
                ], 200);
            else :
                return response()->json([
                    'status' => false,
                    'message' => __('auth.create_users_fails')
                ], 401);
            endif;

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Request Login
     */
    public function signIn(Request $request)
    {
        try {
            $validate = Validator::make($request->all(),
            [
                'username' => 'required',
                'password' => 'required'
            ]);

            if($validate->fails()){
                return response()->json([
                    'status'=>false,
                    'message' => $validate->errors()
                ], 401);
            }

            //cek menggunakan username
            if (!Auth::attempt($request->only(['username', 'password']))){
                //cek menggunakan email
                if (!Auth::attempt($request->only(['email', 'password']))){
                    return response()->json([
                        'status' => false,
                        'message' => __('auth.signin_fails')
                    ], 401);
                } else {
                    $mode = 'email';
                }
            } else {
                $mode = 'username';
            }

            switch($mode):
                case 'username':
                    $user = User::where('username', $request->username)->first();
                    break;
                default:
                    $user = User::where('email', $request->username)->first();
            endswitch;

            return response()->json([
                'status' => true,
                'message' => $user->createToken("API TOKEN")->plainTextToken
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
