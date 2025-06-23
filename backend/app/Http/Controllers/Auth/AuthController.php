<?php

namespace App\Http\Controllers\Auth;

use App\Enums\RoleEnum;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;



class AuthController extends Controller
{

    public function register(RegisterRequest $request)
    {
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'role' => RoleEnum::from($request->role),
            ]);

            $token = $user->createToken('auth-token')->plainTextToken;

            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'message' => 'Đăng ký thành công!',
                'user' => $user
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Đăng ký thất bại!'
            ], 500);
        }
    }


    public function login(LoginRequest $request)
    {
        try {
            if (!Auth::attempt($request->only('email', 'password'))) {
                return response()->json([
                    'message' => 'Sai tài khoản hoặc mật khẩu!'
                ], 500);
            }
            // $request->session()->regenerate();
            $user = Auth::user();
            $token = $user->createToken('auth-token')->plainTextToken;

            return response()->json([
                'message' => 'Đăng nhập thành công!',
                'token' => $token,
                'user' => $user,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Đăng nhập thất bại!'
            ], 500);
        }
    }


    public function logout(Request $request)
    {
        try{
            if (!$request->user()) {
                return response()->json(['message' => 'Người dùng chưa đăng nhập hoặc token không hợp lệ!'], 401);
            }
            // Auth::guard('web')->logout();
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'message' => 'Đăng xuất thành công!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Đăng xuất thất bại!'
            ], 500);
        }
    }

    public function getProfile(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            return response()->json([
                'user' => $user
            ], 200);
        } else {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $user->update($request->only('name', 'email', 'phone'));
        return response()->json([
            'user' => $user,
        ], 200);
    }

    public function changePassword(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        if (!Hash::check($request->currentPassword, $user->password)) {
            return response()->json(['message' => 'Sai mật khẩu hiện tại!'], 400);
        }
        $user->update($request->only('newPassword'));
        return response()->json([
            'user' => $user,
        ], 200);
    }

    public function rentalHistory(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 400);
        }
        return response()->json($user->rentals()->with('books')->get());
    }
}
