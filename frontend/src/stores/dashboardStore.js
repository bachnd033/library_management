import { defineStore } from 'pinia';
import dashboardService from '@/services/dashboardService';

export const useDashboardStore = defineStore('dashboard', {
    state: () => ({
        library: {},
        forum: {},
        newestUsers: [],
        recentPosts: [],
        isLoading: false,
        userStats: {
            library: {},
            forum: {},
            current_loans: []
        },
    }),
    actions: {
        async fetchStats() {
            this.isLoading = true;
            try {
                const res = await dashboardService.getStats();
                this.library = res.data.library;
                this.forum = res.data.forum;
                this.newestUsers = res.data.newest_users;
                this.recentPosts = res.data.recent_posts;
            } catch (error) {
                console.error("Lỗi tải thống kê:", error);
            } finally {
                this.isLoading = false;
            }
        },

        async fetchUserStats() {
            this.isLoading = true;
            try {
                const res = await dashboardService.getUserStats();
                this.userStats = res.data;
            } catch (error) {
                console.error("Lỗi tải thống kê user:", error);
            } finally {
                this.isLoading = false;
            }
        },
    }
});