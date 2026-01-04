<template>
  <div class="container mt-4">
    <div class="card shadow-sm">
      <div class="card-header text-primary d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-folder-open me-2"></i>Quản Lý Chuyên Mục Diễn Đàn</h5>
      </div>
      
      <div class="card-body">
        <form @submit.prevent="handleAdd" class="row g-3 mb-4 border-bottom pb-4">
            <div class="col-md-6">
                <input v-model="form.name" type="text" class="form-control" placeholder="Tên chuyên mục (VD: Thông báo)" required>
            </div>
            <div class="col-md-6 d-flex">
                <input v-model="form.description" type="text" class="form-control me-2" placeholder="Mô tả ngắn...">
                <button class="btn btn-success text-nowrap"><i class="fas fa-plus"></i> Thêm</button>
            </div>
        </form>

        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Tên chuyên mục</th>
                    <th>Mô tả</th>
                    <th class="text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="cat in forumStore.categories" :key="cat.id">
                    <td>{{ cat.id }}</td>
                    <td class="fw-bold text-primary">{{ cat.name }}</td>
                    <td class="text-muted">{{ cat.description }}</td>
                    <td class="text-center">
                        <button @click="handleDelete(cat)" class="btn btn-sm btn-outline-danger">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { useForumStore } from '@/stores/forumStore';
import forumService from '@/services/forumService';

const forumStore = useForumStore();
const form = reactive({ name: '', description: '' });

onMounted(() => {
    forumStore.fetchCategories();
});

const handleAdd = async () => {
    try {
        await forumService.createCategory(form);
        alert('Thêm chuyên mục thành công!');
        forumStore.fetchCategories(); // Load lại danh sách
        form.name = ''; form.description = ''; // Reset form
    } catch (error) {
        alert('Lỗi: ' + (error.response?.data?.message || 'Không thể thêm'));
    }
};

const handleDelete = async (cat) => {
    if(confirm(`Cảnh báo: Xóa chuyên mục "${cat.name}" sẽ xóa TẤT CẢ bài viết trong đó. Bạn có chắc không?`)) {
        try {
            await forumStore.removeCategory(cat.id);
            alert('Đã xóa thành công!');
        } catch (error) {
            alert('Lỗi khi xóa.');
        }
    }
};
</script>