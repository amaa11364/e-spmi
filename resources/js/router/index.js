// src/router/index.js
import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../Views/HomeView.vue'

const routes = [
  {
    path: '/',
    name: 'home',
    component: HomeView
  },
  {
    path: '/login',
    name: 'login',
    component: () => import('@/Views/LoginView.vue')
  },
  {
    path: '/pengelola/login',
    name: 'admin.login',
    component: () => import('@/Views/LoginView.vue')
  },
  {
    path: '/berita',
    name: 'berita',
    component: () => import('@/Views/BeritaView.vue')
  },
  {
    path: '/dokumen-publik',
    name: 'dokumen-publik',
    component: () => import('@/Views/DokumenPublikView.vue')
  },
  {
    path: '/dashboard',
    name: 'dashboard',
    component: () => import('@/Views/DashboardView.vue'),
    meta: { requiresAuth: true }
  }
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes
})

// Navigation guard untuk proteksi route
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token')
  
  if (to.meta.requiresAuth && !token) {
    // Simpan URL yang ingin diakses untuk redirect setelah login
    sessionStorage.setItem('login_redirect', to.fullPath)
    next({ path: '/pengelola/login' })
  } else if ((to.path === '/login' || to.path === '/pengelola/login') && token) {
    // Jika sudah login, redirect ke dashboard
    next('/dashboard')
  } else {
    next()
  }
})

export default router