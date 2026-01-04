import axios from 'axios';
import router from '@/router';

const api = axios.create({
    baseURL: 'http://localhost:8000', 
    withCredentials: true, 
    headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest', 
    }
});

function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
}

api.interceptors.request.use(config => {
    const token = getCookie('XSRF-TOKEN');
    
    if (token) {
        config.headers['X-XSRF-TOKEN'] = decodeURIComponent(token);
    }
    return config;
}, error => {
    return Promise.reject(error);
});

api.interceptors.response.use(
    (response) => {
        return response; // Nếu thành công thì trả về bình thường
    },
    (error) => {
        // Nếu Server trả về lỗi 403 (Forbidden)
        if (error.response && error.response.status === 403) {
            
            router.push('/'); 
        }
        
        // Nếu Server trả về lỗi 401 (Chưa đăng nhập / Hết phiên)
        if (error.response && error.response.status === 401) {
            router.push('/login');
        }

        return Promise.reject(error);
    }
);

export default api;