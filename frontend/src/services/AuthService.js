import api from '@/api/axios';

export const AuthService = {
    login(credentials) {
        return api.post('/api/login', credentials);
    },
    register(userInfo) {
        return api.post('/api/register', userInfo); 
    },
    logout() {
        return api.post('/api/logout');
    },
    getUser() {
        return api.get('/api/user');
    }
};