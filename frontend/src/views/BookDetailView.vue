<template>
  <div class="book-detail-page container py-5">
    
    <div v-if="isLoading" class="text-center py-5">
      <div class="spinner-border text-primary" role="status"></div>
      <p class="mt-2 text-muted">Đang tải thông tin sách...</p>
    </div>

    <div v-else-if="!book" class="text-center py-5">
      <h3 class="text-danger">Không tìm thấy sách</h3>
      <button @click="router.push('/')" class="btn btn-outline-primary mt-3">
        <i class="fas fa-arrow-left"></i> Quay lại trang chủ
      </button>
    </div>

    <div v-else class="row g-5">
      <div class="col-md-4">
        <div class="card shadow border-0 overflow-hidden">
           <img 
               v-if="book.image_url" 
               :src="book.image_url" 
               :alt="book.title"
               class="img-fluid w-100"
               style="object-fit: cover; max-height: 500px;" 
           >
           <div v-else class="bg-secondary text-white d-flex justify-content-center align-items-center" style="height: 400px; font-size: 5rem;">
              <i class="fas fa-book opacity-50"></i>
           </div>
        </div>
      </div>

      <div class="col-md-8">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><router-link to="/">Trang chủ</router-link></li>
            <li class="breadcrumb-item"><router-link to="/books">Sách</router-link></li>
            <li class="breadcrumb-item active" aria-current="page">{{ book.title }}</li>
          </ol>
        </nav>

        <h1 class="fw-bold mb-2">{{ book.title }}</h1>
        <p class="text-muted fs-5 mb-4">
          <i class="fas fa-user-edit me-2"></i> Tác giả: <strong>{{ book.author }}</strong>
        </p>

        <div class="d-flex gap-3 mb-4">
          <span class="badge bg-primary fs-6 px-3 py-2">{{ book.category }}</span>
          <span :class="['badge fs-6 px-3 py-2', book.available_copies > 0 ? 'bg-success' : 'bg-danger']">
             {{ book.available_copies > 0 ? 'Còn hàng' : 'Hết hàng' }}
          </span>
        </div>

        <div class="card bg-light border-0 p-4 mb-4">
            <h5 class="fw-bold">Mô tả</h5>
            <p class="mb-0">{{ book.description || 'Chưa có mô tả cho cuốn sách này.' }}</p>
        </div>

        <div class="d-flex gap-3 mt-4">
           
           <template v-if="isAdmin">
               <button @click="handleEdit" class="btn btn-warning btn-lg px-4 text-white">
                  <i class="fas fa-edit me-2"></i> Sửa sách
               </button>
               
               <button @click="handleDelete" class="btn btn-danger btn-lg px-4">
                  <i class="fas fa-trash-alt me-2"></i> Xóa sách
               </button>
           </template>

           <template v-else>
               <button @click="handleBorrow" class="btn btn-primary btn-lg px-5" :disabled="book.available_copies < 1">
                  <i class="fas fa-book-reader me-2"></i> 
                  {{ book.available_copies > 0 ? 'Mượn Sách Ngay' : 'Tạm Hết Hàng' }}
               </button>
               
               <button @click="toggleWishlist" :class="['btn btn-lg px-4', isInWishlist ? 'btn-danger' : 'btn-outline-danger']">
                  <i :class="[isInWishlist ? 'fas' : 'far', 'fa-heart']"></i>
               </button>
           </template>

        </div>
        
        <p class="mt-3 text-muted small">
            <i class="fas fa-info-circle me-1"></i> 
            Số lượng còn lại trong kho: {{ book.available_copies }} cuốn.
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useBookStore } from '@/stores/bookStore';
import { useAuthStore } from '@/stores/authStore';

const route = useRoute();
const router = useRouter();
const bookStore = useBookStore();
const authStore = useAuthStore();

const book = ref(null);
const isLoading = ref(true);

const isAdmin = computed(() => {
    return authStore.user && authStore.user.role === 'admin'; 
});

const isInWishlist = computed(() => {
    return book.value && bookStore.wishlist.some(b => b.id === book.value.id);
});

onMounted(async () => {
    const bookId = parseInt(route.params.id);
    
    await bookStore.fetchBook(bookId);
    
    if (bookStore.currentBook && bookStore.currentBook.id === bookId) {
        book.value = bookStore.currentBook;
    } else {
        if (bookStore.books.length === 0) {
            await bookStore.fetchBooks();
        }
        book.value = bookStore.books.find(b => b.id === bookId);
    }
    
    isLoading.value = false;
});

const handleBorrow = async () => {
    if (!authStore.user) {
        if(confirm('Vui lòng đăng nhập để mượn sách. Đi đến trang đăng nhập?')) {
            router.push('/login');
        }
        return;
    }
    if(confirm(`Xác nhận mượn cuốn: ${book.value.title}?`)) {
        const result = await bookStore.borrowBook(book.value.id);
        alert(result.message);
        if(result.success) {
            // Cập nhật lại số lượng sau khi mượn thành công
            await bookStore.fetchBook(book.value.id);
            book.value = bookStore.currentBook;
        }
    }
};

const toggleWishlist = async () => {
    if (!authStore.user) {
         alert('Bạn cần đăng nhập để sử dụng tính năng này.');
         return;
    }
    await bookStore.toggleWishlist(book.value);
};

const handleEdit = () => {
    router.push(`/books/edit/${book.value.id}`);
};

const handleDelete = async () => {
    if(confirm(`CẢNH BÁO: Bạn có chắc chắn muốn xóa cuốn sách "${book.value.title}" không? Hành động này không thể hoàn tác.`)) {
        try {
            const result = await bookStore.deleteBook(book.value.id);
            alert('Đã xóa sách thành công.');
            router.push('/books'); 
        } catch (error) {
            alert('Có lỗi xảy ra khi xóa sách: ' + error.message);
        }
    }
};
</script>