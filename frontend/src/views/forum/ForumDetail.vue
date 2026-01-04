<template>
  <div class="container mt-4" v-if="post">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="mb-3">
                <span class="badge bg-info text-dark">{{ post.category?.name }}</span>
                <span v-if="post.is_pinned" class="badge bg-danger ms-2">Được ghim</span>
            </div>
            <h1 class="h3 fw-bold">{{ post.title }}</h1>
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
                    <h6 class="fw-bold mb-1">{{ comment.user?.name }} <small class="text-muted fw-normal ms-2">{{ formatDate(comment.created_at) }}</small></h6>
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

const route = useRoute();
const forumStore = useForumStore();
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

const formatDate = (date) => new Date(date).toLocaleString('vi-VN');
</script>