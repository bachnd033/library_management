<template>
  <div class="container mt-5">
    <div class="row">
      <div class="col-md-3 mb-4">
        <div class="card shadow-sm">
          <div class="card-body text-center">
            <div class="avatar-placeholder bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" 
                 style="width: 80px; height: 80px; font-size: 2rem; font-weight: bold;">
              {{ userInitial }}
            </div>
            <h5 class="card-title">{{ authStore.user?.name }}</h5>
            <p class="text-muted small">{{ authStore.user?.email }}</p>
          </div>
          <div class="list-group list-group-flush">
            <button @click="activeTab = 'info'" :class="['list-group-item list-group-item-action', activeTab === 'info' ? 'active' : '']">
              <i class="fas fa-user-edit me-2"></i> Thông tin cá nhân
            </button>
            <button @click="activeTab = 'loans'" :class="['list-group-item list-group-item-action', activeTab === 'loans' ? 'active' : '']">
              <i class="fas fa-book-reader me-2"></i> Sách đang mượn
            </button>
            <button @click="activeTab = 'wishlist'" :class="['list-group-item list-group-item-action', activeTab === 'wishlist' ? 'active' : '']">
              <i class="fas fa-heart me-2"></i> Sách yêu thích
            </button>
             <button @click="handleLogout" class="list-group-item list-group-item-action text-danger">
              <i class="fas fa-sign-out-alt me-2"></i> Đăng xuất
            </button>
          </div>
        </div>
      </div>

      <div class="col-md-9">
        <div class="card shadow-sm p-4">
          
          <div v-if="activeTab === 'info'">
            <h4 class="mb-4 text-primary border-bottom pb-2">Chỉnh sửa thông tin</h4>
            <form @submit.prevent="handleUpdateProfile">
              <div class="mb-3">
                <label class="form-label fw-bold">Họ và tên</label>
                <input v-model="form.name" type="text" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label fw-bold">Đổi mật khẩu <span class="text-muted fw-normal">(Bỏ trống nếu không đổi)</span></label>
                <input v-model="form.password" type="password" class="form-control" placeholder="Nhập mật khẩu mới">
              </div>
              <div class="mb-3">
                <label class="form-label fw-bold">Nhập lại mật khẩu mới</label>
                <input v-model="form.password_confirmation" type="password" class="form-control">
              </div>
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i> Lưu thay đổi
              </button>
            </form>
          </div>

          <div v-if="activeTab === 'loans'">
             <h4 class="mb-4 text-primary border-bottom pb-2">Sách đang mượn</h4>
             
             <div v-if="bookStore.isLoading" class="text-center py-3">
                <div class="spinner-border text-primary" role="status"></div>
             </div>

             <div v-else-if="bookStore.borrowedBooks.length === 0" class="alert alert-light text-center py-4 border">
                <i class="fas fa-box-open fa-2x mb-3 text-muted"></i>
                <p>Bạn chưa có lịch sử mượn sách nào.</p>
                <router-link to="/books" class="btn btn-outline-primary btn-sm">Tìm sách ngay</router-link>
             </div>

             <div v-else class="table-responsive">
               <table class="table table-hover align-middle">
                  <thead class="table-light">
                      <tr>
                        <th>Sách</th>
                        <th>Trạng thái</th> <th>Hạn trả</th>
                        <th class="text-end">Thao tác</th>
                      </tr>
                  </thead>
                  <tbody>
                      <tr v-for="loan in bookStore.borrowedBooks" :key="loan.id">
                          <td class="fw-medium">{{ loan.book?.title || 'Sách đã bị xóa' }}</td>
                          <td>
                              <span v-if="loan.status === 'pending'" class="badge bg-warning text-dark">Chờ duyệt</span>
                              <span v-else-if="loan.status === 'approved'" class="badge bg-success">Đang mượn</span>
                              <span v-else-if="loan.status === 'returned'" class="badge bg-secondary">Đã trả</span>
                              <span v-else class="badge bg-danger">Đã hủy</span>
                          </td>
                          <td>
                            <span v-if="loan.status === 'returned'" class="text-muted small">
                                Trả lúc: {{ formatDate(loan.returned_at) }}
                            </span>
                            <span v-else :class="isOverdue(loan.due_date) ? 'text-danger fw-bold' : ''">
                                {{ formatDate(loan.due_date) }}
                            </span>
                          </td>
                          <td class="text-end">
                              <button 
                                  v-if="loan.status === 'approved'"
                                  @click="handleReturn(loan.book_id)" 
                                  class="btn btn-outline-primary btn-sm"
                              >
                                  Trả sách
                              </button>
                          </td>
                      </tr>
                  </tbody>
               </table>
             </div>
          </div>

          <div v-if="activeTab === 'wishlist'">
            <h4 class="mb-4 text-primary border-bottom pb-2">Danh sách yêu thích</h4>
            <div v-if="bookStore.wishlist.length === 0" class="text-center text-muted py-4">
                Chưa có sách yêu thích.
            </div>
            
            <div class="d-flex flex-column gap-3">
                <div v-for="book in bookStore.wishlist" :key="book.id" class="border rounded p-3 shadow-sm bg-white">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div class="flex-grow-1">
                            <h6 class="mb-1 fw-bold text-dark fs-5">{{ book.title }}</h6>
                            <small class="text-muted d-block"><i class="fas fa-user-edit me-1"></i>{{ book.author }}</small>
                            <span class="badge bg-light text-secondary border mt-2">{{ book.category }}</span>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <button @click="viewDetails(book.id)" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye me-1"></i> Chi tiết
                            </button>
                            
                            <button 
                                @click="handleBorrow(book)" 
                                class="btn btn-sm btn-success text-white"
                                :disabled="book.available_copies < 1"
                            >
                                <i class="fas fa-book-reader me-1"></i> 
                                {{ book.available_copies < 1 ? 'Hết hàng' : 'Mượn' }}
                            </button>

                            <button @click="removeFromWishlist(book)" class="btn btn-sm btn-outline-danger" title="Xóa khỏi yêu thích">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

          </div>

        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useAuthStore } from '@/stores/authStore';
