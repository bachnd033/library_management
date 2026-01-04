import { createRouter, createWebHistory } from 'vue-router'
import BookList from '../views/library/BookList.vue';
import BookCreate from '../views/library/BookCreate.vue';
import BookEdit from '../views/library/BookEdit.vue';
import HomeView from '../views/HomeView.vue'
import RegisterView from '../views/RegisterView.vue'
import LoginView from '../views/LoginView.vue'
import BookDetailView from '../views/BookDetailView.vue'
import MyBooks from '../views/library/MyBooks.vue'
import UserProfile from '../views/UserProfile.vue'
import AdminLoans from '../views/admin/AdminLoans.vue'
import UserListView from '../views/admin/UserListView.vue'
import ForumHome from '../views/forum/ForumHome.vue'
import ForumCreate from '../views/forum/ForumCreate.vue'
import ForumDetail from '../views/forum/ForumDetail.vue'
import AdminCategories from '../views/forum/AdminCategories.vue'
import MyPosts from '../views/forum/MyPosts.vue'
import AdminDashboard from '../views/admin/AdminDashboard.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    // {
    //   path: '/books',
    //   name: 'books',
    //   redirect: '/books', 
    //   meta: { requiresAuth: true }
    // },
    
    {
      path: '/',
      name: 'Home',
      component: HomeView, 
      meta: { requiresAuth: false }
    },

    { 
      path: '/register',
      name: 'Register',
      component: RegisterView,
      meta: { 
        requiresAuth: false,
        hideNavbar: true 
      }
    },

    {
      path: '/login',
      name: 'Login',
      component: LoginView,
      meta: { 
        requiresAuth: false,
        hideNavbar: true 
      }
    },

    {
      path: '/books',
      name: 'BookList',
      component: BookList,
      meta: { requiresAuth: true }
    },

    {
      path: '/books/create',
      name: 'BookCreate',
      component: BookCreate,
      meta: { requiresAuth: true }
    },

    {
      path: '/books/edit/:id', 
      name: 'BookEdit',
      component: BookEdit,
      meta: { requiresAuth: true }
    },

    {
      path: '/books/:id',
      name: 'book-detail',
      component: BookDetailView
    },

    {
      path: '/my-books',
      name: 'MyBooks',
      component: MyBooks,
      meta: { requiresAuth: true }
    },

    {
      path: '/profile',
      name: 'UserProfile',
      component: UserProfile,
      meta: { requiresAuth: true }
    },

    {
      path: '/admin/loans',
      name: 'AdminLoans',
      component: AdminLoans,
      meta: { requiresAuth: true, role: 'admin' } 
    },

    {
      path: '/admin/users',
      name: 'admin-users',
      component: UserListView,
      meta: { requiresAuth: true, role: 'admin' }
    },

    {
      path: '/forum',
      name: 'ForumHome',
      component: ForumHome,
      meta: { requiresAuth: true } 
    },

    {
      path: '/forum/create',
      name: 'ForumCreate',
      component: ForumCreate,
      meta: { requiresAuth: true }
    },

    {
      path: '/forum/:id',
      name: 'ForumDetail',
      component: ForumDetail,
      meta: { requiresAuth: true }
    },

    {
      path: '/admin/forum/categories',
      name: 'AdminForumCategories',
      component: AdminCategories,
      meta: { requiresAuth: true, role: 'admin' }
    },

    {
      path: '/forum/my-posts',
      name: 'MyPosts',
      component: MyPosts,
      meta: { requiresAuth: true }
  },

  {
    path: '/admin/dashboard',
    name: 'AdminDashboard',
    component: AdminDashboard,
    meta: { requiresAuth: true, role: 'admin' }
  },
    // {
    //   path: '/about',
    //   name: 'about',
    //   // route level code-splitting
    //   // this generates a separate chunk (About.[hash].js) for this route
    //   // which is lazy-loaded when the route is visited.
    //   component: () => import('../views/AboutView.vue'),
    // },
  ],
})

export default router
