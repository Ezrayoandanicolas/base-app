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

            if (!empty($request->roles)) {
                $user->syncRoles($request->roles);
            }

            foreach ($user->getAllPermissions() as $permission) {
                $user->revokePermissionTo($permission);
            }

            // Berikan izin (permissions) jika ada
            if (!empty($request->permissions)) {
                $user->syncPermissions($request->permissions);
            }

            if ($user) :
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
     * Update User & Permission
     */
    public function updateUser(Request $request, $id, $permissions = [], $roles = [])
    {
        try {
            // Ambil user yang akan diperbarui
            $user = User::find($id);

            if (!$user) :
                return response()->json([
                    'status' => false,
                    'message' => __('auth.user_not_found')
                ], 404);
            endif;

             // Check apakah ada pergantian Password
            if (!empty($request->password)) {
                $request['password'] = Hash::make($request->password);
            }

            // Perbarui informasi pengguna
            $user->update($request->all());

            // return response()->json($user, 200);
            // Berikan peran (roles) jika ada
            if (!empty($request->roles)) {
                $user->syncRoles($request->roles);
            }

            foreach ($user->getAllPermissions() as $permission) {
                $user->revokePermissionTo($permission);
            }

            // Berikan izin (permissions) jika ada
            if (!empty($request->permissions)) {
                $user->syncPermissions($request->permissions);
            }


            return response()->json([
                'status' => true,
                'message' => __('auth.update_user_success'),
                'data' => $user
            ], 200);

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
                'message' => __('auth.signin_success'),
                'token' => $user->createToken("API TOKEN")->plainTextToken
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Check Token
     */
    public function checkToken(Request $request)
    {
        try {
            if (Auth::check()) {
                return response()->json([
                    'status' => true,
                    'message' => __('auth.check_token_success'),
                ], 200);
            }

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Destory Users
     */

     public function destroy($id) {
        try {
            // Find the user by ID
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => __('auth.user_not_found')
                ], 404);
            }

            // Delete user's roles
            $user->roles()->detach();

            // Delete user's permissions
            $user->permissions()->detach();

            // Delete the user
            $user->delete();

            return response()->json([
                'status' => true,
                'message' => __('auth.delete_user_success'),
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
     }

    /**
     * Logout the user and revoke the token.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        try {
            // Revoke the user's current token
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'status' => true,
                'message' => __('auth.signout_success'),
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
