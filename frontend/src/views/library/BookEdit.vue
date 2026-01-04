<template>
  <div class="card shadow-sm p-4">
    <h1 class="h3 border-bottom pb-3 mb-4">Chỉnh Sửa Sách (ID: {{ bookId }})</h1>

    <div v-if="bookStore.isLoading && !bookStore.currentBook" class="alert alert-info">
      Đang tải dữ liệu sách...
    </div>
    
    <div v-else-if="bookStore.error && !bookStore.currentBook" class="alert alert-danger">
      {{ bookStore.error }}
    </div>

    <BookForm 
      v-if="bookStore.currentBook"
      :book="bookStore.currentBook"
      @submit="handleUpdate" 
      :isLoading="bookStore.isLoading"
      :error="bookStore.error"
      submitText="Cập Nhật"
    />
  </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useBookStore } from '@/stores/bookStore';
import BookForm from '@/components/BookForm.vue';

const router = useRouter();
const route = useRoute();
const bookStore = useBookStore();
const bookId = route.params.id; // Lấy ID từ URL

// Lấy dữ liệu sách cần sửa khi component được tải
onMounted(() => {
  bookStore.fetchBook(bookId);
});

// Xử lý logic cập nhật
const handleUpdate = async (bookData) => {
  const success = await bookStore.updateBook(bookId, bookData);
  if (success) {
    alert('Cập nhật sách thành công!');
    router.push('/books'); // Quay về trang danh sách
  }
};
</script>