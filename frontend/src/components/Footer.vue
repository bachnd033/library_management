<template>
  <footer class="bg-dark text-light py-4 mt-auto">
    <div class="container">
      <div class="row">
        <div class="col-md-6">
          <h5>📚 Quản Lý Thư Viện</h5>
          <p>Hệ thống quản lý thư viện thông minh, giúp bạn tra cứu và mượn sách dễ dàng.</p>
        </div>
        <div class="col-md-3">
          <h6>Liên kết nhanh</h6>
          <ul class="list-unstyled">
            <li><router-link to="/" class="text-decoration-none">Trang chủ</router-link></li>
            <li><router-link to="/books" class="text-decoration-none">Danh mục sách</router-link></li>
          </ul>
        </div>
        <div class="col-md-3">
          <h6>Liên hệ</h6>
          <ul class="list-unstyled">
            <li>📍 123 Đường ABC, Hà Nội</li>
            <li>📧 contact@library.com</li>
            <li>📞 0901 234 567</li>
          </ul>
        </div>
      </div>
      <div class="text-center pt-3 border-top border-secondary mt-3">
        <small>&copy; 2025 Library Management System. All rights reserved.</small>
      </div>
    </div>
  </footer>
</template>