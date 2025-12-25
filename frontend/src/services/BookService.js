import api from '@/api/axios'; 

export const BookService = {
    getAllBooks() {
        return api.get('/books');
    },

    getBook(id) {
        return api.get(`/books/${id}`);
    },

    createBook(data) {
        return api.post('/books', data);
    },

    updateBook(id, data) {
        return api.put(`/books/${id}`, data);
    },

    deleteBook(id) {
        return api.delete(`/books/${id}`);
    },

    borrowBook(bookId) {
        return api.post('/borrow', { book_id: bookId });
    },

    getMyLoans() {
        return api.get('/my-loans');
    },

    returnBook(bookId) {
        return api.post('/return', { book_id: bookId });
    },
};