<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\User;

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
    // --- PHẦN ADMIN ---

    public function index(Request $request) 
    {
        // Check quyền Admin
        if (!$request->user() || $request->user()->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $query = User::query();

        // Tìm kiếm
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        // Sắp xếp & Phân trang
        $users = $query->orderBy('created_at', 'desc')->paginate(10);
        
        return response()->json($users);
    }

    public function updateRole(Request $request, $id) 
    {
        if (!$request->user() || $request->user()->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $request->validate(['role' => 'required|in:admin,user']);
        $user = User::findOrFail($id);

        if ($user->id === $request->user()->id) {
            return response()->json(['message' => 'Không thể đổi quyền chính mình'], 403);
        }

        $user->role = $request->role;
        $user->save();

        return response()->json(['message' => 'Cập nhật thành công']);
    }

    public function destroy(Request $request, $id) 
    {
        if (!$request->user() || $request->user()->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $user = User::findOrFail($id);

        if ($user->id === $request->user()->id) {
            return response()->json(['message' => 'Không thể xóa chính mình'], 403);
        }

        $user->delete();
        return response()->json(['message' => 'Đã xóa user']);
    }
}