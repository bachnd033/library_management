import './assets/main.css'
import { createApp } from 'vue'
import { createPinia } from 'pinia'

import App from './App.vue'
import router from './router'
import 'bootstrap/dist/css/bootstrap.min.css'

// Import store 
import { useAuthStore } from './stores/authStore'

const app = createApp(App)

// Cài đặt Pinia 
app.use(createPinia())
// Cài đặt Router
app.use(router)

/**
 * -----------------------------------------------------------------
 * NAVIGATION GUARD (Bảo vệ Route)
 * -----------------------------------------------------------------
 */
router.beforeEach(async (to, from, next) => {
  // Gọi useAuthStore() BÊN TRONG guard
  const authStore = useAuthStore();

  // Cố gắng fetch user nếu state chưa được tải (cho trường hợp F5)
  if (authStore.user === null) {
      try {
          await authStore.fetchUser(); // Chờ lấy thông tin user
      } catch (error) {
          // Lỗi (ví dụ: session hết hạn), không sao, isLoggedIn vẫn là false
      }
  }

  // Kiểm tra quyền truy cập
  const requiresAuth = to.meta.requiresAuth;

  if (requiresAuth && !authStore.isLoggedIn) {
    // Nếu route yêu cầu đăng nhập VÀ user chưa đăng nhập
    next('/login'); // Chuyển hướng về trang login
  } else if (to.path === '/login' && authStore.isLoggedIn) {
    // (Tùy chọn): Nếu đã đăng nhập, không cho vào trang login nữa
    next('/books'); // Chuyển về trang books
  } else {
    // Cho phép truy cập
    next();
  }
});

app.mount('#app')