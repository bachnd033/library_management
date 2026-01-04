<template>
  <form @submit.prevent="handleSubmit">
    <div v-if="error" class="alert alert-danger">{{ error }}</div>

    <div class="mb-3">
      <label for="title" class="form-label">Tiêu đề sách (*)</label>
      <input type="text" v-model="formData.title" class="form-control" id="title" required>
    </div>

    <div class="mb-3">
      <label for="author" class="form-label">Tác giả (*)</label>
      <input type="text" v-model="formData.author" class="form-control" id="author" required>
    </div>

    <div class="mb-3">
      <label for="category" class="form-label">Thể loại (*)</label>
      <input type="text" v-model="formData.category" class="form-control" id="category" required>
    </div>

    <div class="row">
      <div class="col-md-4 mb-3">
        <label for="publication_year" class="form-label">Năm xuất bản</label>
        <input type="number" v-model.number="formData.publication_year" class="form-control" id="publication_year">
      </div>
      
      <div class="col-md-4 mb-3">
        <label for="total_copies" class="form-label">Tổng số bản (*)</label>
        <input type="number" v-model.number="formData.total_copies" class="form-control" id="total_copies" required min="1">
      </div>

      <div class="col-md-4 mb-3">
        <label for="available_copies" class="form-label">Số lượng hiện có (*)</label>
        <input 
            type="number" 
            v-model.number="formData.available_copies" 
            class="form-control" 
            id="available_copies" 
            required 
            min="0"
            :max="formData.total_copies"
        >
        <div class="form-text text-muted" v-if="formData.available_copies > formData.total_copies">
            <small class="text-danger">Lưu ý: Số hiện có đang lớn hơn tổng số bản.</small>
        </div>
      </div>
    </div>

    <div class="mb-3">
      <label for="description" class="form-label">Mô tả</label>
      <textarea v-model="formData.description" class="form-control" id="description" rows="4"></textarea>
    </div>

    <div class="d-flex justify-content-end gap-2">
      <button type="button" @click="router.push('/books')" class="btn btn-secondary">Hủy</button>
      <button type="submit" class="btn btn-primary" :disabled="isLoading">
        <span v-if="isLoading" class="spinner-border spinner-border-sm" role="status"></span>
        {{ submitText }}
      </button>
    </div>
  </form>
</template>

<script setup>
import { ref, watchEffect } from 'vue';
import { useRouter } from 'vue-router';

const props = defineProps({
  book: Object, 
  isLoading: Boolean,
  error: String,
  submitText: { 
    type: String,
    default: 'Lưu'
  }
});

const emit = defineEmits(['submit']);
const router = useRouter();

const formData = ref({
  title: '',
  author: '',
  category: '',
  publication_year: new Date().getFullYear(),
  total_copies: 1,
  available_copies: 1, // Khởi tạo mặc định
  description: '',
});

watchEffect(() => {
  if (props.book) {
    formData.value = { ...props.book };
  } else {
    //
  }
});

const handleSubmit = () => {
    if (formData.value.available_copies > formData.value.total_copies) {
        if(!confirm('Số lượng hiện có đang LỚN HƠN tổng số bản. Bạn có chắc chắn muốn lưu không?')) {
            return;
        }
    }
    emit('submit', formData.value);
};
</script>