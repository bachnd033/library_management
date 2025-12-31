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
    
    // Đăng xuất
    Route::post('/logout', [AuthController::class, 'logout']);

    // Quản lý sách 
    Route::apiResource('books', BookController::class);

    //Mượn sách
    Route::post('/borrow', [LoanController::class, 'borrow']);

    // Xem sách đang mượn
    Route::get('/my-loans', [LoanController::class, 'myLoans']); 

    // Trả sách
    Route::post('/return', [LoanController::class, 'returnBook']); 

    // Cập nhật hồ sơ người dùng
    Route::put('/profile', [UserController::class, 'updateProfile']);

    // Quản lý danh sách yêu thích
    Route::get('/wishlist', [UserController::class, 'getWishlist']);

    // Thêm/Xóa yêu thích
    Route::post('/wishlist/toggle', [UserController::class, 'toggleWishlist']);
});

