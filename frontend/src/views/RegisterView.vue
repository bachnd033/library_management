<template>
  <div class="register-container d-flex justify-content-center align-items-center vh-100 bg-light">
    <div class="card shadow p-4" style="max-width: 400px; width: 100%;">
      <h3 class="text-center mb-4">Đăng Ký Tài Khoản</h3>
      
      <div v-if="authStore.error" class="alert alert-danger">{{ authStore.error }}</div>

      <form @submit.prevent="handleRegister">
        <div class="mb-3">
          <label class="form-label">Họ và tên</label>
          <input v-model="form.name" type="text" class="form-control" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Email</label>
          <input v-model="form.email" type="email" class="form-control" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Mật khẩu</label>
          <input v-model="form.password" type="password" class="form-control" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Nhập lại mật khẩu</label>
          <input v-model="form.password_confirmation" type="password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-success w-100" :disabled="authStore.isLoading">
          {{ authStore.isLoading ? 'Đang xử lý...' : 'Đăng Ký' }}
        </button>
      </form>
      
      <div class="mt-3 text-center">
        <p>Đã có tài khoản? <router-link to="/login">Đăng nhập ngay</router-link></p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useAuthStore } from '@/stores/authStore';
import { useRouter } from 'vue-router';

const authStore = useAuthStore();
const router = useRouter();

const form = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: ''
});

const handleRegister = async () => {
  const success = await authStore.register(form.value);
  if (success) {
    alert('Đăng ký thành công!');
    router.push('/'); // Chuyển về trang chủ
  }
};
</script>