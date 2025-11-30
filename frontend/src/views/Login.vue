<template>
  <div class="login-container d-flex justify-content-center align-items-center">
    <div class="card shadow-sm p-4" style="width: 400px;">
      <h1 class="h3 mb-3 text-center">Đăng Nhập</h1>
      
      <form @submit.prevent="handleLogin">
        
        <div v-if="error" class="alert alert-danger">
          {{ error }}
        </div>

        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input type="email" v-model="email" class="form-control" id="email" required>
        </div>
        
        <div class="mb-3">
          <label for="password" class="form-label">Mật khẩu</label>
          <input type="password" v-model="password" class="form-control" id="password" required>
        </div>
        
        <div class="d-grid">
          <button type="submit" class="btn btn-primary" :disabled="isLoading">
            <span v-if="isLoading" class="spinner-border spinner-border-sm" role="status"></span>
            Đăng nhập
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/authStore';

const router = useRouter();
const authStore = useAuthStore();

const email = ref('');
const password = ref('');
const isLoading = ref(false);
const error = ref(null);

const handleLogin = async () => {
  isLoading.value = true;
  error.value = null;
  
  try {
    // Gọi action 'login' từ authStore
    await authStore.login({
      email: email.value,
      password: password.value,
    });
    
    // Đăng nhập thành công, chuyển hướng đến trang Quản lý Sách
    router.push('/books');

  } catch (err) {
    isLoading.value = false;
    // Xử lý lỗi
    if (err.response && err.response.status === 422) {
      error.value = err.response.data.errors.email[0];
    } else {
      error.value = 'Đã xảy ra lỗi. Vui lòng thử lại.';
    }
  }
};
</script>

<style scoped>
    .login-container {
    min-height: 80vh; 
    }
</style>