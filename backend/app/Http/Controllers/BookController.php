<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use App\Http\Resources\BookResource; 
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;

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

        return BookResource::collection($books);
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', 
        ]);

        // Xử lý upload ảnh
        if ($request->hasFile('image')) {
            // Lưu file vào thư mục 'storage/app/public/books'
            // Hàm store trả về đường dẫn tương đối
            $path = $request->file('image')->store('books', 'public');
            $validated['image'] = $path;
        }

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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Xử lý ảnh khi cập nhật
        if ($request->hasFile('image')) {
            // Xóa ảnh cũ nếu đã tồn tại
            if ($book->image) {
                Storage::disk('public')->delete($book->image);
            }

            // Lưu ảnh mới và cập nhật đường dẫn vào mảng dữ liệu
            $path = $request->file('image')->store('books', 'public');
            $validated['image'] = $path;
        }

        $book->update($validated);

        return new BookResource($book);
    }

    /**
     * Xóa sách.
     */
    public function destroy(Book $book)
    {
        $isBorrowed = $book->loans()
                            ->whereIn('status', ['pending', 'approved']) 
                            ->exists();

        if ($isBorrowed) {
            return response()->json([
                'message' => 'Không thể xóa sách này vì đang có người mượn hoặc đang chờ duyệt.'
            ], 400); 
        }
        // Xóa ảnh khỏi storage nếu sách có ảnh
        if ($book->image) {
            Storage::disk('public')->delete($book->image);
        }

        // Xóa record trong database
        $book->delete();

        // Trả về 204 No Content
        return response()->json(null, 204);
    }
}