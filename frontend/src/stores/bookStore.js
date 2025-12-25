import { defineStore } from 'pinia';
import { BookService } from '@/services/BookService';

export const useBookStore = defineStore('bookStore', {
    state: () => ({
        books: [],
        currentBook: null,
        borrowedBooks: [],
        pagination: {},
        isLoading: false,
        error: null,
    }),

    actions: {
        async fetchBooks(page = 1) { 
            this.isLoading = true;
            this.error = null;
            try {
                // Gọi service
                const response = await BookService.getAllBooks({ page });
                
                this.books = response.data.data; 
                
            } catch (err) {
                this.error = 'Không thể tải danh sách sách.';
                console.error(err);
            } finally {
                this.isLoading = false;
            }
        },

        async deleteBook(id) {
            this.isLoading = true;
            try {
                await BookService.deleteBook(id);
                // Xóa khỏi danh sách hiện tại để không cần load lại trang
                this.books = this.books.filter(book => book.id !== id);
                return true;
            } catch (err) {
                this.error = 'Không thể xóa sách.';
                console.error(err);
                return false;
            } finally {
                this.isLoading = false;
            }
        },

        async fetchBook(id) {
            this.isLoading = true;
            this.error = null;
            this.currentBook = null;
            try {
                const response = await BookService.getBook(id);
                this.currentBook = response.data.data;
            } catch (err) {
                this.error = 'Lỗi: Không thể tải thông tin sách.';
                console.error(err);
            } finally {
                this.isLoading = false;
            }
        },

        async createBook(bookData) {
            this.isLoading = true;
            this.error = null;
            try {
                const response = await BookService.createBook(bookData);
                this.books.unshift(response.data.data);
                return true;
            } catch (err) {
                this.error = 'Lỗi: Không thể tạo sách.';
                console.error(err);
                return false;
            } finally {
                this.isLoading = false;
            }
        },

        async updateBook(id, bookData) {
            this.isLoading = true;
            this.error = null;
            try {
                const response = await BookService.updateBook(id, bookData);
                const index = this.books.findIndex(b => b.id === id);
                if (index !== -1) {
                    this.books[index] = response.data.data;
                }
                return true;
            } catch (err) {
                this.error = 'Lỗi: Không thể cập nhật sách.';
                console.error(err);
                return false;
            } finally {
                this.isLoading = false;
            }
        },

        async borrowBook(bookId) {
            this.isLoading = true;
            try {
                await BookService.borrowBook(bookId);
                
                // Mượn thành công thì phải cập nhật lại danh sách để số lượng giảm đi
                await this.fetchBooks();                
                return { success: true, message: 'Mượn sách thành công!' };
            } catch (err) {
                const msg = err.response?.data?.message || 'Không thể mượn sách.';
                return { success: false, message: msg };
            } finally {
                this.isLoading = false;
            }
        },

        async fetchBorrowedBooks() {
            this.isLoading = true;
            try {
                const response = await BookService.getMyLoans();
                this.borrowedBooks = response.data.data;
            } catch (err) {
                console.error(err);
            } finally {
                this.isLoading = false;
            }
        },

        // Trả sách
        async returnBook(bookId) {
            this.isLoading = true;
            try {
                await BookService.returnBook(bookId);
                
                // Xóa khỏi danh sách đang mượn
                this.borrowedBooks = this.borrowedBooks.filter(item => item.book_id !== bookId);
                
                // Cập nhật lại list sách chính để hiển thị số lượng tồn kho mới
                await this.fetchBooks(); 

                return { success: true, message: 'Đã trả sách thành công.' };
            } catch (err) {
                return { success: false, message: err.response?.data?.message || 'Lỗi trả sách' };
            } finally {
                this.isLoading = false;
            }
        },
    }
});