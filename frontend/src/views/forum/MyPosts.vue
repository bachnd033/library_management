<template>
  <div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="text-primary"><i class="fas fa-user-edit me-2"></i>Bài Viết Của Tôi</h3>
        <router-link to="/forum" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại diễn đàn
        </router-link>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div v-if="forumStore.isLoading" class="text-center py-5">
                <div class="spinner-border text-primary"></div>
            </div>

            <div v-else-if="forumStore.myPosts.length === 0" class="text-center py-5">
                <p class="text-muted">Bạn chưa đăng bài viết nào.</p>
                <router-link to="/forum/create" class="btn btn-primary">Đăng bài ngay</router-link>
            </div>

            <table v-else class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Tiêu đề</th>
                        <th>Chuyên mục</th>
                        <th>Thống kê</th>
                        <th>Ngày đăng</th>
                        <th class="text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="post in forumStore.myPosts" :key="post.id">
                        <td>
                            <router-link :to="'/forum/' + post.id" class="fw-bold text-decoration-none text-dark">
                                {{ post.title }}
                            </router-link>
                            <span v-if="post.is_pinned" class="badge bg-danger ms-1">Ghim</span>
                        </td>
                        <td><span class="badge bg-light text-dark border">{{ post.category?.name }}</span></td>
                        <td>
                            <small class="text-muted">
                                <i class="fas fa-eye me-1"></i> {{ post.views }}
                                <span class="mx-2">|</span>
                                <i class="fas fa-comment me-1"></i> {{ post.comments_count }}
                            </small>
                        </td>
                        <td>{{ formatDate(post.created_at) }}</td>
                        <td class="text-center">
                            <div class="btn-group">
                                <router-link :to="'/forum/' + post.id" class="btn btn-sm btn-outline-primary" title="Xem">
                                    <i class="fas fa-eye"></i>
                                </router-link>
                                <button @click="handleDelete(post)" class="btn btn-sm btn-outline-danger" title="Xóa">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="d-flex justify-content-center mt-3" v-if="forumStore.pagination.last_page > 1">
                <button class="btn btn-sm btn-outline-secondary me-2" 
                    :disabled="forumStore.pagination.current_page === 1" 
                    @click="changePage(forumStore.pagination.current_page - 1)">Trước</button>
                <button class="btn btn-sm btn-outline-secondary" 
                    :disabled="forumStore.pagination.current_page === forumStore.pagination.last_page"
                    @click="changePage(forumStore.pagination.current_page + 1)">Sau</button>
            </div>
        </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { useForumStore } from '@/stores/forumStore';

const forumStore = useForumStore();

onMounted(() => {
    forumStore.fetchMyPosts();
});

const handleDelete = async (post) => {
    if(confirm(`Bạn có chắc chắn muốn xóa bài viết "${post.title}"?\nHành động này sẽ xóa luôn các bình luận bên trong.`)) {
        try {
            await forumStore.removePost(post.id);
            alert('Đã xóa bài viết.');
        } catch (error) {
            alert('Lỗi khi xóa.');
        }
    }
};

const changePage = (page) => forumStore.fetchMyPosts({ page });
const formatDate = (date) => new Date(date).toLocaleDateString('vi-VN');
</script>