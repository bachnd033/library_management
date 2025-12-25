<template>
  <div class="container mt-4">
    <div class="book-management card shadow-sm p-4">
      
      <header class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
        <h1 class="h3">Quản Lý Danh Mục Sách</h1>
        
        <button 
          v-if="authStore.user?.role === 'admin'" 
          @click="router.push('/books/create')" 
          class="btn btn-primary"
        >
          <i class="fas fa-plus"></i> Thêm Sách Mới
        </button>
      </header>

      <div v-if="bookStore.isLoading" class="alert alert-info">Đang tải dữ liệu...</div>
      <div v-else-if="bookStore.error" class="alert alert-danger">{{ bookStore.error }}</div>

      <div class="table-responsive" v-else>
        <table class="table table-hover align-middle">
          <thead class="table-light">
            <tr>
              <th>ID</th>
              <th>Tiêu đề</th>
              <th>Tác giả</th>
              <th>Thể loại</th>
              <th>Tồn kho (Sẵn / Tổng)</th>
              <th>Thao tác</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="bookStore.books.length === 0">
              <td colspan="6" class="text-center text-muted">Không có dữ liệu sách.</td>
            </tr>
            <tr v-for="book in bookStore.books" :key="book.id">
              <td>{{ book.id }}</td>
              <td class="fw-bold text-primary">{{ book.title }}</td>
              <td>{{ book.author }}</td>
              <td>{{ book.category }}</td>
              
              <td>
                <span class="fw-bold" :class="book.available_copies > 0 ? 'text-success' : 'text-danger'">
                  {{ book.available_copies ?? 0 }}
                </span> 
                <span class="text-muted mx-1">/</span>
                <span class="fw-bold">{{ book.total_copies ?? 0 }}</span>
              </td>

              <td>
                <div v-if="authStore.user?.role === 'admin'">
                  <button @click="router.push(`/books/edit/${book.id}`)" class="btn btn-warning btn-sm me-2">
                    Sửa
                  </button>
                  <button @click="handleDelete(book.id, book.title)" class="btn btn-danger btn-sm">
                    Xóa
                  </button>
                </div>

                <div v-else>
                  <button 
                    @click="handleBorrow(book.id)" 
                    class="btn btn-success btn-sm"
                    :disabled="book.available_copies < 1 || bookStore.isLoading"
                  >
                    {{ book.available_copies > 0 ? 'Mượn Sách' : 'Hết Hàng' }}
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useBookStore } from '@/stores/bookStore';
import { useAuthStore } from '@/stores/authStore'; 

const bookStore = useBookStore();
const authStore = useAuthStore();
const router = useRouter();

onMounted(() => {
  bookStore.fetchBooks();
});

const handleDelete = async (bookId, bookTitle) => {
  if (confirm(`Bạn có chắc chắn muốn xóa sách "${bookTitle}" không?`)) {
    const success = await bookStore.deleteBook(bookId);
    if (success) {
      alert('Xóa sách thành công!');
    }
  }
};

const handleBorrow = async (bookId) => {
    // Hỏi xác nhận trước khi mượn
    if (!confirm('Bạn có chắc muốn mượn cuốn sách này?')) return;

    const result = await bookStore.borrowBook(bookId);
    
    if (result.success) {
        alert(result.message);
    } else {
        alert('Lỗi: ' + result.message);
    }
};
</script>

<style scoped>
  .h3 {
    margin-bottom: 0; 
  }
  button:disabled {
    cursor: not-allowed;
    opacity: 0.7;
  }
</style>