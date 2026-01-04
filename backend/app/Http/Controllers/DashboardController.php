<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Book;
use App\Models\Loan;
use App\Models\ForumPost;
use App\Models\ForumComment;
use App\Models\ForumCategory;

class DashboardController extends Controller
{
    public function getAdminStats() {
        $libraryStats = [
            'total_users' => User::count(),
            'total_books' => Book::count(),
            'active_loans' => Loan::where('status', 'borrowed')->count(), // Đang mượn
            'overdue_loans' => Loan::where('status', 'overdue')->count(), // Quá hạn
            'returned_loans' => Loan::where('status', 'returned')->count(), // Đã trả
        ];

        $forumStats = [
            'total_categories' => ForumCategory::count(),
            'total_posts' => ForumPost::count(),
            'total_comments' => ForumComment::count(),
            'total_views' => ForumPost::sum('views'), // Tổng lượt xem tất cả bài
            'pending_posts' => 0, // (Nếu sau này có tính năng duyệt bài thì đếm ở đây)
        ];

        $newestUsers = User::orderBy('created_at', 'desc')->take(5)->get(['id', 'name', 'email', 'created_at']);

        $recentPosts = ForumPost::with('user:id,name')
                                ->orderBy('created_at', 'desc')
                                ->take(5)
                                ->get(['id', 'title', 'user_id', 'created_at', 'views']);

        return response()->json([
            'library' => $libraryStats,
            'forum' => $forumStats,
            'newest_users' => $newestUsers,
            'recent_posts' => $recentPosts
        ]);
    }
}