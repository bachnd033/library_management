<template>
  <div class="container mt-4">
    <div class="row">
      <div class="col-md-9">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="text-primary"><i class="fas fa-comments me-2"></i>Diễn Đàn Thảo Luận</h2>
            <div>
                <router-link to="/forum/my-posts" class="btn btn-outline-primary me-2">
                    <i class="fas fa-list-alt me-1"></i> Bài của tôi
                </router-link>
            
                <router-link to="/forum/create" class="btn btn-success">
                    <i class="fas fa-plus-circle me-1"></i> Đăng bài
                </router-link>
            </div>
        </div>

        <div class="input-group mb-4 shadow-sm">
            <input v-model="searchQuery" @keyup.enter="handleSearch" type="text" class="form-control" placeholder="Tìm kiếm chủ đề...">
            <button @click="handleSearch" class="btn btn-primary">Tìm</button>
        </div>

        <div v-if="forumStore.isLoading" class="text-center py-5">
            <div class="spinner-border text-primary"></div>
        </div>

        <div v-else>
            <div v-for="post in forumStore.posts" :key="post.id" class="card mb-3 shadow-sm border-start-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <h5 class="card-title mb-1 flex-grow-1">
                            <span v-if="post.is_pinned" class="badge bg-danger me-2">
                                <i class="fas fa-thumbtack"></i>
                            </span>
                            <router-link :to="'/forum/' + post.id" class="text-decoration-none text-dark fw-bold">
                                {{ post.title }}
                            </router-link>
                        </h5>

                        <div class="ms-2 d-flex gap-2 align-items-center">
                            <small class="text-muted"><i class="fas fa-eye"></i> {{ post.views }}</small>

                            <button v-if="authStore.user?.role === 'admin'"
                                    @click="handlePin(post)"
                                    class="btn btn-sm btn-link p-0 text-warning ms-2"
                                    :title="post.is_pinned ? 'Bỏ ghim' : 'Ghim bài'">
                                <i class="fas fa-thumbtack" :class="{'text-secondary': !post.is_pinned}"></i>
                            </button>

                            <button v-if="authStore.user && (authStore.user.role === 'admin' || authStore.user.id === post.user_id)"
                                    @click="handleDelete(post)"
                                    class="btn btn-sm btn-link p-0 text-danger ms-2"
                                    title="Xóa bài viết">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </div>
                    
                    <p class="card-text text-muted text-truncate mt-2">{{ post.content }}</p>
                    
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <small class="text-secondary">
                            <i class="fas fa-user-circle me-1"></i> {{ post.user?.name }} 
                            <span class="mx-2">•</span> 
                            <i class="fas fa-clock me-1"></i> {{ formatDate(post.created_at) }}
                        </small>
                        <span class="badge bg-light text-dark border">{{ post.category?.name }}</span>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-center mt-4" v-if="forumStore.pagination.last_page > 1">
                <button class="btn btn-outline-primary me-2" :disabled="forumStore.pagination.current_page === 1" 
                    @click="changePage(forumStore.pagination.current_page - 1)">Trước</button>
                <button class="btn btn-outline-primary" :disabled="forumStore.pagination.current_page === forumStore.pagination.last_page"
                    @click="changePage(forumStore.pagination.current_page + 1)">Sau</button>
            </div>
        </div>
      </div>

      <div class="col-md-3">
        <div class="card shadow-sm">
            <div class="card-header bg-white fw-bold">Chuyên Mục</div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item list-group-item-action" @click="filterCategory(null)" style="cursor: pointer">
                    Tất cả chủ đề
                </li>
                <li v-for="cat in forumStore.categories" :key="cat.id" 
                    class="list-group-item list-group-item-action" 
                    @click="filterCategory(cat.id)" style="cursor: pointer">
                    {{ cat.name }}
                </li>
            </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useForumStore } from '@/stores/forumStore';
import { useAuthStore } from '@/stores/authStore'; 

const forumStore = useForumStore();
const authStore = useAuthStore(); 
const searchQuery = ref('');
const currentCat = ref(null);

onMounted(() => {
    forumStore.fetchCategories();
    forumStore.fetchPosts();
});

const handleSearch = () => forumStore.fetchPosts({ search: searchQuery.value, category_id: currentCat.value });

const filterCategory = (id) => {
    currentCat.value = id;
    forumStore.fetchPosts({ category_id: id });
};

const changePage = (page) => forumStore.fetchPosts({ page, category_id: currentCat.value, search: searchQuery.value });

const handlePin = async (post) => {
    try {
        await forumStore.pinPost(post.id);
    } catch (error) {
        alert('Lỗi: ' + (error.response?.data?.message || 'Không thể ghim bài'));
    }
};

const handleDelete = async (post) => {
    if(confirm(`Bạn có chắc chắn muốn xóa bài: "${post.title}"?`)) {
        try {
            await forumStore.removePost(post.id);
        } catch (error) {
            alert('Lỗi: ' + (error.response?.data?.message || 'Không thể xóa bài'));
        }
    }
};

const formatDate = (date) => new Date(date).toLocaleDateString('vi-VN');
</script>

<style scoped>
.border-start-primary { border-left: 4px solid #0d6efd; }
.btn-link { text-decoration: none; }
</style>