<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\AuthController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {
    
    // Lấy thông tin user hiện tại
    Route::get('/user', [AuthController::class, 'user']);
    
    // Đăng xuất
    Route::post('/logout', [AuthController::class, 'logout']);

    // Quản lý Sách (Chỉ user đăng nhập mới được thao tác)
    Route::apiResource('books', BookController::class);
});