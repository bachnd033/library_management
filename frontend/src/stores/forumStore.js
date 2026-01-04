import { defineStore } from 'pinia';
import forumService from '@/services/forumService';

export const useForumStore = defineStore('forum', {
    state: () => ({
        posts: [],
        categories: [],
        currentPost: null, 
        isLoading: false,
        pagination: { current_page: 1, last_page: 1 }
    }),

    actions: {
        // Lấy danh sách chuyên mục
        async fetchCategories() {
            const res = await forumService.getCategories();
            this.categories = res.data;
        },

        // Lấy danh sách bài viết
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

        // Lấy chi tiết bài viết
        async fetchPostDetail(id) {
            this.isLoading = true;
            try {
                const res = await forumService.getPostDetail(id);
                this.currentPost = res.data;
            } finally {
                this.isLoading = false;
            }
        },

        // Thêm bình luận
        async addComment(postId, content) {
            const res = await forumService.createComment(postId, content);
            if (this.currentPost) {
                this.currentPost.comments.push(res.data.comment);
            }
        },

        // Xóa bình luận
        async removeComment(commentId) {
            await forumService.deleteComment(commentId);
            // Cập nhật giao diện: Loại bỏ comment vừa xóa khỏi danh sách
            if (this.currentPost && this.currentPost.comments) {
                this.currentPost.comments = this.currentPost.comments.filter(c => c.id !== commentId);
            }
        },

        // Xóa chuyên mục
        async removeCategory(id) {
            await forumService.deleteCategory(id);
            this.categories = this.categories.filter(c => c.id !== id);
        },

        // Xóa bài viết
        async removePost(postId) {
            await forumService.deletePost(postId);
            // Sau khi xóa xong, reset currentPost
            this.currentPost = null; 
        },
    }
});