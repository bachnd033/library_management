import api from '@/api/axios'; // Import instance Axios đã cấu hình

export const BookService = {
    // [R] READ: Lấy danh sách sách
    getAllBooks() {
        return api.get('/books');
        console.log("⚡ ĐANG GỌI API VỚI BASE URL:", api.defaults.baseURL);
    },

    // [R] READ: Lấy chi tiết sách
    getBook(id) {
        return api.get(`/books/${id}`);
    },

    // [C] CREATE: Tạo sách mới
    createBook(data) {
        // Gửi request POST kèm dữ liệu sách mới
        return api.post('/books', data);
    },

    // [U] UPDATE: Cập nhật thông tin sách
    updateBook(id, data) {
        // Gửi request PUT/PATCH kèm ID và dữ liệu cập nhật
        return api.put(`/books/${id}`, data);
    },

    // [D] DELETE: Xóa sách
    deleteBook(id) {
        return api.delete(`/books/${id}`);
    }
};