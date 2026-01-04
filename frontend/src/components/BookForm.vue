<template>
  <form @submit.prevent="handleSubmit">
    <div v-if="error" class="alert alert-danger">{{ error }}</div>

    <div class="row">
      <div class="col-md-8">
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
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="mb-3">
            <label class="form-label">Ảnh bìa sách</label>
            
            <input type="file" class="form-control" @change="handleFileChange" accept="image/*">
            
            <div class="mt-3 text-center border p-2 bg-light" style="min-height: 200px;">
                <img v-if="previewImage" :src="previewImage" class="img-fluid" style="max-height: 200px;" alt="New preview">
                
                <img v-else-if="formData.image_url" :src="formData.image_url" class="img-fluid" style="max-height: 200px;" alt="Current cover">
                
                <span v-else class="text-muted d-block mt-5">Chưa có ảnh</span>
            </div>
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
  submitText: { type: String, default: 'Lưu' }
});

const emit = defineEmits(['submit']);
const router = useRouter();

const formData = ref({
  title: '',
  author: '',
  category: '',
  publication_year: new Date().getFullYear(),
  total_copies: 1,
  available_copies: 1,
  description: '',
  image_url: null, 
});

const selectedFile = ref(null);
const previewImage = ref(null);

// Load dữ liệu khi sửa sách
watchEffect(() => {
  if (props.book) {
    // Copy props vào formData
    formData.value = { ...props.book };
    // Reset file input state khi load book mới
    selectedFile.value = null;
    previewImage.value = null;
  }
});

// Xử lý khi chọn file
const handleFileChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        selectedFile.value = file;
        // Tạo URL ảo để preview ngay lập tức
        previewImage.value = URL.createObjectURL(file);
    }
};

const handleSubmit = () => {
    if (formData.value.available_copies > formData.value.total_copies) {
        if(!confirm('Số lượng hiện có đang LỚN HƠN tổng số bản. Bạn có chắc chắn muốn lưu không?')) {
            return;
        }
    }

    const payload = new FormData();

    // Append các trường text
    payload.append('title', formData.value.title);
    payload.append('author', formData.value.author);
    payload.append('category', formData.value.category);
    payload.append('publication_year', formData.value.publication_year || '');
    payload.append('total_copies', formData.value.total_copies);
    payload.append('available_copies', formData.value.available_copies);
    payload.append('description', formData.value.description || '');

    // Append file ảnh 
    if (selectedFile.value) {
        payload.append('image', selectedFile.value);
    }

    // Nếu là edit, thêm phương thức PUT
    if (props.book) {
        payload.append('_method', 'PUT');
    }
    
    emit('submit', payload);
};
</script>