<template>
  <div class="container mt-4" v-if="post">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="mb-3">
                <span class="badge bg-info text-dark">{{ post.category?.name }}</span>
                <span v-if="post.is_pinned" class="badge bg-danger ms-2">Được ghim</span>
            </div>
            <div class="d-flex justify-content-between align-items-start mb-3">
                <h1 class="h3 fw-bold mb-0">
                    {{ post.title }}
                    <span v-if="post.is_pinned" class="badge bg-danger fs-6 ms-2 align-middle">
                        <i class="fas fa-thumbtack"></i> Đã Ghim
                    </span>
                </h1>
                
                <div class="btn-group">
                    <button v-if="authStore.user?.role === 'admin'"
                            @click="handlePin"
                            class="btn btn-outline-warning btn-sm text-dark fw-bold"
                            :title="post.is_pinned ? 'Bỏ ghim bài này' : 'Ghim bài này lên đầu'">
                        <i class="fas fa-thumbtack"></i> {{ post.is_pinned ? 'Bỏ ghim' : 'Ghim bài' }}
                    </button>

                    <button v-if="authStore.user && (authStore.user.role === 'admin' || authStore.user.id === post.user_id)"
                            @click="handleDeletePost"
                            class="btn btn-outline-danger btn-sm">
                        <i class="fas fa-trash-alt"></i> Xóa
                    </button>
                </div>
            </div>
            <div class="text-muted small mb-4">
                Đăng bởi <strong>{{ post.user?.name }}</strong> vào lúc {{ formatDate(post.created_at) }}
                <span class="ms-3"><i class="fas fa-eye"></i> {{ post.views }} lượt xem</span>
            </div>
            <hr>
            <div class="post-content" style="white-space: pre-line;">
                {{ post.content }}
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-light fw-bold">
            <i class="fas fa-comments me-2"></i>Bình luận ({{ post.comments?.length || 0 }})
        </div>
        <div class="card-body">
            <div v-for="comment in post.comments" :key="comment.id" class="d-flex mb-3 pb-3 border-bottom">
                <div class="flex-shrink-0">
                    <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px">
                        {{ comment.user?.name.charAt(0).toUpperCase() }}
                    </div>
                </div>
                <div class="flex-grow-1 ms-3">
                    <div class="d-flex justify-content-between">
                        <h6 class="fw-bold mb-1">
                            {{ comment.user?.name }} 
                            <small class="text-muted fw-normal ms-2">{{ formatDate(comment.created_at) }}</small>
                        </h6>
                        
                        <button v-if="authStore.user?.role === 'admin' || authStore.user?.id === comment.user_id" 
                                @click="deleteComment(comment.id)" 
                                class="btn btn-sm text-danger p-0 border-0" 
                                title="Xóa bình luận">
                            <span>Xóa bình luận</span>
                        </button>
                    </div>
                    <p class="mb-0">{{ comment.content }}</p>
                </div>
            </div>

            <div class="mt-4">
                <textarea v-model="newComment" class="form-control mb-2" rows="3" placeholder="Viết bình luận của bạn..."></textarea>
                <button @click="submitComment" class="btn btn-primary" :disabled="!newComment.trim()">
                    Gửi bình luận
                </button>
            </div>
        </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRoute } from 'vue-router';
import { useForumStore } from '@/stores/forumStore';
import { useAuthStore } from '@/stores/authStore';
import { useRouter } from 'vue-router';

const route = useRoute();
const forumStore = useForumStore();
const authStore = useAuthStore();
const router = useRouter();
const newComment = ref('');

const post = computed(() => forumStore.currentPost);

onMounted(() => {
    forumStore.fetchPostDetail(route.params.id);
});

const submitComment = async () => {
    if(!newComment.value.trim()) return;
    await forumStore.addComment(post.value.id, newComment.value);
    newComment.value = ''; // Xóa ô nhập sau khi gửi
};

const deleteComment = async (commentId) => {
    if(confirm('Bạn muốn xóa bình luận này?')) {
        await forumStore.removeComment(commentId);
    }
};

const handleDeletePost = async () => {
    if (confirm('CẢNH BÁO: Hành động này không thể hoàn tác.\nBạn có chắc chắn muốn xóa bài viết này không?')) {
        try {
            await forumStore.removePost(post.value.id);
            alert('Đã xóa bài viết thành công!');
            router.push('/forum'); 
        } catch (error) {
            alert('Lỗi: ' + (error.response?.data?.message || 'Không thể xóa bài viết'));
        }
    }
};
const handlePin = async () => {
    try {
        const message = await forumStore.pinPost(post.value.id);
        alert(message);
    } catch (error) {
        alert('Lỗi: ' + (error.response?.data?.message || 'Không thể ghim bài'));
    }
};
const formatDate = (date) => new Date(date).toLocaleString('vi-VN');
</script>