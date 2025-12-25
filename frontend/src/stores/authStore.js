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
     * Xử lý Đăng nhập (
     */
    async login(credentials) {
      this.isLoading = true;
      this.error = null; // Reset lỗi cũ
      try {
        // Lấy CSRF cookie 
        await api.get('http://localhost:8000/sanctum/csrf-cookie'); 

        // Gửi yêu cầu đăng nhập
        const response = await api.post('/login', credentials);

        // Lưu Token nhận được
        const token = response.data.access_token || response.data.token;
        
        if (token) {
            this.token = token;
            localStorage.setItem('token', token);
            api.defaults.headers.common['Authorization'] = `Bearer ${token}`;
        } else {
            console.warn("Server không trả về Token!");
        }

        // 4. Lấy thông tin User
        await this.fetchUser();

        return true; // Báo thành công
      } catch (err) {
        console.error('Lỗi đăng nhập:', err);
        this.error = err.response?.data?.message || 'Đăng nhập thất bại';
        return false; // Báo thất bại
      } finally {
        this.isLoading = false;
      }
    },

    /**
     * Xử lý Đăng xuất
     */
    async logout() {
      try {
        // Gọi API để Server hủy token
        await api.post('/logout');
      } catch (error) {
        console.warn('Lỗi gọi API logout:', error);
      } finally {
        // Dọn dẹp dữ liệu ở Frontend  
        this.user = null;
        this.token = null;
        this.isLoggedIn = false;
        
        // Xóa sạch Token trong kho
        localStorage.removeItem('token'); 
        
        // Xóa header Authorization của Axios
        delete api.defaults.headers.common['Authorization'];

        // Chuyển hướng về trang đăng nhập
        router.push('/login');
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
    },
  }
});