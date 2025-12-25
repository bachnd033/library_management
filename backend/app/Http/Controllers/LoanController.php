<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Loan; 
use Illuminate\Support\Facades\DB;

class LoanController extends Controller
{
    public function borrow(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
            // 'due_date' => 'required|date|after:today' 
        ]);

        $book = Book::find($request->book_id);

        // Kiểm tra xem còn sách để mượn không
        if ($book->available_copies < 1) {
            return response()->json(['message' => 'Sách này đã hết hàng.'], 400);
        }

        // Kiểm tra xem user này có đang mượn cuốn này mà chưa trả không 
        $existingLoan = Loan::where('user_id', $request->user()->id)
                            ->where('book_id', $book->id)
                            ->whereNull('returned_at')
                            ->first();
        if ($existingLoan) {
             return response()->json(['message' => 'Bạn đang mượn sách này rồi.'], 400);
        }

        // Thực hiện giao dịch (Transaction) để đảm bảo an toàn dữ liệu
        DB::beginTransaction();
        try {
            // Tạo phiếu mượn
            Loan::create([
                'user_id' => $request->user()->id, // Lấy ID user đang đăng nhập
                'book_id' => $book->id,
                'borrowed_at' => now(),
                'due_date' => now()->addDays(14), // Mặc định cho mượn 14 ngày
            ]);

            // Trừ số lượng sách trong kho
            $book->decrement('available_copies');

            DB::commit();

            return response()->json(['message' => 'Mượn sách thành công!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Lỗi hệ thống, vui lòng thử lại.'], 500);
        }
    }

    // Xem sách đang mượn
    public function myLoans(Request $request)
    {
        // Lấy các phiếu mượn của user hiện tại mà chưa trả (returned_at là null)
        $loans = Loan::with('book') // Load kèm thông tin sách
            ->where('user_id', $request->user()->id)
            ->whereNull('returned_at')
            ->get();

        return response()->json([
            'data' => $loans
        ]);
    }

    // Trả sách
    public function returnBook(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
        ]);

        // Tìm phiếu mượn đang hoạt động (chưa trả)
        $loan = Loan::where('user_id', $request->user()->id)
            ->where('book_id', $request->book_id)
            ->whereNull('returned_at')
            ->first();

        if (!$loan) {
            return response()->json(['message' => 'Bạn không mượn cuốn sách này hoặc đã trả rồi.'], 400);
        }

        DB::beginTransaction();
        try {
            // Cập nhật ngày trả
            $loan->update([
                'returned_at' => now()
            ]);

            // Cộng lại số lượng sách vào kho
            $book = Book::find($request->book_id);
            $book->increment('available_copies');

            DB::commit();

            return response()->json(['message' => 'Trả sách thành công!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Lỗi hệ thống khi trả sách.'], 500);
        }
    }
}