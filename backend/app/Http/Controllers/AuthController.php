<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Xử lý Đăng nhập (Login)
     */
    public function login(Request $request)
    {
        // Validate dữ liệu gửi lên
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Kiểm tra thông tin đăng nhập
        if (!Auth::attempt($validated)) {
            return response()->json([
                'message' => 'Thông tin đăng nhập không chính xác (Email hoặc Mật khẩu sai).'
            ], 401); // Trả về lỗi 401 Unauthorized
        }

        // Lấy thông tin user sau khi đăng nhập thành công
        $user = User::where('email', $validated['email'])->first();

        $token = $user->createToken('auth_token')->plainTextToken;

        // Trả về JSON chứa Token và User
        return response()->json([
            'message' => 'Đăng nhập thành công',
            'access_token' => $token, 
            'token_type' => 'Bearer',
            'user' => $user
        ], 200);
    }

    /**
     * Xử lý đăng xuất
     */
    public function logout(Request $request)
    {
        // Đăng xuất khỏi Guard web
        Auth::guard('web')->logout();

        // Hủy Session hiện tại
        $request->session()->invalidate();

        // Tạo lại CSRF Token mới
        $request->session()->regenerateToken();

        // Trả về thành công (204 No Content hoặc 200 OK)
        return response()->json(['message' => 'Đăng xuất thành công']);
    }

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
        'role' => 'user',
    ]);

    // Tự động đăng nhập luôn sau khi đăng ký
    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'message' => 'Đăng ký thành công',
        'access_token' => $token,
        'token_type' => 'Bearer',
        'user' => $user
    ], 201);
}
    /**
     * Lấy thông tin User hiện tại (Me)
     */
    public function user(Request $request)
    {
        return $request->user();
    }
}