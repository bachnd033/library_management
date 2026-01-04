<template>
  <div class="container mt-4">
    <div class="card shadow-sm p-4">
      <header class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
        <h1 class="h3 text-primary">Quản Lý Người Dùng</h1>
        <small class="text-muted">Tổng: {{ userStore.pagination.total }}</small>
      </header>

      <div class="row mb-4">
        <div class="col-md-6 mx-auto">
             <div class="input-group">
                <input v-model="searchQuery" @keyup.enter="handleSearch" type="text" class="form-control" placeholder="Tìm kiếm...">
                <button @click="handleSearch" class="btn btn-primary">Tìm</button>
             </div>
        </div>
      </div>

      <div v-if="userStore.isLoading" class="text-center py-5"><div class="spinner-border text-primary"></div></div>
      <div v-else-if="userStore.error" class="alert alert-danger">{{ userStore.error }}</div>

      <div v-else class="table-responsive">
        <table class="table table-hover align-middle">
          <thead class="table-light">
            <tr><th>ID</th><th>Tên</th><th>Email</th><th>Vai trò</th><th>Thao tác</th></tr>
          </thead>
          <tbody>
            <tr v-if="userStore.users.length === 0"><td colspan="5" class="text-center">Không có dữ liệu</td></tr>
            <tr v-for="user in userStore.users" :key="user.id">
              <td>#{{ user.id }}</td>
              <td>
                  {{ user.name ? user.name : 'Không tên' }}
              </td>
              <td>{{ user.email }}</td>
              <td>
                 <span :class="user.role === 'admin' ? 'badge bg-danger' : 'badge bg-success'">
                    {{ user.role === 'admin' ? 'Admin' : 'User' }}
                 </span>
              </td>
              <td>
                <div v-if="authStore.user?.id !== user.id" class="btn-group">
                    <button v-if="user.role !== 'admin'" @click="changeRole(user, 'admin')" class="btn btn-sm btn-outline-danger">Lên Admin</button>
                    <button v-else @click="changeRole(user, 'user')" class="btn btn-sm btn-outline-success">Xuống User</button>
                    <button @click="handleDelete(user)" class="btn btn-sm btn-danger ms-2"><i class="fas fa-trash"></i></button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <div class="d-flex justify-content-center mt-3" v-if="userStore.pagination.last_page > 1">
          <button class="btn btn-sm btn-outline-secondary me-2" 
            :disabled="userStore.pagination.current_page === 1"
            @click="changePage(userStore.pagination.current_page - 1)">Trước</button>
          
          <span class="align-self-center">Trang {{ userStore.pagination.current_page }}</span>
          
          <button class="btn btn-sm btn-outline-secondary ms-2"
            :disabled="userStore.pagination.current_page === userStore.pagination.last_page"
            @click="changePage(userStore.pagination.current_page + 1)">Sau</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useUserStore } from '@/stores/userStore';
import { useAuthStore } from '@/stores/authStore';

const userStore = useUserStore();
const authStore = useAuthStore();
const searchQuery = ref('');

onMounted(() => {
    userStore.fetchUsers();
});

const handleSearch = () => userStore.fetchUsers({ search: searchQuery.value, page: 1 });
const changePage = (page) => userStore.fetchUsers({ search: searchQuery.value, page });

const changeRole = async (user, role) => {
    if(confirm(`Đổi quyền "${user.name}" thành ${role}?`)) {
        const res = await userStore.updateUserRole(user.id, role);
        if(!res.success) alert(res.message);
    }
};

const handleDelete = async (user) => {
    if(confirm(`Xóa user "${user.name}"?`)) {
        const res = await userStore.deleteUser(user.id);
        if(!res.success) alert(res.message);
    }
};
</script>