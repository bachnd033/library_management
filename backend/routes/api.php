<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\UserController;

Route::post('/login', [AuthController::class, 'login']);

Route::post('/register', [AuthController::class, 'register']);

Route::middleware(['auth:sanctum'])->group(function () {
    
    // Lấy thông tin user hiện tại
    Route::get('/user', [AuthController::class, 'user']);
    
    // Quản lý người dùng (Admin)
    Route::get('/users', [UserController::class, 'index']);

    // Cập nhật vai trò người dùng (Admin)
    Route::put('/users/{id}/role', [UserController::class, 'updateRole']);

    // Xóa người dùng (Admin)
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
    
    // Đăng xuất
    Route::post('/logout', [AuthController::class, 'logout']);

    // Quản lý sách 
    Route::apiResource('books', BookController::class);

    // Cập nhật hồ sơ người dùng
    Route::put('/profile', [UserController::class, 'updateProfile']);

    // Quản lý danh sách yêu thích
    Route::get('/wishlist', [UserController::class, 'getWishlist']);

    // Thêm/Xóa yêu thích
    Route::post('/wishlist/toggle', [UserController::class, 'toggleWishlist']);

    // Quản lý mượn trả sách
    Route::post('/borrow', [LoanController::class, 'borrow']);
    Route::post('/return', [LoanController::class, 'returnBook']);
    Route::get('/my-loans', [LoanController::class, 'myLoans']);

    // Quản lý mượn trả sách (Admin)
    Route::get('/admin/loans', [LoanController::class, 'index']); 
    Route::post('/admin/loans/{id}/approve', [LoanController::class, 'approve']);
    Route::post('/admin/loans/{id}/reject', [LoanController::class, 'reject']);
    Route::put('/admin/loans/{id}/due-date', [LoanController::class, 'updateDueDate']);
});

