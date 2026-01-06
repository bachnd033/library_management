<template>
  <div class="container mt-4">
    <h2 class="mb-4">Quản Lý Mượn Trả</h2>

    <ul class="nav nav-tabs mb-3">
      <li class="nav-item" v-for="status in ['pending', 'approved', 'returned', 'rejected']" :key="status">
        <button 
            class="nav-link text-capitalize" 
            :class="{ active: currentFilter === status }"
            @click="currentFilter = status"
        >
          {{ status === 'pending' ? 'Chờ duyệt' : (status === 'approved' ? 'Đang mượn' : status) }}
        </button>
      </li>
      <li class="nav-item">
          <button class="nav-link" :class="{ active: currentFilter === '' }" @click="currentFilter = ''">Tất cả</button>
      </li>
    </ul>

    <div class="table-responsive bg-white shadow-sm p-3 rounded">
      <table class="table table-hover align-middle">
        <thead class="table-light">
          <tr>
            <th>ID</th>
            <th>Độc giả</th>
            <th>Sách</th>
            <th>Ngày mượn</th>
            <th>Hạn trả</th>
            <th>Trạng thái</th>
            <th>Thao tác</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="loan in filteredLoans" :key="loan.id">
            <td>#{{ loan.id }}</td>
            <td>{{ loan.user?.name }} <br> <small class="text-muted">{{ loan.user?.email }}</small></td>
            <td class="fw-bold text-primary">{{ loan.book?.title }}</td>
            <td>{{ formatDate(loan.borrowed_at) }}</td>
            <td>
                <div v-if="loan.status === 'approved'" class="d-flex align-items-center">
                    <input type="date" class="form-control form-control-sm me-1" 
                           :value="formatDateInput(loan.due_date)" 
                           @change="updateDate(loan.id, $event)" 
                           style="width: 130px;">
                </div>
                <span v-else>{{ formatDate(loan.due_date) }}</span>
            </td>
            <td>
                <span class="badge" :class="getStatusBadge(loan.status)">
                    {{ loan.status.toUpperCase() }}
                </span>
            </td>
            <td>
              <div v-if="loan.status === 'pending'">
                <button @click="approve(loan.id)" class="btn btn-sm btn-success me-2">Duyệt</button>
                <button @click="reject(loan.id)" class="btn btn-sm btn-danger">Từ chối</button>
              </div>
              <span v-else class="text-muted small">---</span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { BookService } from '@/services/BookService';

const loans = ref([]);
const currentFilter = ref('pending'); // Mặc định hiển thị phiếu chờ duyệt

const fetchLoans = async () => {
    try {
        const res = await BookService.getAllLoans(); 
        loans.value = res.data;
    } catch (e) { console.error(e); }
};

// Lọc danh sách theo Tab
const filteredLoans = computed(() => {
    if (!currentFilter.value) return loans.value;
    return loans.value.filter(l => l.status === currentFilter.value);
});

const approve = async (id) => {
    if(!confirm('Duyệt phiếu này? Kho sách sẽ bị trừ.')) return;
    await BookService.approveLoan(id);
    await fetchLoans();
};

const reject = async (id) => {
    if(!confirm('Từ chối phiếu này?')) return;
    await BookService.rejectLoan(id);
    await fetchLoans();
};

const formatDate = (date) => date ? new Date(date).toLocaleDateString('vi-VN') : '';
const formatDateInput = (dateString) => {
    if (!dateString) return '';
    // Định dạng DD-MM-YYYY cho input date
    return String(dateString).substring(0, 10);
};
// Hàm xử lý khi Admin chọn ngày mới
const updateDate = async (id, event) => {
    const newDate = event.target.value;
    
    // Nếu user xóa trắng ngày -> bỏ qua
    if (!newDate) return;

    // Format lại ngày để hiển thị 
    const displayDate = new Date(newDate).toLocaleDateString('vi-VN');

    if(confirm(`Bạn có chắc muốn đổi hạn trả sang ngày ${displayDate} không?`)) {
        try {
            await BookService.updateDueDate(id, newDate);
            alert('Cập nhật thành công!');
            
            // Reload lại danh sách để cập nhật dữ liệu mới nhất
            await fetchLoans();
        } catch (error) {
            alert('Lỗi: ' + (error.response?.data?.message || 'Không thể cập nhật'));
            // Reset lại giá trị cũ nếu lỗi 
            await fetchLoans();
        }
    } else {
        // Nếu bấm Cancel, reload để ô input quay về ngày cũ
        await fetchLoans(); 
    }
};
const getStatusBadge = (status) => {
    if(status === 'pending') return 'bg-warning text-dark';
    if(status === 'approved') return 'bg-success';
    if(status === 'rejected') return 'bg-danger';
    return 'bg-secondary';
};

onMounted(fetchLoans);
</script>