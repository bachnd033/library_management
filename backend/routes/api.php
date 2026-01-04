<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\DashboardController;

Route::post('/login', [AuthController::class, 'login']);

Route::post('/register', [AuthController::class, 'register']);

Route::get('/forum/categories', [ForumController::class, 'getCategories']);

Route::get('/forum/posts', [ForumController::class, 'getPosts']);

Route::get('/forum/posts/{id}', [ForumController::class, 'getPostDetail']);

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

    // Đăng bài viết mới
    Route::post('/forum/posts', [ForumController::class, 'createPost']);
    
    // Gửi bình luận cho bài viết ID
    Route::post('/forum/posts/{id}/comments', [ForumController::class, 'createComment']);
    
    // Xóa bài viết
    Route::delete('/forum/posts/{id}', [ForumController::class, 'deletePost']);

    // Quản lý chuyên mục (Admin)
    Route::post('/forum/categories', [ForumController::class, 'createCategory']);
    Route::delete('/forum/categories/{id}', [ForumController::class, 'deleteCategory']);

    // Xóa bình luận
    Route::delete('/forum/comments/{id}', [ForumController::class, 'deleteComment']);

    // Lấy danh sách bài viết của user hiện tại
    Route::get('/forum/my-posts', [ForumController::class, 'getMyPosts']);

    // Ghim/Bỏ ghim bài viết (Admin)
    Route::put('/forum/posts/{id}/pin', [ForumController::class, 'togglePin']);

    // Lấy bài viết nổi bật
    Route::get('/forum/featured', [ForumController::class, 'getFeaturedPosts']);

    // Thống kê Dashboard Admin
    Route::get('/admin/dashboard', [DashboardController::class, 'getAdminStats']);
});



