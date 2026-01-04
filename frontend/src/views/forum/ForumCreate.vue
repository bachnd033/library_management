<template>
  <div class="container mt-4">
    <div class="card shadow p-4" style="max-width: 800px; margin: 0 auto;">
        <h3 class="mb-4 text-center">Tạo Chủ Đề Mới</h3>
        
        <form @submit.prevent="handleCreate">
            <div class="mb-3">
                <label class="form-label fw-bold">Tiêu đề bài viết</label>
                <input v-model="form.title" type="text" class="form-control" required placeholder="Nhập tiêu đề ngắn gọn...">
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Chuyên mục</label>
                <select v-model="form.forum_category_id" class="form-select" required>
                    <option disabled value="">-- Chọn chuyên mục --</option>
                    <option v-for="cat in forumStore.categories" :key="cat.id" :value="cat.id">
                        {{ cat.name }}
                    </option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Nội dung</label>
                <textarea v-model="form.content" class="form-control" rows="8" required placeholder="Nội dung chi tiết..."></textarea>
            </div>

            <div class="d-flex justify-content-between">
                <router-link to="/forum" class="btn btn-secondary">Hủy bỏ</router-link>
                <button type="submit" class="btn btn-primary px-5">Đăng bài</button>
            </div>
        </form>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useForumStore } from '@/stores/forumStore';
import forumService from '@/services/forumService';

const forumStore = useForumStore();
const router = useRouter();

const form = reactive({
    title: '',
    forum_category_id: '',
    content: ''
});

onMounted(() => {
    if (forumStore.categories.length === 0) {
        forumStore.fetchCategories();
    }
});

const handleCreate = async () => {
    try {
        await forumService.createPost(form);
        alert('Đăng bài thành công!');
        router.push('/forum'); // Quay về trang chủ diễn đàn
    } catch (error) {
        alert('Lỗi: ' + (error.response?.data?.message || 'Không thể đăng bài'));
    }
};
</script>