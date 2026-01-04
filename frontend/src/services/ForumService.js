import api from '@/api/axios';

const RESOURCE = '/api/forum';

export default {
    getCategories() {
        return api.get(`${RESOURCE}/categories`);
    },

    getPosts(params) {
        return api.get(`${RESOURCE}/posts`, { params });
    },

    getPostDetail(id) {
        return api.get(`${RESOURCE}/posts/${id}`);
    },

    createPost(data) {
        return api.post(`${RESOURCE}/posts`, data);
    },
    
    createComment(postId, content) {
        return api.post(`${RESOURCE}/posts/${postId}/comments`, { content });
    },

    createCategory(data) {
        return api.post(`${RESOURCE}/categories`, data);
    },

    deleteCategory(id) {
        return api.delete(`${RESOURCE}/categories/${id}`);
    },
    
    deleteComment(commentId) {
        return api.delete(`${RESOURCE}/comments/${commentId}`);
    },

    deletePost(id) {
        return api.delete(`${RESOURCE}/posts/${id}`);
    },

    getMyPosts(params) {
        return api.get(`${RESOURCE}/my-posts`, { params });
    },

    togglePin(id) {
        return api.put(`${RESOURCE}/posts/${id}/pin`);
    },
};