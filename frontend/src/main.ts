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

router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore();

  // Khôi phục trạng thái đăng nhập khi F5 trang
  // Nếu Store chưa có User, gọi API lấy User
  if (!authStore.user) {
      try {
          await authStore.fetchUser();
      } catch (e) {
          console.error("Lỗi khi khôi phục trạng thái đăng nhập:", e);
      }
  }

  // Kiểm tra quyền truy cập dựa trên kết quả bước 1
  const isAuthenticated = !!authStore.user; 

  // Trang yêu cầu đăng nhập 
  if (to.meta.requiresAuth && !isAuthenticated) {
    console.warn("CHẶN: Chưa đăng nhập, đá về Login");
    return next('/login');
  }

  // Trang chỉ dành cho khách 
  if (to.meta.guestOnly && isAuthenticated) {
    console.warn("CHẶN: Đã đăng nhập rồi, đá về Home");
    return next('/'); 
  }

  next();
});

app.use(router)
app.mount('#app')