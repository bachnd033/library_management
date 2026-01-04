<template>
  <div class="container mt-4">
    <h2 class="mb-4 text-primary fw-bold"><i class="fas fa-chart-line me-2"></i>Bảng Thống Kê Admin</h2>

    <div v-if="store.isLoading" class="text-center py-5">
        <div class="spinner-border text-primary"></div>
    </div>

    <div v-else>
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white fw-bold">
                <i class="fas fa-book me-2"></i>Số Liệu Thư Viện
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="p-3 border rounded bg-light border-primary border-start-5">
                            <h6 class="text-muted text-uppercase">Tổng đầu sách</h6>
                            <h3 class="fw-bold text-primary">{{ store.library.total_books }}</h3>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 border rounded bg-light border-success border-start-5">
                            <h6 class="text-muted text-uppercase">Thành viên</h6>
                            <h3 class="fw-bold text-success">{{ store.library.total_users }}</h3>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 border rounded bg-light border-info border-start-5">
                            <h6 class="text-muted text-uppercase">Đang mượn</h6>
                            <h3 class="fw-bold text-info">{{ store.library.active_loans }}</h3>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 border rounded bg-light border-danger border-start-5">
                            <h6 class="text-danger text-uppercase">Sách quá hạn</h6>
                            <h3 class="fw-bold text-danger">{{ store.library.overdue_loans }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-warning text-dark fw-bold">
                <i class="fas fa-comments me-2"></i>Số Liệu Diễn Đàn
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="d-flex align-items-center">
                            <div class="bg-warning bg-opacity-25 p-3 rounded-circle me-3">
                                <i class="fas fa-file-alt fa-2x text-warning"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-0">Bài viết</h6>
                                <h4 class="fw-bold mb-0">{{ store.forum.total_posts }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-center">
                            <div class="bg-info bg-opacity-25 p-3 rounded-circle me-3">
                                <i class="fas fa-comment-dots fa-2x text-info"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-0">Bình luận</h6>
                                <h4 class="fw-bold mb-0">{{ store.forum.total_comments }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-center">
                            <div class="bg-secondary bg-opacity-25 p-3 rounded-circle me-3">
                                <i class="fas fa-eye fa-2x text-secondary"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-0">Lượt xem toàn trang</h6>
                                <h4 class="fw-bold mb-0">{{ store.forum.total_views }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-white fw-bold">Thành viên mới đăng ký</div>
                    <ul class="list-group list-group-flush">
                        <li v-for="user in store.newestUsers" :key="user.id" class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <span class="fw-bold">{{ user.name }}</span>
                                <br><small class="text-muted">{{ user.email }}</small>
                            </div>
                            <small class="text-secondary">{{ formatDate(user.created_at) }}</small>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-white fw-bold">Bài viết diễn đàn mới</div>
                    <ul class="list-group list-group-flush">
                        <li v-for="post in store.recentPosts" :key="post.id" class="list-group-item">
                            <router-link :to="'/forum/' + post.id" class="text-decoration-none text-dark fw-bold">
                                {{ post.title }}
                            </router-link>
                            <div class="d-flex justify-content-between mt-1">
                                <small class="text-muted">Bởi: {{ post.user?.name }}</small>
                                <small class="text-secondary">{{ formatDate(post.created_at) }}</small>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { useDashboardStore } from '@/stores/dashboardStore';

const store = useDashboardStore();

onMounted(() => {
    store.fetchStats();
});

const formatDate = (date) => new Date(date).toLocaleDateString('vi-VN');
</script>

<style scoped>
    .border-start-5 { border-left-width: 5px !important; }
</style>