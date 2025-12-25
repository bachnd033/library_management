import api from '@/api/axios';

export const AuthService = {
    login(credentials) {
        return api.post('/login', credentials);
    },
    register(userInfo) {
        return api.post('/register', userInfo); 
    },
    logout() {
        return api.post('/logout');
    },
    getUser() {
        return api.get('/user');
    }
};