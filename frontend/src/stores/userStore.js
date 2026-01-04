import { defineStore } from 'pinia';
import userService from '@/services/userService';

export const useUserStore = defineStore('user', {
  state: () => ({
    users: [],
    isLoading: false,
    error: null,
    pagination: { current_page: 1, last_page: 1, total: 0, per_page: 10 }
  }),

  actions: {
    async fetchUsers(params = {}) {
      this.isLoading = true;
      this.error = null;
      try {
        const queryParams = { 
            page: params.page || 1, 
            search: params.search || '',
            _t: Date.now() 
        };

        const response = await userService.getUsers(queryParams);
        const resData = response.data;

        // Xử lý dữ liệu trả về
        if (resData && Array.isArray(resData.data)) {
            this.users = resData.data; 
            this.pagination = {
                current_page: resData.current_page,
                last_page: resData.last_page,
                total: resData.total,
                per_page: resData.per_page
            };
        } else if (Array.isArray(resData)) {
            this.users = resData; 
        } else {
            this.users = [];
        }
      } catch (err) {
        console.error('Error fetching users:', err);
        this.error = err.response?.data?.message || 'Lỗi tải dữ liệu';
        this.users = [];
      } finally {
        this.isLoading = false;
      }
    },

    async updateUserRole(id, role) {
        try {
            await userService.updateRole(id, role);
            const user = this.users.find(u => u.id === id);
            if(user) user.role = role;
            return { success: true, message: 'Thành công' };
        } catch(err) {
            return { success: false, message: err.response?.data?.message || 'Lỗi' };
        }
    },

    async deleteUser(id) {
        try {
            await userService.deleteUser(id);
            this.users = this.users.filter(u => u.id !== id);
            this.pagination.total--;
            return { success: true, message: 'Thành công' };
        } catch(err) {
            return { success: false, message: err.response?.data?.message || 'Lỗi' };
        }
    }
  }
});