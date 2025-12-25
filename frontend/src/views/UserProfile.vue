<template>
  <div class="container mt-5">
    <div class="row">
      <div class="col-md-3 mb-4">
        <div class="card shadow-sm">
          <div class="card-body text-center">
            <div class="avatar-placeholder bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px; font-size: 2rem;">
              {{ authStore.user?.name.charAt(0).toUpperCase() }}
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
            <h4 class="mb-4">Chỉnh sửa thông tin</h4>
            <form @submit.prevent="handleUpdateProfile">
              <div class="mb-3">
                <label class="form-label">Họ và tên</label>
                <input v-model="form.name" type="text" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Đổi mật khẩu (Bỏ trống nếu không đổi)</label>
                <input v-model="form.password" type="password" class="form-control" placeholder="Nhập mật khẩu mới">
              </div>
              <div class="mb-3">
                <label class="form-label">Nhập lại mật khẩu mới</label>
                <input v-model="form.password_confirmation" type="password" class="form-control">
              </div>
              <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
            </form>
          </div>

          <div v-if="activeTab === 'loans'">
             <h4 class="mb-4">Sách đang mượn</h4>
             <div v-if="bookStore.borrowedBooks.length === 0" class="alert alert-secondary">
                Bạn không mượn cuốn nào.
             </div>
             <table v-else class="table">
                <thead>
                    <tr><th>Sách</th><th>Hạn trả</th><th>Thao tác</th></tr>
                </thead>
                <tbody>
                    <tr v-for="loan in bookStore.borrowedBooks" :key="loan.id">
                        <td>{{ loan.book?.title }}</td>
                        <td class="text-danger">{{ new Date(loan.due_date).toLocaleDateString('vi-VN') }}</td>
                        <td>
                            <button @click="handleReturn(loan.book_id)" class="btn btn-sm btn-outline-primary">Trả sách</button>
                        </td>
                    </tr>
                </tbody>
             </table>
          </div>

          <div v-if="activeTab === 'wishlist'">
            <h4 class="mb-4">Danh sách yêu thích</h4>
            <div v-if="bookStore.wishlist.length === 0" class="text-muted">Chưa có sách yêu thích.</div>
            <div class="row g-3">
                <div v-for="book in bookStore.wishlist" :key="book.id" class="col-md-6">
                    <div class="border rounded p-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">{{ book.title }}</h6>
                            <small class="text-muted">{{ book.author }}</small>
                        </div>
                        <button @click="removeFromWishlist(book.id)" class="btn btn-sm btn-danger">
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
</template>

<script setup>
import { ref, onMounted } from 'vue';
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

onMounted(() => {
  // Load dữ liệu khi vào trang
  bookStore.fetchBorrowedBooks();
  bookStore.fetchWishlist();
});

const handleUpdateProfile = async () => {
    const result = await authStore.updateProfile(form.value);
    alert(result.message);
    if(result.success) {
        form.value.password = '';
        form.value.password_confirmation = '';
    }
};

const handleReturn = async (bookId) => {
    if(confirm('Trả sách nhé?')) {
        await bookStore.returnBook(bookId);
    }
};

const removeFromWishlist = async (bookId) => {
    if(confirm('Bỏ sách này khỏi yêu thích?')) {
        await bookStore.toggleWishlist(bookId);
    }
};

const handleLogout = () => {
    authStore.logout();
    router.push('/login');
};
</script>