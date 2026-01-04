import api from '@/api/axios';

export default {
    getStats() {
        return api.get('/api/admin/dashboard');
    }
};