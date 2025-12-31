<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use App\Http\Resources\BookResource; 
use Illuminate\Validation\ValidationException;

class BookController extends Controller
{
    /**
     * Áp dụng middleware cho Controller.
     */
    public function __construct()
    {
        // Yêu cầu xác thực cho tất cả các phương thức
        $this->middleware('auth:sanctum');
    }

    /**
     * Lấy danh sách (phân trang) tất cả sách.
     */
    public function index(Request $request)
    {
       // Tạo Query Builder
        $query = Book::query();

        if ($request->filled('search')) {
            $keyword = $request->search;
            
            $query->where(function($q) use ($keyword) {
                $q->where('title', 'like', '%' . $keyword . '%')
                ->orWhere('author', 'like', '%' . $keyword . '%');
            });
        }

        $books = $query->latest()->paginate(10);

        return response()->json($books);
    }

    /**
     * Tạo sách mới.
     */
    public function store(Request $request)
    {
        // Validate dữ liệu đầu vào
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'publication_year' => 'nullable|integer|max:' . date('Y'),
            'description' => 'nullable|string',
            'total_copies' => 'required|integer|min:1',
        ]);

        // Khi tạo sách, số lượng có sẵn = tổng số sách
        $validated['available_copies'] = $validated['total_copies'];

        $book = Book::create($validated);

        // Trả về sách vừa tạo 
        return (new BookResource($book))
                ->response()
                ->setStatusCode(201);
    }

    /**
     * Hiển thị chi tiết một sách.
     */
    public function show(Book $book)
    {
        return new BookResource($book);
    }

    /**
     * Cập nhật thông tin sách.
     */
    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'author' => 'sometimes|string|max:255',
            'category' => 'sometimes|string|max:100',
            'publication_year' => 'nullable|integer',
            'description' => 'nullable|string',
            'total_copies' => 'sometimes|integer|min:0',
        ]);


        $book->update($validated);

        return new BookResource($book);
    }

    /**
     * Xóa sách.
     */
    public function destroy(Book $book)
    {

        $book->delete();

        // Trả về 204 No Content (Xóa thành công, không cần trả về nội dung)
        return response()->json(null, 204);
    }
}