<template>
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    
    <div class="container">
      
      <router-link to="/" class="navbar-brand">
        📚 Quản Lý Thư Viện
      </router-link>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarNavDropdown">
        
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <router-link to="/" class="nav-link" active-class="active">Trang chủ</router-link>
          </li>
          <li class="nav-item">
            <router-link to="/books" class="nav-link" active-class="active">Quản lý Sách</router-link>
          </li>
          </ul>

        <ul class="navbar-nav">
           <li class="nav-item">
             <button @click="handleLogout" class="btn btn-light btn-sm">Đăng xuất</button>
           </li>
        </ul>
      </div>
    </div>
  </nav>
</template>

<script setup>
    import { useAuthStore } from '@/stores/authStore';
    import { useRouter } from 'vue-router';

    const authStore = useAuthStore();
    const router = useRouter();

    const handleLogout = async () => {
        // Gọi action logout từ store
        await authStore.logout();
    };
</script>

<style scoped>
    .navbar-brand {
    font-weight: bold;
    font-size: 1.4rem;
    }
</style>