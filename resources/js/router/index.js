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
    path: '/pengelola/dashboard',
    name: 'dashboard',
    component: () => import('@/Views/DashboardView.vue'),
    meta: { requiresAuth: true, requiresAdmin: true }
  },
  // Admin routes
  {
    path: '/pengelola/berita',
    name: 'pengelola.berita',
    component: () => import('@/Views/Pengelola/BeritaIndex.vue'),
    meta: { requiresAuth: true, requiresAdmin: true }
  },
  {
    path: '/pengelola/berita/create',
    name: 'pengelola.berita.create',
    component: () => import('@/Views/Pengelola/BeritaCreate.vue'),
    meta: { requiresAuth: true, requiresAdmin: true }
  },
  {
    path: '/pengelola/jadwal',
    name: 'pengelola.jadwal',
    component: () => import('@/Views/Pengelola/JadwalIndex.vue'),
    meta: { requiresAuth: true, requiresAdmin: true }
  },
  {
    path: '/pengelola/jadwal/create',
    name: 'pengelola.jadwal.create',
    component: () => import('@/Views/Pengelola/JadwalCreate.vue'),
    meta: { requiresAuth: true, requiresAdmin: true }
  },
  {
    path: '/pengelola/users',
    name: 'pengelola.users',
    component: () => import('@/Views/Pengelola/UsersIndex.vue'),
    meta: { requiresAuth: true, requiresAdmin: true }
  },
  {
    path: '/pengelola/unit-kerja',
    name: 'pengelola.unit-kerja',
    component: () => import('@/Views/Pengelola/UnitKerjaIndex.vue'),
    meta: { requiresAuth: true, requiresAdmin: true }
  },
  {
    path: '/pengelola/iku',
    name: 'pengelola.iku',
    component: () => import('@/Views/Pengelola/IkuIndex.vue'),
    meta: { requiresAuth: true, requiresAdmin: true }
  },
  {
    path: '/pengelola/dokumen/:slugPath*',
    name: 'pengelola.dokumen',
    component: () => import('@/Views/Pengelola/DokumenIndex.vue'),
    meta: { requiresAuth: true, requiresAdmin: true }
  },
  {
    path: '/pengelola/landing-settings',
    name: 'pengelola.landing',
    component: () => import('@/Views/Pengelola/LandingPageSettings.vue'),
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
  // PERBAIKAN DI SINI:
  else if ((to.path === '/login' || to.path === '/pengelola/login') && token) {
    next('/pengelola/dashboard') // Ubah dari /admin/dashboard
  } 
  else {
    next()
  }
})

export default router