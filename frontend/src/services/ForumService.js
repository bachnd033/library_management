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
    }
};