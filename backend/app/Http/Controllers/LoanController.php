<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoanController extends Controller
{
    //User tạo yêu cầu mượn
    public function borrow(Request $request)
    {
        $request->validate(['book_id' => 'required|exists:books,id']);

        // Kiểm tra xem user có đang mượn cuốn này không
        $exists = Loan::where('user_id', $request->user()->id)
                      ->where('book_id', $request->book_id)
                      ->whereIn('status', ['pending', 'approved'])
                      ->exists();

        if ($exists) {
            return response()->json(['message' => 'Bạn đã gửi yêu cầu hoặc đang mượn sách này rồi.'], 400);
        }

        Loan::create([
            'user_id' => $request->user()->id,
            'book_id' => $request->book_id,
            'status' => 'pending',
            'loan_date' => now(),
            'due_date' => now()->addDays(14), // Mặc định 14 ngày
        ]);

        return response()->json(['message' => 'Đã gửi yêu cầu mượn sách. Vui lòng chờ Admin duyệt.']);
    }

    // User trả sách 
    public function returnBook(Request $request)
    {
        $request->validate(['book_id' => 'required']);

        $loan = Loan::where('user_id', $request->user()->id)
                    ->where('book_id', $request->book_id)
                    ->where('status', 'approved') // Chỉ trả được sách đang mượn
                    ->first();

        if (!$loan) {
            return response()->json(['message' => 'Không tìm thấy phiếu mượn hợp lệ.'], 404);
        }

        DB::transaction(function () use ($loan) {
            // Cập nhật trạng thái
            $loan->update([
                'status' => 'returned',
                'returned_at' => now()
            ]);

            $loan->refresh();

            // Cộng lại số lượng tồn kho
            $loan->book()->increment('available_copies');
        });

        return response()->json([
            'message' => 'Trả sách thành công!',
            'loan' => $loan 
        ]);
    }

    // ADMIN 

    // Lấy tất cả danh sách mượn 
    public function index(Request $request)
    {
        // Cho phép lọc theo status 
        $query = Loan::with(['user', 'book']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return response()->json($query->latest()->get());
    }

    // Admin duyệt phiếu mượn
    public function approve($id)
    {
        $loan = Loan::findOrFail($id);

        if ($loan->status !== 'pending') {
            return response()->json(['message' => 'Phiếu này không ở trạng thái chờ duyệt.'], 400);
        }

        // Kiểm tra tồn kho trước khi duyệt
        if ($loan->book->available_copies < 1) {
            return response()->json(['message' => 'Sách này đã hết hàng, không thể duyệt.'], 400);
        }

        DB::transaction(function () use ($loan) {
            $loan->update(['status' => 'approved']);
            $loan->book()->decrement('available_copies');
        });

        return response()->json(['message' => 'Đã duyệt phiếu mượn.']);
    }

    // Admin từ chối phiếu mượn
    public function reject($id)
    {
        $loan = Loan::findOrFail($id);
        if ($loan->status !== 'pending') return response()->json(['message' => 'Sai trạng thái.'], 400);
        
        $loan->update(['status' => 'rejected']);
        return response()->json(['message' => 'Đã từ chối phiếu mượn.']);
    }

    // Admin cập nhật ngày trả
    public function updateDueDate(Request $request, $id)
    {
        $request->validate(['due_date' => 'required|date']);
        
        $loan = Loan::findOrFail($id);
        $loan->update(['due_date' => $request->due_date]);

        return response()->json(['message' => 'Đã cập nhật ngày trả.']);
    }
    
    // Lấy danh sách cá nhân user
    public function myLoans(Request $request)
    {
        // Lấy tất cả trạng thái để hiển thị lịch sử
        $loans = Loan::with('book')
                     ->where('user_id', $request->user()->id)
                     ->latest()
                     ->get();
        return response()->json(['data' => $loans]);
    }
}