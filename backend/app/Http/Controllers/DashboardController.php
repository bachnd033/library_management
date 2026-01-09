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
            'active_loans' => Loan::where('status', 'approved')->count(), // Đang mượn
            'overdue_loans' => Loan::where('status', 'approved')->where('due_date', '<', now())->count(), // Quá hạn
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

    public function getUserStats(Request $request) {
        $userId = $request->user()->id;
        $now = now();

        $libraryStats = [
            'borrowing' => Loan::where('user_id', $userId)->where('status', 'approved')->count(), // Đang mượn
            'returned'  => Loan::where('user_id', $userId)->where('status', 'returned')->count(), // Đã trả
            'overdue'   => Loan::where('user_id', $userId)->where('status', 'approved')->where('due_date', '<', now())->count(),  // Quá hạn (Cần cảnh báo)
        ];

        $forumStats = [
            'my_posts'    => ForumPost::where('user_id', $userId)->count(),
            'my_comments' => ForumComment::where('user_id', $userId)->count(),
            // Tổng view của tất cả bài viết do user này đăng
            'total_views_received' => ForumPost::where('user_id', $userId)->sum('views'),
        ];

        // Danh sách sách đang mượn 
        $currentLoans = Loan::with('book:id,title,author')
                            ->where('user_id', $userId)
                            ->whereIn('status', ['borrowed', 'overdue'])
                            ->orderBy('due_date', 'asc') // Sắp xếp ngày hết hạn gần nhất lên đầu
                            ->get();

        $currentLoans->transform(function($loan) use ($now) {
            if ($loan->due_date < $now) {
                $loan->status = 'overdue'; 
            }
                return $loan;
        });

        return response()->json([
            'library' => $libraryStats,
            'forum' => $forumStats,
            'current_loans' => $currentLoans
        ]);
    }
}