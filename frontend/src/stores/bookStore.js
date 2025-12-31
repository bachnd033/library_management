import { defineStore } from 'pinia';
import { BookService } from '@/services/BookService';
import api from '@/api/axios'; 

export const useBookStore = defineStore('bookStore', {
    state: () => ({
        books: [],
        currentBook: null,
        borrowedBooks: [],
        wishlist: [],
        pagination: {},
        isLoading: false,
        error: null,
    }),
    
    getters: {
        isInWishlist: (state) => (bookId) => {
            if (!state.wishlist || !Array.isArray(state.wishlist)) {
                return false; 
            }
            return state.wishlist.some((item) => item.id === bookId);
        },
    },

    actions: {
        async fetchBooks(params = {}) { 
            this.isLoading = true;
            this.error = null;
            try {
                // Mặc định page là 1 
                const payload = {
                    page: params.page || 1,
                    search: params.search || '' // Thêm tham số search
                };

                // Gọi service
                const response = await BookService.getAllBooks(payload);
                
                this.books = response.data.data;
                
                // Lưu lại pagination 
                this.pagination = {
                    current_page: response.data.current_page,
                    last_page: response.data.last_page,
                    total: response.data.total
                };
                
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

        async returnBook(bookId) {
            this.isLoading = true;
            try {
                await BookService.returnBook(bookId);
                this.borrowedBooks = this.borrowedBooks.filter(item => item.book_id !== bookId);
                await this.fetchBooks(); 
                return { success: true, message: 'Đã trả sách thành công.' };
            } catch (err) {
                return { success: false, message: err.response?.data?.message || 'Lỗi trả sách' };
            } finally {
                this.isLoading = false;
            }
        },

        async fetchWishlist() {
            try {
                const response = await api.get('/api/wishlist');

                if (Array.isArray(response.data)) {
                    this.wishlist = response.data;
                } else if (response.data && Array.isArray(response.data.data)) {
                    this.wishlist = response.data.data;
                } else {
                    this.wishlist = [];
                }
            } catch (error) {
                console.error("Lỗi fetchWishlist:", error);
                if (error.response && error.response.status === 401) {
                    //
                }
            }
        },

        async toggleWishlist(input) {
            try {
                const bookId = (typeof input === 'object') ? input.id : input;
                const bookObj = (typeof input === 'object') ? input : null;

                await api.post('/api/wishlist/toggle', { book_id: bookId });
                
                // Cập nhật giao diện
                if (bookObj) {
                    if (this.isInWishlist(bookId)) {
                        this.wishlist = this.wishlist.filter(item => item.id !== bookId);
                    } else {
                        this.wishlist.push(bookObj);
                    }
                } else {
                    // Nếu chỉ có ID thì phải tải lại để lấy đủ thông tin
                    await this.fetchWishlist();
                }
                
                return true;
            } catch (error) {
                console.error("Lỗi toggle wishlist:", error);
                await this.fetchWishlist(); // Sync lại nếu lỗi
                return false;
            }
        },
    }
});