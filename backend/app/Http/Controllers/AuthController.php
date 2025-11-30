<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Xử lý Đăng nhập (Login)
     */
    public function login(Request $request)
    {
        // Validate dữ liệu gửi lên
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Đăng nhập bằng Auth::attempt
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            
            // Đăng nhập thành công -> Tạo lại Session ID
            $request->session()->regenerate();

            // Trả về thông tin User và status 200
            return response()->json([
                'message' => 'Đăng nhập thành công!',
                'user' => Auth::user(),
            ]);
        }

        throw ValidationException::withMessages([
            'email' => ['Thông tin đăng nhập không chính xác.'],
        ]);
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

    /**
     * Lấy thông tin User hiện tại (Me)
     * Dùng để Frontend kiểm tra xem F5 lại trang thì còn đăng nhập không
     */
    public function user(Request $request)
    {
        return $request->user();
    }
}