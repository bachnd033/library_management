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

        <div class="row g-4">
          <div v-for="book in featuredBooks" :key="book.id" class="col-6 col-md-3">
            <div class="card h-100 shadow-sm border-0 book-card">
              <div class="card-img-top bg-secondary text-white d-flex justify-content-center align-items-center" style="height: 200px;">
                <span>Ảnh bìa</span>
              </div>
              <div class="card-body d-flex flex-column">
                <h5 class="card-title text-truncate" :title="book.title">{{ book.title }}</h5>
                <p class="card-text text-muted small mb-2">{{ book.author }}</p>
                <div class="mt-auto d-flex justify-content-between align-items-center">
                  <span class="badge bg-light text-dark border">{{ book.category }}</span>
                </div>
              </div>
              <div class="card-footer bg-transparent border-top-0">
                <button @click="router.push(`/books/edit/${book.id}`)" class="btn btn-primary btn-sm w-100">Chi tiết</button>
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
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import Footer from '@/components/Footer.vue';

const router = useRouter();
const searchQuery = ref('');

// Dữ liệu giả lập cho sách nổi bật (Sau này bạn có thể gọi API để lấy 4 sách mới nhất)
const featuredBooks = ref([
  { id: 1, title: 'Nhà Giả Kim', author: 'Paulo Coelho', category: 'Văn học' },
  { id: 2, title: 'Đắc Nhân Tâm', author: 'Dale Carnegie', category: 'Kỹ năng' },
  { id: 3, title: 'Sapiens: Lược Sử Loài Người', author: 'Yuval Noah Harari', category: 'Lịch sử' },
  { id: 4, title: 'Clean Code', author: 'Robert C. Martin', category: 'Công nghệ' },
]);

const handleSearch = () => {
  if (searchQuery.value.trim()) {
    // Chuyển hướng sang trang BookList và kèm theo query search
    // Bạn cần sửa BookList để nhận query này (tôi sẽ hướng dẫn nếu cần)
    router.push({ name: 'BookList', query: { search: searchQuery.value } });
  }
};
</script>

<style scoped>
/* Hero Section Styling */
.hero-section {
  /* Ảnh nền thư viện (có thể thay link ảnh khác) */
  background-image: url('https://images.unsplash.com/photo-1507842217121-9e96c885ee3f?ixlib=rb-1.2.1&auto=format&fit=crop&w=1920&q=80');
  background-size: cover;
  background-position: center;
  height: 500px;
  position: relative;
}

.overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.6); /* Lớp phủ đen mờ để chữ dễ đọc */
  z-index: 0;
}

.z-index-1 {
  z-index: 1; /* Đảm bảo nội dung nằm trên lớp phủ */
}

.search-box {
  max-width: 600px;
}

/* Hiệu ứng hover cho card sách */
.book-card {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.book-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
}
</style>