<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function updateProfile(Request $request) {
        $user = $request->user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'nullable|string|min:6|confirmed', 
        ]);

        $user->name = $validated['name'];
        
        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();
        return response()->json(['message' => 'Cập nhật thành công', 'user' => $user]);
    }

    // Lấy danh sách yêu thích
    public function getWishlist(Request $request) {

        try {
            // Lấy user đang đăng nhập
            $user = $request->user();

            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $wishlistBooks = $user->wishlist()->get();

            return response()->json([
                'status' => 'success',
                'data' => $wishlistBooks
            ], 200);

        } catch (\Exception $e) {
            Log::error('Lỗi : ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Không thể lấy danh sách yêu thích. Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    // Thêm/Xóa yêu thích 
    public function toggleWishlist(Request $request) {
        $request->validate(['book_id' => 'required|exists:books,id']);
        $user = $request->user();
        $bookId = $request->book_id;

        // Nếu có rồi thì xóa, chưa có thì thêm
        $attached = $user->wishlist()->toggle($bookId);

        if (count($attached['attached']) > 0) {
            return response()->json(['message' => 'Đã thêm vào yêu thích', 'status' => 'added']);
        } else {
            return response()->json(['message' => 'Đã bỏ yêu thích', 'status' => 'removed']);
        }
    }
}