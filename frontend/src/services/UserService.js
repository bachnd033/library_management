import api from '@/api/axios'; 

const RESOURCE = '/api/users';

export default {
  getUsers(params) {
    return api.get(RESOURCE, { 
        params,
        headers: { 'Cache-Control': 'no-cache', 'Pragma': 'no-cache', 'Expires': '0' }
    });
  },

  updateRole(id, role) {
    return api.put(`${RESOURCE}/${id}/role`, { role });
  },

  deleteUser(id) {
    return api.delete(`${RESOURCE}/${id}`);
  }
};