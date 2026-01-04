<template>
  <div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary fw-bold"><i class="fas fa-chart-line me-2"></i>Thống Kê Của Tôi</h2>
        <span class="text-muted">Xin chào, {{ authStore.user?.name }}!</span>
    </div>

    <div v-if="store.isLoading" class="text-center py-5">
        <div class="spinner-border text-primary"></div>
    </div>

    <div v-else>
        <h5 class="mb-3 text-secondary border-bottom pb-2">Hoạt động Thư viện</h5>
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card bg-info text-white shadow-sm h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-book-reader fa-2x mb-2"></i>
                        <h5>Đang mượn</h5>
                        <h2 class="fw-bold">{{ store.userStats.library.borrowing || 0 }}</h2>
                        <small>Cuốn sách</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white shadow-sm h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-check-circle fa-2x mb-2"></i>
                        <h5>Đã trả</h5>
                        <h2 class="fw-bold">{{ store.userStats.library.returned || 0 }}</h2>
                        <small>Lịch sử đọc tốt!</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white shadow-sm h-100" 
                     :class="store.userStats.library.overdue > 0 ? 'bg-danger' : 'bg-secondary'">
                    <div class="card-body text-center">
                        <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                        <h5>Quá hạn</h5>
                        <h2 class="fw-bold">{{ store.userStats.library.overdue || 0 }}</h2>
                        <small v-if="store.userStats.library.overdue > 0">Vui lòng trả gấp!</small>
                        <small v-else>Không có sách quá hạn</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-5" v-if="store.userStats.current_loans?.length > 0">
            <div class="card-header bg-white fw-bold text-primary">
                <i class="fas fa-clock me-2"></i>Sách cần trả (Sắp xếp theo hạn trả)
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Tên sách</th>
                            <th>Ngày mượn</th>
                            <th>Hạn trả</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="loan in store.userStats.current_loans" :key="loan.id">
                            <td class="fw-bold">{{ loan.book?.title }}</td>
                            <td>{{ formatDate(loan.loan_date) }}</td>
                            <td :class="isLate(loan.due_date) ? 'text-danger fw-bold' : 'text-dark'">
                                {{ formatDate(loan.due_date) }}
                            </td>
                            <td>
                                <span v-if="loan.status === 'overdue'" class="badge bg-danger">Quá hạn</span>
                                <span v-else class="badge bg-info">Đang mượn</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <h5 class="mb-3 text-secondary border-bottom pb-2">Đóng góp Diễn đàn</h5>
        <div class="row g-3">
            <div class="col-md-4">
                <div class="card border-primary h-100 shadow-sm">
                    <div class="card-body d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 p-3 rounded me-3 text-primary">
                            <i class="fas fa-pen-nib fa-2x"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0">Bài viết đã đăng</h6>
                            <h3 class="fw-bold mb-0">{{ store.userStats.forum.my_posts || 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-warning h-100 shadow-sm">
                    <div class="card-body d-flex align-items-center">
                        <div class="bg-warning bg-opacity-10 p-3 rounded me-3 text-warning">
                            <i class="fas fa-comments fa-2x"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0">Bình luận đã gửi</h6>
                            <h3 class="fw-bold mb-0">{{ store.userStats.forum.my_comments || 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-success h-100 shadow-sm">
                    <div class="card-body d-flex align-items-center">
                        <div class="bg-success bg-opacity-10 p-3 rounded me-3 text-success">
                            <i class="fas fa-eye fa-2x"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-0">Lượt xem thu được</h6>
                            <h3 class="fw-bold mb-0">{{ store.userStats.forum.total_views_received || 0 }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { useDashboardStore } from '@/stores/dashboardStore';
import { useAuthStore } from '@/stores/authStore';

const store = useDashboardStore();
const authStore = useAuthStore();

onMounted(() => {
    store.fetchUserStats();
});

const formatDate = (date) => new Date(date).toLocaleDateString('vi-VN');
const isLate = (date) => new Date(date) < new Date(); // Kiểm tra xem đã qua ngày hiện tại chưa
</script>