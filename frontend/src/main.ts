import './assets/main.css'
import { createApp } from 'vue'
import { createPinia } from 'pinia'

import App from './App.vue'
import router from './router'
import 'bootstrap/dist/css/bootstrap.min.css'

// Import store 
import { useAuthStore } from './stores/authStore'

const app = createApp(App)

app.use(createPinia())
app.use(router)

/**
 * -----------------------------------------------------------------
 * NAVIGATION GUARD (Bảo vệ Route)
 * Logic: Chặn ngay từ cửa nếu không có vé (Token)
 * -----------------------------------------------------------------
 */
router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore();
  
  // Lấy Token trực tiếp từ kho
  const token = localStorage.getItem('token');
  
  // Xác định xem trang này có cần bảo vệ không
  const requiresAuth = to.meta.requiresAuth;
  const isGuestOnly = to.meta.guestOnly; 

  if (requiresAuth && !token) {
    console.warn("🚫 CHẶN: Không có token, đá về Login");
    return next('/login'); // Dừng ngay lập tức, chuyển hướng
  }

  if (isGuestOnly && token) {
    console.warn("🚫 CHẶN: Đã đăng nhập, đá về Home");
    return next('/'); 
  }

  if (token && !authStore.user) {
    try {
      await authStore.fetchUser();
    } catch (e) {
      // Nếu token hết hạn hoặc lỗi -> Xóa token và bắt đăng nhập lại
      console.error("Token lỗi, đăng xuất...");
      authStore.logout(); 
      return next('/login');
    }
  }

  // Nếu không vi phạm gì cả -> Cho đi tiếp
  next();
});

app.mount('#app')