import { defineStore } from 'pinia';
import api from '@/api/axios'; 
import router from '@/router'; 

export const useAuthStore = defineStore('authStore', {
  state: () => ({
    user: null,
    isLoggedIn: false,
    isLoading: false,
    error: null,
  }),

  actions: {
    /**
     * Lấy thông tin User
     */
    async fetchUser() {
        this.isLoading = true;
        try {
            const response = await api.get('/api/user');
            
            this.user = response.data;
            this.isLoggedIn = true;
        } catch (error) {
            this.user = null;
            this.isLoggedIn = false;
        } finally {
            this.isLoading = false;
        }
    },

    // Đăng nhập
    async login(credentials) {
      this.isLoading = true;
      this.error = null;
      try {
          await api.get('/sanctum/csrf-cookie');
          await api.post('/api/login', credentials);
          await this.fetchUser();

          return { success: true }; 

      } catch (err) {
          console.error('Lỗi đăng nhập:', err);
          const msg = err.response?.data?.message || 'Đăng nhập thất bại';
          
          this.error = msg; 

          return { success: false, message: msg }; 

      } finally {
          this.isLoading = false;
      }
  },

    // Đăng xuất
    async logout() {
      try {
        await api.post('/api/logout'); 
      } catch (error) {
        console.warn('Lỗi logout:', error);
      } finally {
        // Reset State
        this.user = null;
        this.isLoggedIn = false;
        
        // Chuyển hướng về login
        router.push('/login');
      }
    },

    // Đăng ký
    async register(userInfo) {
        this.isLoading = true;
        this.error = null;
        try {
            await api.post('/api/register', userInfo);
            
            // Lấy luôn thông tin user (Auto login sau khi register)
            await this.fetchUser();
            
            return true;
        } catch (err) {
            this.error = err.response?.data?.message || 'Đăng ký thất bại';
            return false;
        } finally {
            this.isLoading = false;
        }
    },

    // Cập nhật hồ sơ
    async updateProfile(formData) {
        this.isLoading = true;
        try {
            const response = await api.put('/api/profile', formData); 
            this.user = response.data.user; 
            return { success: true, message: 'Cập nhật thành công!' };
        } catch (err) {
            return { success: false, message: err.response?.data?.message || 'Lỗi cập nhật' };
        } finally {
            this.isLoading = false;
        }
    },
  }
});