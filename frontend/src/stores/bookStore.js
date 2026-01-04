import { defineStore } from 'pinia';
import { BookService } from '@/services/BookService'; 
import api from '@/api/axios'; 

export const useBookStore = defineStore('bookStore', {
    state: () => ({
        books: [],
        currentBook: null,
        borrowedBooks: [],
        wishlist: [],
        pagination: {
            current_page: 1,
            last_page: 1,
            total: 0
        },
        isLoading: false,
        error: null,
    }),
    
    getters: {
        isInWishlist: (state) => (bookId) => {
            if (!state.wishlist || !Array.isArray(state.wishlist)) return false; 
            return state.wishlist.some((item) => item.id === parseInt(bookId)); 
        },
    },

    actions: {
        handleError(err, defaultMsg) {
            if (err.response && err.response.data && err.response.data.message) {
                this.error = err.response.data.message;
            } else {
                this.error = defaultMsg;
            }
            console.error(defaultMsg, err);
        },

        async fetchBooks(params = {}) { 
            this.isLoading = true;
            this.error = null;
            try {
                const payload = {
                    page: params.page || 1,
                    search: params.search || ''
                };

                const response = await BookService.getAllBooks(payload);
                
                this.books = response.data.data;
                
                const meta = response.data; 
                this.pagination = {
                    current_page: meta.current_page,
                    last_page: meta.last_page,
                    total: meta.total
                };
                
            } catch (err) {
                this.handleError(err, 'Không thể tải danh sách sách.');
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
                this.handleError(err, 'Lỗi: Không thể tải thông tin sách.');
            } finally {
                this.isLoading = false;
            }
        },

        async createBook(bookData) {
            this.isLoading = true;
            this.error = null;
            try {
                const response = await BookService.createBook(bookData);
                
                // Thêm vào đầu danh sách để user thấy ngay
                this.books.unshift(response.data.data);
                
                // Cập nhật lại số lượng total trong pagination (để UI đồng bộ)
                this.pagination.total += 1;

                return true;
            } catch (err) {
                this.handleError(err, 'Lỗi: Không thể tạo sách.');
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
                
                // Cập nhật lại sách trong list local để không cần reload trang
                const index = this.books.findIndex(b => b.id === parseInt(id));
                if (index !== -1) {
                    this.books[index] = response.data.data;
                }
                
                // Nếu đang xem chi tiết sách đó thì update luôn currentBook
                if (this.currentBook && this.currentBook.id === parseInt(id)) {
                    this.currentBook = response.data.data;
                }

                return true;
            } catch (err) {
                this.handleError(err, 'Lỗi: Không thể cập nhật sách.');
                return false;
            } finally {
                this.isLoading = false;
            }
        },

        async deleteBook(id) {
            this.isLoading = true;
            try {
                await BookService.deleteBook(id);
                this.books = this.books.filter(book => book.id !== id);
                this.pagination.total -= 1; // Giảm số lượng tổng
                return true;
            } catch (err) {
                this.handleError(err, 'Không thể xóa sách.');
                return false;
            } finally {
                this.isLoading = false;
            }
        },
        
        async toggleWishlist(input) {

            try {
                const bookId = (typeof input === 'object') ? input.id : input;
                const bookObj = (typeof input === 'object') ? input : null;

                const exists = this.isInWishlist(bookId);
                if (exists) {
                    this.wishlist = this.wishlist.filter(item => item.id !== bookId);
                } else if (bookObj) {
                    this.wishlist.push(bookObj);
                }

                // Gọi API để toggle trên server
                await api.post('/api/wishlist/toggle', { book_id: bookId });
                
                // Nếu không có object sách (chỉ có ID), cần fetch lại để lấy data đầy đủ
                if (!bookObj && !exists) {
                     await this.fetchWishlist();
                }
                
                return true;
            } catch (error) {
                console.error("Lỗi toggle wishlist:", error);
                await this.fetchWishlist(); // Re-sync nếu lỗi 
                return false;
            }
        },
        
        async fetchWishlist() {
            try {
                const response = await api.get('/api/wishlist');
                // Xử lý cả 2 trường hợp response trả về là mảng trực tiếp hoặc có data bên trong
                if (response.data.data) {
                    this.wishlist = response.data.data;
                } else {
                    this.wishlist = response.data;
                }
            } catch (error) {
                console.log(error);
            }
        }
    }
});