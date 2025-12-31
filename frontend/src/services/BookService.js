import api from '@/api/axios'; 

export const BookService = {
    getAllBooks(params) {
        return api.get('/api/books', { params: params });
    },

    getBook(id) {
        return api.get(`/api/books/${id}`);
    },

    createBook(data) {
        return api.post('/api/books', data);
    },

    updateBook(id, data) {
        return api.put(`/api/books/${id}`, data);
    },

    deleteBook(id) {
        return api.delete(`/api/books/${id}`);
    },

    borrowBook(bookId) {
        return api.post('/api/borrow', { book_id: bookId });
    },

    getMyLoans() {
        return api.get('/api/my-loans'); 
    },

    returnBook(bookId) {
        return api.post('/api/return', { book_id: bookId });
    },
};