import { useBookStore } from '@/stores/bookStore';
import { useRouter } from 'vue-router'; 

const authStore = useAuthStore();
const bookStore = useBookStore();
const router = useRouter(); 
const activeTab = ref('info'); 

// Form dữ liệu cho edit profile
const form = ref({
  name: authStore.user?.name || '',
  password: '',
  password_confirmation: ''
});

const userInitial = computed(() => {
    return authStore.user?.name ? authStore.user.name.charAt(0).toUpperCase() : 'U';
});

onMounted(async () => {
    await bookStore.fetchBorrowedBooks();
    await bookStore.fetchWishlist();
});

const formatDate = (dateString) => {
    if (!dateString) return '';
    return new Date(dateString).toLocaleDateString('vi-VN');
};

const isOverdue = (dateString) => {
    if (!dateString) return false;
    return new Date(dateString) < new Date();
};

const handleUpdateProfile = async () => {
    const result = await authStore.updateProfile(form.value);
    alert(result.message);
    if(result.success) {
        form.value.password = '';
        form.value.password_confirmation = '';
    }
};

const handleReturn = async (bookId) => {
    if(confirm('Bạn có chắc muốn trả cuốn sách này không?')) {
        const result = await bookStore.returnBook(bookId);
        if(result.message) alert(result.message);
        if(result.success) await bookStore.fetchBorrowedBooks(); // Refresh list
    }
};

const removeFromWishlist = async (book) => {
    if(confirm(`Bỏ sách "${book.title}" khỏi yêu thích?`)) {
        await bookStore.toggleWishlist(book);
    }
};

// Xem chi tiết
const viewDetails = (bookId) => {
    router.push(`/books/${bookId}`);
};

// Mượn sách từ Wishlist
const handleBorrow = async (book) => {
    if(confirm(`Bạn muốn đăng ký mượn cuốn "${book.title}"?`)) {
        const result = await bookStore.borrowBook(book.id);
        alert(result.message);
        // Sau khi mượn, có thể cần load lại sách đang mượn
        if(result.success) {
             await bookStore.fetchBorrowedBooks();
             // Chuyển sang tab mượn sách để người dùng thấy kết quả
             activeTab.value = 'loans';
        }
    }
};

const handleLogout = async () => {
    await authStore.logout();
    router.push('/login');
};
</script>