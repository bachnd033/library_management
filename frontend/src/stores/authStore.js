import { defineStore } from 'pinia';
import api from '@/api/axios'; 
import router from '@/router'; 
import { AuthService } from '@/services/AuthService';

export const useAuthStore = defineStore('authStore', {
  state: () => ({
    user: null, // Ban đầu chưa có user
    isLoggedIn: false,
  }),

  actions: {
    /**
     * Lấy thông tin user nếu đã đăng nhập
     */
    async fetchUser() {
      // Kiểm tra xem có token trong localStorage không
        const token = localStorage.getItem('token');
         
        if (!token) {
            this.user = null;
            this.isLoggedIn = false;
            return; 
        }

        // Có token mới gọi API
        this.isLoading = true;
        try {
            // Đảm bảo token được gắn vào request hiện tại
            api.defaults.headers.common['Authorization'] = `Bearer ${token}`;
            
            const response = await api.get('/user');
            this.user = response.data;
            this.isLoggedIn = true;
        } catch (error) {
            // Nếu có token nhưng gọi API vẫn lỗi (Token hết hạn/Fake token)
            console.warn("Token không hợp lệ hoặc đã hết hạn.");
            
            // Xóa token bẩn đi để lần sau không gọi nữa
            this.user = null;
            this.isLoggedIn = false;
            localStorage.removeItem('token'); 
            
            // Xóa header cũ
            delete api.defaults.headers.common['Authorization'];
        } finally {
            this.isLoading = false;
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
    },

    async register(userInfo) {
        this.isLoading = true;
        this.error = null;
        try {
            // Gọi API đăng ký
            const response = await AuthService.register(userInfo);
            
            this.token = response.data.access_token;
            this.user = response.data.user;
            
            localStorage.setItem('token', this.token);
            
            // Cấu hình lại header cho axios
            api.defaults.headers.common['Authorization'] = `Bearer ${this.token}`;
            
            return true;
        } catch (err) {
            this.error = err.response?.data?.message || 'Đăng ký thất bại';
            return false;
        } finally {
            this.isLoading = false;
        }
    }
  }
});