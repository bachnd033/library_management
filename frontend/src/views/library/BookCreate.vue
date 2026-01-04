<template>
  <div class="card shadow-sm p-4">
    <h1 class="h3 border-bottom pb-3 mb-4">Thêm Sách Mới</h1>
    
    <BookForm 
      @submit="handleCreate" 
      :isLoading="bookStore.isLoading"
      :error="bookStore.error"
      submitText="Thêm Mới"
    />
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router';
import { useBookStore } from '@/stores/bookStore';
import BookForm from '@/components/BookForm.vue'; 

const router = useRouter();
const bookStore = useBookStore();

// Xử lý logic tạo
const handleCreate = async (bookData) => {
  const success = await bookStore.createBook(bookData);
  if (success) {
    alert('Thêm sách mới thành công!');
    router.push('/books'); // Quay về trang danh sách
  }
  // Nếu thất bại, 'bookStore.error' sẽ tự động hiển thị trong BookForm
};
</script>