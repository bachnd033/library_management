<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Book;

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
        $books = $request->user()->wishlist;
        return response()->json($books);
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