// src/router/index.js
import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../Views/HomeView.vue'
import axios from '../main'

const routes = [
  {
    path: '/',
    name: 'home',
    component: () => import('@/components/LandingPage.vue')
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
    path: '/admin/dashboard',
    name: 'dashboard',
    component: () => import('@/Views/DashboardView.vue'),
    meta: { requiresAuth: true, requiresAdmin: true }
  },
  // Admin routes
  {
    path: '/admin/berita',
    name: 'admin.berita',
    component: () => import('@/Views/Admin/BeritaIndex.vue'),
    meta: { requiresAuth: true, requiresAdmin: true }
  },
  {
    path: '/admin/berita/create',
    name: 'admin.berita.create',
    component: () => import('@/Views/Admin/BeritaCreate.vue'),
    meta: { requiresAuth: true, requiresAdmin: true }
  },
  {
    path: '/admin/jadwal',
    name: 'admin.jadwal',
    component: () => import('@/Views/Admin/JadwalIndex.vue'),
    meta: { requiresAuth: true, requiresAdmin: true }
  },
  {
    path: '/admin/jadwal/create',
    name: 'admin.jadwal.create',
    component: () => import('@/Views/Admin/JadwalCreate.vue'),
    meta: { requiresAuth: true, requiresAdmin: true }
  },
  {
    path: '/admin/users',
    name: 'admin.users',
    component: () => import('@/Views/Admin/UsersIndex.vue'),
    meta: { requiresAuth: true, requiresAdmin: true }
  },
  {
    path: '/admin/settings/iku',
    name: 'admin.settings.iku',
    component: () => import('@/Views/Admin/SettingsIKU.vue'),
    meta: { requiresAuth: true, requiresAdmin: true }
  },
  {
    path: '/admin/settings/iku/create',
    name: 'admin.settings.iku.create',
    component: () => import('@/Views/Admin/SettingsIKUCreate.vue'),
    meta: { requiresAuth: true, requiresAdmin: true }
  },
  {
    path: '/admin/settings/unit-kerja/create',
    name: 'admin.settings.unit-kerja.create',
    component: () => import('@/Views/Admin/UnitKerjaCreate.vue'),
    meta: { requiresAuth: true, requiresAdmin: true }
  },
  {
    path: '/admin/dokumen',
    name: 'admin.dokumen',
    component: () => import('@/Views/Admin/DokumenIndex.vue'),
    meta: { requiresAuth: true, requiresAdmin: true }
  },
  {
    path: '/admin/landing-settings',
    name: 'admin.landing',
    component: () => import('@/Views/Admin/LandingPageSettings.vue'),
    meta: { requiresAuth: true, requiresAdmin: true }
  }
]

const router = createRouter({
  history: createWebHistory('/'),
  routes
})

// Navigation Guard
router.beforeEach(async (to, from, next) => {
  const token = localStorage.getItem('token')
  
  if (to.meta.requiresAuth) {
    if (!token) {
      sessionStorage.setItem('login_redirect', to.fullPath)
      next('/pengelola/login')
      return
    }
    
    if (to.meta.requiresAdmin) {
      try {
        const userData = localStorage.getItem('user')
        if (userData) {
          const user = JSON.parse(userData)
          if (user.is_admin) {
            next()
            return
          }
        }
        
        const response = await axios.get('/user')
        if (response.data.success && response.data.data.is_admin) {
          localStorage.setItem('user', JSON.stringify(response.data.data))
          next()
        } else {
          next('/')
        }
      } catch (error) {
        console.error('Auth error:', error)
        if (error.response?.status === 401) {
          localStorage.removeItem('token')
          localStorage.removeItem('user')
          next('/pengelola/login')
        } else {
          next('/')
        }
      }
    } else {
      next()
    }
  } 
  else if ((to.path === '/login' || to.path === '/pengelola/login') && token) {
    next('/admin/dashboard')
  } 
  else {
    next()
  }
})

export default router