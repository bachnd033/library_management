import { defineStore } from 'pinia';
import api from '@/api/axios'; // Import Axios instance
import router from '@/router'; // Import router

export const useAuthStore = defineStore('authStore', {
  state: () => ({
    user: null, // Ban đầu chưa có user
    isLoggedIn: false,
  }),

  actions: {
    /**
     * Lấy thông tin user (thường gọi khi tải lại trang)
     */
    async fetchUser() {
      try {
        const response = await api.get('/user');
        this.user = response.data;
        this.isLoggedIn = true;
      } catch (error) {
        this.user = null;
        this.isLoggedIn = false;
      }
    },

    /**
     * Xử lý Đăng nhập (Gồm 2 bước của Sanctum)
     */
    async login(credentials) {
      // 1. Lấy CSRF cookie (Bắt buộc cho Sanctum)
      await api.get('http://localhost:8000/sanctum/csrf-cookie'); 

      // 2. Gửi yêu cầu đăng nhập
      await api.post('/login', credentials);

      // 3. Đăng nhập thành công, lấy thông tin user
      await this.fetchUser();
    },

    /**
     * Xử lý Đăng xuất
     */
    async logout() {
      try {
        await api.post('/logout');
        this.user = null;
        this.isLoggedIn = false;
        // Chuyển về trang đăng nhập
        router.push('/login');
      } catch (error) {
        console.error('Lỗi khi đăng xuất:', error);
      }
    }
  }
});