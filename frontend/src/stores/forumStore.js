import { defineStore } from 'pinia';
import forumService from '@/services/forumService';

export const useForumStore = defineStore('forum', {
    state: () => ({
        posts: [],
        categories: [],
        currentPost: null, // Bài viết đang xem chi tiết
        isLoading: false,
        pagination: { current_page: 1, last_page: 1 }
    }),
    actions: {
        async fetchCategories() {
            const res = await forumService.getCategories();
            this.categories = res.data;
        },
        async fetchPosts(params = {}) {
            this.isLoading = true;
            try {
                const res = await forumService.getPosts(params);
                this.posts = res.data.data;
                this.pagination = {
                    current_page: res.data.current_page,
                    last_page: res.data.last_page
                };
            } finally {
                this.isLoading = false;
            }
        },
        async fetchPostDetail(id) {
            this.isLoading = true;
            try {
                const res = await forumService.getPostDetail(id);
                this.currentPost = res.data;
            } finally {
                this.isLoading = false;
            }
        },
        async addComment(postId, content) {
            const res = await forumService.createComment(postId, content);
            if (this.currentPost) {
                this.currentPost.comments.push(res.data.comment);
            }
        }
    }
});