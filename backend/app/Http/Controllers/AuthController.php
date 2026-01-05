<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Xử lý Đăng nhập 
     */
    public function login(Request $request)
    {
        // Validate dữ liệu gửi lên
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($validated)) {
            
            $request->session()->regenerate(); 

            return response()->json([
                'message' => 'Đăng nhập thành công',
                'user' => Auth::user()
            ], 200);
        }

        // Đăng nhập thất bại
        return response()->json([
            'message' => 'Email hoặc mật khẩu không chính xác'
        ], 401);
    }

    /**
     * Xử lý Đăng ký 
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed', 
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'user', // Mặc định là user thường
        ]);

        // Tự động login ngay sau khi đăng ký
        Auth::login($user);

        return response()->json([
            'message' => 'Đăng ký thành công',
            'user' => $user
        ], 201);
    }

    /**
     * Xử lý Đăng xuất 
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Đăng xuất thành công']);
    }

    /**
     * Lấy thông tin User hiện tại
     */
    public function user(Request $request)
    {
        return $request->user();
    }
}