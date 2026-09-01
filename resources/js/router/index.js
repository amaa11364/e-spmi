// src/router/index.js
import { createRouter, createWebHistory } from 'vue-router'
import axios from '../main'

const routes = [
  // Public Routes with Layout
  {
    path: '/',
    component: () => import('@/components/PublicLayout.vue'),
    children: [
      {
        path: '',
        name: 'home',
        component: () => import('@/components/LandingPage.vue'),
        meta: { title: 'E-SPMI - IKIP Siliwangi' }
      },
      {
        path: 'tentang/profil',
        name: 'tentang.profil',
        component: () => import('@/Views/ProfilView.vue'),
        meta: { title: 'Profil LPMI - E-SPMI IKIP Siliwangi' }
      },
      {
        path: 'tentang/visi-misi',
        name: 'tentang.visi-misi',
        component: () => import('@/Views/VisiMisiView.vue'),
        meta: { title: 'Visi & Misi - E-SPMI IKIP Siliwangi' }
      },
      {
        path: 'tentang/siklus-ppepp',
        name: 'tentang.siklus-ppepp',
        component: () => import('@/Views/SiklusPpeppView.vue'),
        meta: { title: 'Siklus PPEPP - E-SPMI IKIP Siliwangi' }
      },
      {
        path: 'berita',
        name: 'berita',
        component: () => import('@/Views/BeritaView.vue'),
        meta: { title: 'Berita - E-SPMI IKIP Siliwangi' }
      },
      {
        path: 'dokumen-publik',
        name: 'dokumen-publik',
        component: () => import('@/Views/DokumenPublikView.vue'),
        meta: { title: 'Dokumen Publik - E-SPMI IKIP Siliwangi' }
      }
    ]
  },
  {
    path: '/login',
    name: 'login',
    component: () => import('@/Views/LoginView.vue'),
    meta: { title: 'Login - E-SPMI' }
  },
  {
    path: '/pengelola/login',
    name: 'admin.login',
    component: () => import('@/Views/LoginView.vue'),
    meta: { title: 'Login Pengelola - E-SPMI' }
  },

  // Admin / Pengelola Routes
  {
    path: '/pengelola/dashboard',
    name: 'dashboard',
    component: () => import('@/Views/DashboardView.vue'),
    meta: { requiresAuth: true, requiresAdmin: true, title: 'Dashboard Pengelola' }
  },
  {
    path: '/pengelola/berita',
    name: 'pengelola.berita',
    component: () => import('@/Views/Pengelola/BeritaIndex.vue'),
    meta: { requiresAuth: true, requiresAdmin: true, title: 'Kelola Berita' }
  },
  {
    path: '/pengelola/berita/create',
    name: 'pengelola.berita.create',
    component: () => import('@/Views/Pengelola/BeritaCreate.vue'),
    meta: { requiresAuth: true, requiresAdmin: true, title: 'Tambah Berita' }
  },
  {
    path: '/pengelola/jadwal',
    name: 'pengelola.jadwal',
    component: () => import('@/Views/Pengelola/JadwalIndex.vue'),
    meta: { requiresAuth: true, requiresAdmin: true, title: 'Kelola Jadwal' }
  },
  {
    path: '/pengelola/jadwal/create',
    name: 'pengelola.jadwal.create',
    component: () => import('@/Views/Pengelola/JadwalCreate.vue'),
    meta: { requiresAuth: true, requiresAdmin: true, title: 'Tambah Jadwal' }
  },
  {
    path: '/pengelola/users',
    name: 'pengelola.users',
    component: () => import('@/Views/Pengelola/UsersIndex.vue'),
    meta: { requiresAuth: true, requiresAdmin: true, title: 'Kelola Pengguna' }
  },
  {
    path: '/pengelola/unit-kerja',
    name: 'pengelola.unit-kerja',
    component: () => import('@/Views/Pengelola/UnitKerjaIndex.vue'),
    meta: { requiresAuth: true, requiresAdmin: true, title: 'Kelola Unit Kerja' }
  },
  {
    path: '/pengelola/iku',
    name: 'pengelola.iku',
    component: () => import('@/Views/Pengelola/IkuIndex.vue'),
    meta: { requiresAuth: true, requiresAdmin: true, title: 'Kelola IKU' }
  },
  {
    path: '/pengelola/dokumen/:slugPath*',
    name: 'pengelola.dokumen',
    component: () => import('@/Views/Pengelola/DokumenIndex.vue'),
    meta: { requiresAuth: true, requiresAdmin: true, title: 'Kelola Dokumen' }
  },
  {
    path: '/pengelola/landing-settings',
    name: 'pengelola.landing',
    component: () => import('@/Views/Pengelola/LandingPageSettings.vue'),
    meta: { requiresAuth: true, requiresAdmin: true, title: 'Pengaturan Landing Page' }
  },

  // Catch-All 404 Redirect
  {
    path: '/:pathMatch(.*)*',
    redirect: '/'
  }
]

const router = createRouter({
  history: createWebHistory('/'),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition
    }
    
    // Penanganan Scroll Smooth untuk Anchor Tag (#about, #about-visi-misi, dll)
    if (to.hash) {
      return new Promise((resolve) => {
        setTimeout(() => {
          resolve({
            el: to.hash,
            behavior: 'smooth',
            top: 80 // Offset jarak untuk mengantisipasi Navbar Fixed
          })
        }, 300)
      })
    }
    
    // Default scroll ke atas halaman
    return { top: 0, left: 0 }
  }
})

// Navigation Guard & Authentication
router.beforeEach(async (to, from, next) => {
  // 1. Pengaturan Dynamic Page Title
  if (to.meta && to.meta.title) {
    document.title = to.meta.title
  } else {
    document.title = 'E-SPMI IKIP Siliwangi'
  }

  const token = localStorage.getItem('token')
  
  // 2. Proteksi Rute Terautentikasi (Protected Routes)
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
        
        // Verifikasi Ulang ke Server apabila Cache User Lokal Kosong
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
  // 3. Pengalihan Pengguna yang Sudah Login jika Mengakses Halaman Login
  else if ((to.path === '/login' || to.path === '/pengelola/login') && token) {
    next('/pengelola/dashboard')
  } 
  else {
    next()
  }
})

export default router