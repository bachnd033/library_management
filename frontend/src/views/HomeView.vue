<template>
  <div class="home-page d-flex flex-column min-vh-100">
    
    <header class="hero-section text-center text-white d-flex align-items-center">
      <div class="container position-relative z-index-1">
        <h1 class="display-4 fw-bold mb-3">Chào mừng đến với Thư Viện Trực Tuyến</h1>
        <p class="lead mb-4">Khám phá kho tàng tri thức với hàng ngàn đầu sách hấp dẫn.</p>
        
        <div class="search-box mx-auto bg-white p-2 rounded shadow">
          <form @submit.prevent="handleSearch" class="d-flex">
            <input 
              v-model="searchQuery"
              type="text" 
              class="form-control border-0 me-2" 
              placeholder="Tìm kiếm sách theo tên, tác giả..."
            >
            <button class="btn btn-primary px-4 fw-bold" type="submit">
              <i class="fas fa-search"></i> Tìm Kiếm
            </button>
          </form>
        </div>
      </div>
      <div class="overlay"></div>
    </header>

    <section class="py-5 bg-light">
      <div class="container">
        <div class="row text-center g-4">
          <div class="col-md-4">
            <div class="p-3 bg-white shadow-sm rounded">
              <h2 class="fw-bold text-primary">5,000+</h2>
              <p class="text-muted mb-0">Đầu sách</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="p-3 bg-white shadow-sm rounded">
              <h2 class="fw-bold text-success">1,200+</h2>
              <p class="text-muted mb-0">Độc giả</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="p-3 bg-white shadow-sm rounded">
              <h2 class="fw-bold text-warning">24/7</h2>
              <p class="text-muted mb-0">Tra cứu Online</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="py-5">
      <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h2 class="h3 fw-bold border-start border-4 border-primary ps-3">Sách Mới Cập Nhật</h2>
          <router-link to="/books" class="btn btn-outline-primary btn-sm">Xem tất cả &rarr;</router-link>
        </div>

        <div v-if="bookStore.isLoading" class="text-center py-5">
           <div class="spinner-border text-primary" role="status"></div>
           <p class="mt-2 text-muted">Đang tải sách mới...</p>
        </div>

        <div v-else-if="featuredBooks.length === 0" class="text-center py-5 text-muted">
            Chưa có sách nào được cập nhật.
        </div>

        <div v-else class="row g-4">
          <div v-for="book in featuredBooks" :key="book.id" class="col-6 col-md-3">
            <div class="card h-100 shadow-sm border-0 book-card">
              <div class="card-img-top bg-secondary text-white d-flex justify-content-center align-items-center position-relative overflow-hidden" style="height: 200px;">
                <i class="fas fa-book fa-3x opacity-50"></i>
                <div v-if="book.available_copies < 1" class="position-absolute top-0 end-0 bg-danger text-white px-2 py-1 small m-2 rounded">
                    Hết hàng
                </div>
              </div>
              
              <div class="card-body d-flex flex-column">
                <h5 class="card-title text-truncate" :title="book.title">{{ book.title }}</h5>
                <p class="card-text text-muted small mb-2"><i class="fas fa-user-edit me-1"></i> {{ book.author }}</p>
                <div class="mt-auto d-flex justify-content-between align-items-center">
                  <span class="badge bg-light text-dark border">{{ book.category }}</span>
                  <small class="text-muted" v-if="book.available_copies > 0">Còn {{ book.available_copies }} cuốn</small>
                </div>
              </div>
              
              <div class="card-footer bg-transparent border-top-0 pt-0 d-flex gap-2">
                 <button @click="viewDetails(book.id)" class="btn btn-outline-primary btn-sm flex-grow-1">
                    Chi tiết
                 </button>
                 <button 
                    @click="handleBorrow(book)" 
                    class="btn btn-primary btn-sm flex-grow-1"
                    :disabled="book.available_copies < 1"
                 >
                    Mượn
                 </button>
              </div>
            </div>
          </div>
        </div>

      </div>
    </section>

    <Footer />

  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useBookStore } from '@/stores/bookStore';
import { useAuthStore } from '@/stores/authStore'; 
import Footer from '@/components/Footer.vue';

const router = useRouter();
const bookStore = useBookStore(); 
const authStore = useAuthStore();
const searchQuery = ref('');

onMounted(async () => {
    await bookStore.fetchBooks();
});

const featuredBooks = computed(() => {
    if (!bookStore.books) return [];
    return bookStore.books.slice(0, 4);
});

const handleSearch = () => {
  if (searchQuery.value.trim()) {
    router.push({ 
        path: '/books', 
        query: { search: searchQuery.value } 
    });
  }
};

// Hàm chuyển hướng đến trang chi tiết
const viewDetails = (bookId) => {
    router.push(`/books/${bookId}`);
};

// Hàm xử lý mượn sách
const handleBorrow = async (book) => {
    // Kiểm tra đăng nhập
    if (!authStore.user) {
        if(confirm('Bạn cần đăng nhập để mượn sách. Đi đến trang đăng nhập?')) {
            router.push('/login');
        }
        return;
    }

    // Gọi store để mượn
    if (confirm(`Bạn muốn mượn cuốn sách "${book.title}"?`)) {
        const result = await bookStore.borrowBook(book.id);
        alert(result.message);
        // Load lại danh sách để cập nhật số lượng tồn kho nếu thành công
        if(result.success) {
            await bookStore.fetchBooks(); 
        }
    }
};
</script>

<style scoped>
.hero-section {
  background-image: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('../assets/hero-image.png');
  background-size: cover;
  background-position: center;
  height: 500px;
  position: relative;
}

.z-index-1 {
  z-index: 1; 
}

.search-box {
  max-width: 600px;
}

.book-card {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  cursor: pointer;
}

.book-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15) !important;
}

.card-img-top {
    background-color: #6c757d; 
}
</style>