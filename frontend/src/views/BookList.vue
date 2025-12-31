<template>
  <div class="container mt-4">
    <div class="book-management card shadow-sm p-4">
      
      <header class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
        <h1 class="h3 text-primary"><i class="fas fa-book me-2"></i>Quản Lý Danh Mục Sách</h1>
        
        <button 
          v-if="authStore.user?.role === 'admin'" 
          @click="router.push('/books/create')" 
          class="btn btn-primary shadow-sm"
        >
          <i class="fas fa-plus"></i> Thêm Sách Mới
        </button>
      </header>

      <div class="row mb-4">
        <div class="col-md-6 mx-auto">
            <div class="input-group shadow-sm">
                <span class="input-group-text bg-white text-muted border-end-0">
                    <i class="fas fa-search"></i>
                </span>
                <input 
                    v-model="searchQuery"
                    @keyup.enter="handleSearch"
                    type="text" 
                    class="form-control border-start-0" 
                    placeholder="Nhập tên sách hoặc tác giả..." 
                >
                <button @click="handleSearch" class="btn btn-primary px-4">Tìm kiếm</button>
            </div>
        </div>
      </div>

      <div v-if="bookStore.isLoading" class="text-center py-5">
        <div class="spinner-border text-primary" role="status"></div>
        <p class="mt-2 text-muted">Đang tải dữ liệu...</p>
      </div>

      <div v-else-if="bookStore.error" class="alert alert-danger shadow-sm">
        <i class="fas fa-exclamation-triangle me-2"></i> {{ bookStore.error }}
      </div>

      <div class="table-responsive" v-else>
        <table class="table table-hover align-middle shadow-sm rounded overflow-hidden">
          <thead class="table-light text-secondary">
            <tr>
              <th class="fw-bold">ID</th>
              <th class="fw-bold">Tiêu đề</th>
              <th class="fw-bold">Tác giả</th>
              <th class="fw-bold">Thể loại</th>
              <th class="fw-bold text-center">Tồn kho</th>
              <th class="fw-bold text-center">Thao tác</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="bookStore.books.length === 0">
              <td colspan="6" class="text-center py-5 text-muted">
                <i class="fas fa-box-open fa-3x mb-3 d-block opacity-50"></i>
                <span v-if="searchQuery">Không tìm thấy sách nào khớp với từ khóa "{{ searchQuery }}"</span>
                <span v-else>Chưa có dữ liệu sách nào trong hệ thống.</span>
              </td>
            </tr>

            <tr v-for="book in bookStore.books" :key="book.id">
              <td>#{{ book.id }}</td>
              <td>
                 <span class="fw-bold text-dark">{{ book.title }}</span>
              </td>
              <td class="text-muted">{{ book.author }}</td>
              <td><span class="badge bg-light text-dark border">{{ book.category }}</span></td>
              
              <td class="text-center">
                <span class="fw-bold" :class="book.available_copies > 0 ? 'text-success' : 'text-danger'">
                  {{ book.available_copies ?? 0 }}
                </span> 
                <span class="text-muted small mx-1">/</span>
                <span class="fw-bold text-secondary">{{ book.total_copies ?? 0 }}</span>
              </td>

              <td class="text-center">
                <div v-if="authStore.user?.role === 'admin'">
                  <button @click="router.push(`/books/edit/${book.id}`)" class="btn btn-outline-warning btn-sm me-2" title="Chỉnh sửa">
                    <i class="fas fa-edit"></i>
                  </button>
                  <button @click="handleDelete(book.id, book.title)" class="btn btn-outline-danger btn-sm" title="Xóa">
                    <i class="fas fa-trash-alt"></i>
                  </button>
                </div>

                <div v-else class="d-flex justify-content-center align-items-center">
                  <button 
                    @click="handleToggleWishlist(book)"
                    class="btn btn-sm me-2 btn-icon-only"
                    :class="bookStore.isInWishlist(book.id) ? 'text-danger' : 'text-muted'"
                    title="Yêu thích"
                  >
                    <i :class="bookStore.isInWishlist(book.id) ? 'fas fa-heart fa-lg' : 'far fa-heart fa-lg'"></i> 
                  </button>

                  <button 
                    @click="handleBorrow(book.id)" 
                    class="btn btn-sm px-3 fw-bold"
                    :class="book.available_copies > 0 ? 'btn-success' : 'btn-secondary'"
                    :disabled="book.available_copies < 1 || bookStore.isLoading"
                  >
                    <i class="fas fa-book-reader me-1"></i>
                    {{ book.available_copies > 0 ? 'Mượn' : 'Hết' }}
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
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useBookStore } from '@/stores/bookStore';
import { useAuthStore } from '@/stores/authStore'; 

const bookStore = useBookStore();
const authStore = useAuthStore();
const router = useRouter();

// Biến lưu từ khóa tìm kiếm
const searchQuery = ref('');

onMounted(async () => {
  // Tải danh sách sách
  await bookStore.fetchBooks();
  
  // Nếu là user thường thì tải thêm wishlist
  if (authStore.user && authStore.user.role !== 'admin') {
    await bookStore.fetchWishlist();
  }
});

// Xử lý tìm kiếm
const handleSearch = async () => {
    await bookStore.fetchBooks({ 
        search: searchQuery.value, // Gửi từ khóa lên store
        page: 1 // Reset về trang 1 khi tìm kiếm
    });
};

const handleDelete = async (bookId, bookTitle) => {
  if (confirm(`Bạn có chắc chắn muốn xóa sách "${bookTitle}" không?`)) {
    const success = await bookStore.deleteBook(bookId);
    if (success) {
      // Xóa xong thì load lại danh sách, giữ nguyên từ khóa tìm kiếm
      await bookStore.fetchBooks({ search: searchQuery.value });
      alert('Xóa sách thành công!');
    }
  }
};

const handleBorrow = async (bookId) => {
    if (!confirm('Bạn có chắc muốn mượn cuốn sách này?')) return;

    const result = await bookStore.borrowBook(bookId);
    
    if (result.success) {
        alert(result.message);
        // Load lại danh sách để cập nhật số tồn kho (giữ nguyên từ khóa tìm kiếm)
        await bookStore.fetchBooks({ search: searchQuery.value });
    } else {
        alert('Lỗi: ' + result.message);
    }
};

const handleToggleWishlist = async (book) => {
    await bookStore.toggleWishlist(book);
};
</script>

<style scoped>
  .h3 { margin-bottom: 0; }

  .input-group-text {
      background-color: transparent;
  }

  .form-control:focus {
      box-shadow: none;
      border-color: #86b7fe; 
  }

  .form-control:focus + .input-group-text {
      border-color: #86b7fe;
  }

  .btn-icon-only {
      border: none;
      background: transparent;
      transition: transform 0.2s;
  }
  .btn-icon-only:hover {
      transform: scale(1.2);
  }
  .btn-icon-only:focus {
      border: none;
      box-shadow: none;
  }

  button:disabled {
    cursor: not-allowed;
    opacity: 0.7;
  }
</style>