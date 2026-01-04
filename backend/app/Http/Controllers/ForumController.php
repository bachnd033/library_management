<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ForumPost;
use App\Models\ForumCategory;
use App\Models\ForumComment;
use Illuminate\Support\Facades\Auth;

class ForumController extends Controller
{
    // Public API (Không cần đăng nhập)

    // Lấy danh sách chuyên mục
    public function getCategories() {
        return response()->json(ForumCategory::all());
    }

    // Lấy danh sách bài viết 
    public function getPosts(Request $request) {
        $query = ForumPost::with(['user:id,name', 'category:id,name']); // Kèm thông tin người đăng và chuyên mục

        // Lọc theo chuyên mục 
        if ($request->has('category_id') && $request->category_id != '') {
            $query->where('forum_category_id', $request->category_id);
        }

        // Tìm kiếm theo tiêu đề
        if ($request->has('search') && $request->search != '') {
            $query->where('title', 'LIKE', '%' . $request->search . '%');
        }

        // Bài ghim lên đầu, sau đó đến bài mới nhất
        $posts = $query->orderBy('is_pinned', 'desc')
                       ->orderBy('created_at', 'desc')
                       ->paginate(10); 

        return response()->json($posts);
    }

    // Xem chi tiết bài viết 
    public function getPostDetail($id) {
        $post = ForumPost::with(['user:id,name', 'category', 'comments.user:id,name'])
                         ->findOrFail($id);
        
        // Tăng lượt xem lên 1
        $post->increment('views');

        return response()->json($post);
    }

    // Private API (Phải đăng nhập)

    // Tạo bài viết mới
    public function createPost(Request $request) {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'forum_category_id' => 'required|exists:forum_categories,id',
        ]);

        $post = ForumPost::create([
            'user_id' => Auth::id(), // Lấy ID người đang đăng nhập
            'forum_category_id' => $validated['forum_category_id'],
            'title' => $validated['title'],
            'content' => $validated['content'],
        ]);

        return response()->json(['message' => 'Đăng bài thành công', 'post' => $post]);
    }

    // Gửi bình luận
    public function createComment(Request $request, $id) {
        $request->validate(['content' => 'required|string']);

        // Kiểm tra bài viết có tồn tại không
        $post = ForumPost::findOrFail($id);

        $comment = ForumComment::create([
            'user_id' => Auth::id(),
            'forum_post_id' => $post->id,
            'content' => $request->content,
        ]);
        
        // Load lại thông tin user để hiển thị ngay lập tức ở frontend
        $comment->load('user:id,name');

        return response()->json(['message' => 'Bình luận thành công', 'comment' => $comment]);
    }

    // Xóa bài viết 
    public function deletePost(Request $request, $id) {
        $post = ForumPost::findOrFail($id);
        $user = Auth::user();

        // Phải là chủ bài viết hoặc là Admin
        if ($post->user_id !== $user->id && $user->role !== 'admin') {
            return response()->json(['message' => 'Bạn không có quyền xóa bài này'], 403);
        }

        $post->delete();
        return response()->json(['message' => 'Đã xóa bài viết']);
    }

    // Admin 

    // Thêm chuyên mục mới
    public function createCategory(Request $request) {
        if ($request->user()->role !== 'admin') return response()->json(['message' => 'Forbidden'], 403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:forum_categories,slug',
            'description' => 'nullable|string'
        ]);

        $category = ForumCategory::create($validated);
        return response()->json(['message' => 'Tạo chuyên mục thành công', 'category' => $category]);
    }

    // Xóa chuyên mục
    public function deleteCategory(Request $request, $id) {
        if ($request->user()->role !== 'admin') return response()->json(['message' => 'Forbidden'], 403);

        $category = ForumCategory::findOrFail($id);
        $category->delete();
        
        return response()->json(['message' => 'Đã xóa chuyên mục']);
    }

    // Xóa bình luận 
    public function deleteComment(Request $request, $id) {
        $comment = ForumComment::findOrFail($id);
        
        // Chỉ Admin hoặc chính chủ mới được xóa
        if ($request->user()->role !== 'admin' && $request->user()->id !== $comment->user_id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $comment->delete();
        return response()->json(['message' => 'Đã xóa bình luận']);
    }

    // Lấy danh sách bài viết của chính mình
    public function getMyPosts(Request $request) {
        $user = $request->user();

        $posts = ForumPost::where('user_id', $user->id)
                          ->with(['category']) 
                          ->withCount('comments')
                          ->orderBy('created_at', 'desc')
                          ->paginate(10);

        return response()->json($posts);
    }

    // Ghim/Bỏ ghim bài viết (Admin)
    public function togglePin(Request $request, $id) {
        // Check quyền Admin
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $post = ForumPost::findOrFail($id);
        
        // Đảo ngược trạng thái 
        $post->is_pinned = !$post->is_pinned;
        $post->save();

        return response()->json([
            'message' => $post->is_pinned ? 'Đã ghim bài viết' : 'Đã bỏ ghim bài viết',
            'is_pinned' => $post->is_pinned
        ]);
    }
}