<template>
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container">
      
      <router-link to="/" class="navbar-brand">
         Quản Lý Thư Viện
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
            <router-link to="/books" class="nav-link" active-class="active">Danh mục sách</router-link>
          </li>
          <li class="nav-item">
            <router-link to="/forum" class="nav-link" active-class="active">Diễn đàn</router-link>
          </li>
        </ul>

        <ul class="navbar-nav ms-auto align-items-center">
            
            <li v-if="authStore.isLoggedIn" class="nav-item dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="avatar-circle me-2">
                        {{ userInitial }}
                    </div>
                    <span class="text-white fw-bold">{{ authStore.user?.name }}</span>
                </a>
                
                <ul class="dropdown-menu dropdown-menu-end shadow">
                    <li v-if="authStore.user?.role === 'user'">
                        <router-link to="/profile" class="dropdown-item">
                            <i class="fas fa-user me-2"></i> Hồ sơ cá nhân
                        </router-link>
                    </li>

                    <li v-if="authStore.user?.role === 'admin'">
                        <h6 class="dropdown-header text-uppercase small text-muted">Quản trị</h6>
                        
                        <router-link to="/admin/loans" class="dropdown-item">
                            <i class="me-2 text-primary"></i> Duyệt phiếu mượn
                        </router-link>

                        <router-link to="/books/create" class="dropdown-item">
                            <i class="me-2 text-success"></i> Nhập sách mới
                        </router-link>

                        <router-link to="/admin/users" class="dropdown-item">
                            <i class="me-2 text-success"></i> Quản lý User
                        </router-link>

                        <router-link to="/admin/forum/categories" class="dropdown-item">
                            <i class="me-2 text-success"></i> Quản lý chuyên mục
                        </router-link>
                    </li>

                    <li><hr class="dropdown-divider"></li>

                    <li>
                        <button @click="handleLogout" class="dropdown-item text-danger">
                            <i class="fas fa-sign-out-alt me-2"></i> Đăng xuất
                        </button>
                    </li>
                </ul>
            </li>

            <li v-else class="nav-item d-flex align-items-center gap-2">
                
                <router-link to="/login" class="btn btn-light btn-sm fw-bold text-primary">
                    Đăng nhập
                </router-link>

                <router-link to="/register" class="btn btn-light btn-sm fw-bold text-primary">
                    Đăng ký
                </router-link>

            </li>

        </ul>

      </div>
    </div>
  </nav>
</template>

<script setup>
    import { computed } from 'vue';
    import { useAuthStore } from '@/stores/authStore';
    import { useRouter } from 'vue-router';

    const authStore = useAuthStore();
    const router = useRouter();

    const userInitial = computed(() => {
        if (authStore.user && authStore.user.name) {
            return authStore.user.name.charAt(0).toUpperCase();
        }
        return 'U';
    });

    const handleLogout = async () => {
        await authStore.logout();
        router.push('/login'); 
    };
</script>

<style scoped>
    .navbar-brand {
        font-weight: bold;
        font-size: 1.4rem;
    }

    .avatar-circle {
        width: 32px;
        height: 32px;
        background-color: white;
        color: var(--bs-primary);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 1rem;
        text-transform: uppercase;
    }

    .nav-item.dropdown .nav-link {
        color: white !important;
    }
</style>