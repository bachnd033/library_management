<template>
  <div class="container mt-4">
    <div class="card shadow-sm p-4">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0">Sách Tôi Đang Mượn</h2>
        <router-link to="/books" class="btn btn-outline-secondary">
          <i class="fas fa-arrow-left"></i> Quay lại thư viện
        </router-link>
      </div>

      <div v-if="bookStore.isLoading" class="text-center py-4">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2 text-muted">Đang tải dữ liệu...</p>
      </div>
      
      <div v-else-if="bookStore.error" class="alert alert-danger">
        {{ bookStore.error }}
      </div>

      <div v-else-if="bookStore.borrowedBooks.length === 0" class="text-center py-5 bg-light rounded">
        <h4 class="text-muted">Bạn chưa mượn cuốn sách nào.</h4>
        <p class="mb-3">Hãy tìm một cuốn sách thú vị và mượn ngay nhé!</p>
        <router-link to="/books" class="btn btn-primary">Đi mượn sách ngay</router-link>
      </div>

      <div v-else class="table-responsive">
        <table class="table align-middle table-hover">
          <thead class="table-light">
            <tr>
              <th>Tên sách</th>
              <th>Ngày mượn</th>
              <th>Hạn trả</th>
              <th>Trạng thái</th>
              <th class="text-end">Thao tác</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="loan in bookStore.borrowedBooks" :key="loan.id">
              <td>
                <span class="fw-bold text-primary">{{ loan.book?.title }}</span>
                <br>
                <small class="text-muted">{{ loan.book?.author }}</small>
              </td>
              <td>{{ new Date(loan.borrowed_at).toLocaleDateString('vi-VN') }}</td>
              <td>
                 <span :class="new Date(loan.due_date) < new Date() ? 'text-danger fw-bold' : ''">
                    {{ new Date(loan.due_date).toLocaleDateString('vi-VN') }}
                 </span>
                 <small v-if="new Date(loan.due_date) < new Date()" class="text-danger d-block">(Quá hạn)</small>
              </td>
              <td><span class="badge bg-warning text-dark">Đang mượn</span></td>
              <td class="text-end">
                <button 
                  @click="handleReturn(loan.book_id)" 
                  class="btn btn-outline-primary btn-sm"
                  :disabled="bookStore.isLoading"
                >
                  Trả Sách
                </button>
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
import { useBookStore } from '@/stores/bookStore';

const bookStore = useBookStore();

onMounted(async () => {
  await bookStore.fetchBorrowedBooks();
});

// Xử lý nút Trả sách
const handleReturn = async (bookId) => {
  if (confirm('Xác nhận trả cuốn sách này?')) {
    const result = await bookStore.returnBook(bookId);
    if (result.success) {
      alert('Đã trả sách thành công!');
    } else {
      alert(result.message);
    }
  }
};
</script>

<style scoped>
</style>