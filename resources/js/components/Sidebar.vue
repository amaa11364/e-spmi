<template>
  <aside class="sidebar" :class="{ 'collapsed': isCollapsed, 'show': isOpen }">
    <!-- Sidebar Header -->
    <div class="sidebar-header">
      <div class="sidebar-brand">
        <div class="sidebar-logo-placeholder" :class="{ 'small': isCollapsed }">
          <!-- Logo dengan fallback sederhana -->
          <img 
            :src="'/images/photos/logo-ikipsiliwangi.png'"
            alt="Logo SPMI" 
            class="brand-logo"
            v-if="!isCollapsed"
            @error="useFallback"
          >
          <!-- Fallback saat collapsed atau gambar error -->
          <span v-else class="brand-initial">E</span>
        </div>
        <div class="sidebar-brand-text" v-if="!isCollapsed">
          <h5>E-SPMI</h5>
          <small>Q-TRACK Digital</small>
        </div>
      </div>
      <div class="sidebar-user-role" v-if="!isCollapsed">
        <span class="role-badge" :class="roleClass">
          {{ userRole }}
        </span>
      </div>
    </div>

    <!-- Sidebar Menu -->
    <nav class="sidebar-menu">
      <ul class="nav flex-column">
        <!-- Dashboard -->
        <li class="nav-item">
          <router-link to="/dashboard" class="nav-link" active-class="active">
            <i class="fas fa-home"></i>
            <span v-if="!isCollapsed">Dashboard</span>
          </router-link>
        </li>

        <!-- Menu Admin -->
        <template v-if="isAdmin">
          <li class="nav-section" v-if="!isCollapsed">MASTER DATA</li>
          
          <li class="nav-item">
            <router-link to="/admin/unit-kerja" class="nav-link" active-class="active">
              <i class="fas fa-building"></i>
              <span v-if="!isCollapsed">Unit Kerja</span>
            </router-link>
          </li>

          <li class="nav-item">
            <router-link to="/admin/iku" class="nav-link" active-class="active">
              <i class="fas fa-chart-line"></i>
              <span v-if="!isCollapsed">IKU</span>
            </router-link>
          </li>

          <li class="nav-section" v-if="!isCollapsed">MANAJEMEN</li>

          <li class="nav-item">
            <router-link to="/admin/users" class="nav-link" active-class="active">
              <i class="fas fa-users-cog"></i>
              <span v-if="!isCollapsed">Kelola Akun</span>
            </router-link>
          </li>

          <li class="nav-item">
            <router-link to="/admin/dokumen" class="nav-link" active-class="active">
              <i class="fas fa-file-alt"></i>
              <span v-if="!isCollapsed">Kelola Dokumen</span>
            </router-link>
          </li>

          <li class="nav-section" v-if="!isCollapsed">KONTEN</li>

          <li class="nav-item">
            <router-link to="/admin/berita" class="nav-link" active-class="active">
              <i class="fas fa-newspaper"></i>
              <span v-if="!isCollapsed">Kelola Berita</span>
            </router-link>
          </li>

          <li class="nav-item">
            <router-link to="/admin/jadwal" class="nav-link" active-class="active">
              <i class="fas fa-calendar-alt"></i>
              <span v-if="!isCollapsed">Kelola Jadwal</span>
            </router-link>
          </li>
        </template>

        <!-- Menu Verifikator -->
        <template v-if="isVerifikator">
          <li class="nav-section" v-if="!isCollapsed">VERIFIKASI</li>

          <li class="nav-item">
            <router-link to="/verifikator/pending" class="nav-link" active-class="active">
              <i class="fas fa-clipboard-list"></i>
              <span v-if="!isCollapsed">Perlu Verifikasi</span>
              <span v-if="pendingCount > 0" class="badge warning">{{ pendingCount }}</span>
            </router-link>
          </li>

          <li class="nav-item">
            <router-link to="/verifikator/dokumen" class="nav-link" active-class="active">
              <i class="fas fa-file-alt"></i>
              <span v-if="!isCollapsed">Semua Dokumen</span>
            </router-link>
          </li>
        </template>

        <!-- Menu User -->
        <template v-if="isUser">
          <li class="nav-section" v-if="!isCollapsed">DOKUMEN SAYA</li>

          <li class="nav-item">
            <router-link to="/user/upload" class="nav-link" active-class="active">
              <i class="fas fa-cloud-upload-alt"></i>
              <span v-if="!isCollapsed">Upload Dokumen</span>
            </router-link>
          </li>

          <li class="nav-item">
            <router-link to="/user/dokumen-saya" class="nav-link" active-class="active">
              <i class="fas fa-folder-open"></i>
              <span v-if="!isCollapsed">Dokumen Saya</span>
              <span v-if="userDocumentsCount > 0" class="badge">{{ userDocumentsCount }}</span>
            </router-link>
          </li>
        </template>

        <!-- Menu SPMI PPEPP (Berlaku Semua Akses) -->
        <li class="nav-section" v-if="!isCollapsed">SIKLUS PPEPP</li>

        <li class="nav-item">
          <router-link to="/spmi/penetapan" class="nav-link" active-class="active">
            <i class="fas fa-pen-fancy"></i>
            <span v-if="!isCollapsed">Penetapan</span>
          </router-link>
        </li>

        <li class="nav-item">
          <router-link to="/spmi/pelaksanaan" class="nav-link" active-class="active">
            <i class="fas fa-play"></i>
            <span v-if="!isCollapsed">Pelaksanaan</span>
          </router-link>
        </li>

        <li class="nav-item">
          <router-link to="/spmi/evaluasi" class="nav-link" active-class="active">
            <i class="fas fa-chart-line"></i>
            <span v-if="!isCollapsed">Evaluasi</span>
          </router-link>
        </li>

        <li class="nav-item">
          <router-link to="/spmi/pengendalian" class="nav-link" active-class="active">
            <i class="fas fa-sliders-h"></i>
            <span v-if="!isCollapsed">Pengendalian</span>
          </router-link>
        </li>

        <li class="nav-item">
          <router-link to="/spmi/peningkatan" class="nav-link" active-class="active">
            <i class="fas fa-arrow-up"></i>
            <span v-if="!isCollapsed">Peningkatan</span>
          </router-link>
        </li>
      </ul>
    </nav>

    <!-- Sidebar Footer -->
    <div class="sidebar-footer">
      <button class="btn-toggle" @click="toggleCollapse" :title="isCollapsed ? 'Buka Sidebar' : 'Tutup Sidebar'">
        <i :class="isCollapsed ? 'fas fa-chevron-right' : 'fas fa-chevron-left'"></i>
      </button>
      <button class="btn-logout" @click="handleLogout" title="Keluar">
        <i class="fas fa-sign-out-alt"></i>
        <span v-if="!isCollapsed">Keluar</span>
      </button>
    </div>
  </aside>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

export default {
  name: 'Sidebar',
  props: {
    isOpen: {
      type: Boolean,
      default: false
    }
  },
  setup(props) {
    const router = useRouter()
    const isCollapsed = ref(false)
    const user = ref(null)
    const pendingCount = ref(0)
    const userDocumentsCount = ref(0)
    const showLogo = ref(true)

    // ===== SEDERHANA: Gunakan path yang pasti ada =====
    // Coba beberapa path alternatif
    const logoPaths = [
      '/images/photos/25600_Logo-IKIP-warna.png',
      '/logo-ikipsiliwangi.png',
      '/assets/logo-ikipsiliwangi.png',
      '/storage/images/logo-ikipsiliwangi.png'
    ]
    
    // Gunakan path pertama sebagai default
    const logoUrl = logoPaths[0]

    // Fallback jika gambar error
    const useFallback = (event) => {
      // Ganti dengan SVG inline sederhana
      const svg = `
        <svg width="42" height="42" viewBox="0 0 42 42" xmlns="http://www.w3.org/2000/svg">
          <rect width="42" height="42" rx="10" fill="#996600"/>
          <text x="21" y="28" text-anchor="middle" fill="white" font-size="20" font-weight="bold">E</text>
        </svg>
      `
      const encoded = btoa(svg)
      event.target.src = `data:image/svg+xml;base64,${encoded}`
      event.target.style.objectFit = 'contain'
      event.target.style.padding = '4px'
    }

    const userRole = computed(() => {
      if (!user.value) return 'Administrator'
      if (user.value.is_admin) return 'Administrator'
      if (user.value.is_verifikator) return 'Verifikator'
      return 'User'
    })

    const isAdmin = computed(() => {
      if (!user.value) return true
      return Boolean(user.value.is_admin)
    })

    const isVerifikator = computed(() => Boolean(user.value?.is_verifikator))
    
    const isUser = computed(() => {
      if (!user.value) return false
      return !user.value.is_admin && !user.value.is_verifikator
    })

    const roleClass = computed(() => {
      if (isAdmin.value) return 'admin'
      if (isVerifikator.value) return 'verifikator'
      return 'user'
    })

    const loadUserData = async () => {
      try {
        const userData = localStorage.getItem('user')
        if (userData) {
          user.value = JSON.parse(userData)
        }
        
        const response = await axios.get('/user')
        if (response.data && response.data.success) {
          user.value = response.data.data
          localStorage.setItem('user', JSON.stringify(user.value))
        }
      } catch (error) {
        console.warn('Gagal memuat profil user:', error)
      }
    }

    const toggleCollapse = () => {
      isCollapsed.value = !isCollapsed.value
      localStorage.setItem('sidebar_collapsed', isCollapsed.value)
    }

    const handleLogout = async () => {
      try {
        await axios.post('/logout')
      } catch (error) {
        console.error('Logout error:', error)
      } finally {
        localStorage.removeItem('token')
        localStorage.removeItem('user')
        delete axios.defaults.headers.common['Authorization']
        router.push('/pengelola/login')
      }
    }

    onMounted(() => {
      try {
        const savedState = localStorage.getItem('sidebar_collapsed')
        if (savedState !== null) {
          isCollapsed.value = savedState === 'true'
        }
        loadUserData()
      } catch (error) {
        console.error('Error on mount:', error)
      }
    })

    return {
      logoUrl,
      showLogo,
      isCollapsed,
      user,
      userRole,
      roleClass,
      isAdmin,
      isVerifikator,
      isUser,
      pendingCount,
      userDocumentsCount,
      toggleCollapse,
      handleLogout,
      useFallback
    }
  }
}
</script>

<style scoped>
.sidebar {
  width: 280px;
  height: 100vh;
  background: linear-gradient(180deg, #996600 0%, #5a3a00 100%);
  color: white;
  position: fixed;
  left: 0;
  top: 0;
  overflow-y: auto;
  box-shadow: 2px 0 10px rgba(0,0,0,0.1);
  transition: all 0.3s ease;
  z-index: 1050;
  display: flex;
  flex-direction: column;
}

.sidebar.collapsed {
  width: 72px;
}

.sidebar::-webkit-scrollbar {
  width: 4px;
}

.sidebar::-webkit-scrollbar-track {
  background: rgba(255,255,255,0.05);
}

.sidebar::-webkit-scrollbar-thumb {
  background: rgba(255,255,255,0.2);
  border-radius: 3px;
}

/* ===== SIDEBAR HEADER ===== */
.sidebar-header {
  padding: 20px 15px;
  border-bottom: 1px solid rgba(255,255,255,0.1);
  background: rgba(0,0,0,0.1);
  flex-shrink: 0;
}

.sidebar-brand {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 10px;
}

.sidebar-logo-placeholder {
  width: 42px;
  height: 42px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(255,255,255,0.15);
  color: white;
  font-weight: 700;
  font-size: 1rem;
  transition: all 0.3s ease;
  border: 1px solid rgba(255,255,255,0.2);
  overflow: hidden;
  flex-shrink: 0;
}

.brand-logo {
  width: 100%;
  height: 100%;
  object-fit: contain;
  padding: 4px;
}

.brand-initial {
  font-size: 18px;
  font-weight: 700;
  color: white;
}

.sidebar-logo-placeholder.small {
  width: 36px;
  height: 36px;
}

.sidebar-logo-placeholder.small .brand-initial {
  font-size: 14px;
}

.sidebar-brand-text h5 {
  margin: 0;
  font-weight: 700;
  color: white;
  font-size: 18px;
  line-height: 1.2;
}

.sidebar-brand-text small {
  color: rgba(255,255,255,0.8);
  font-size: 11px;
  display: block;
}

.sidebar-user-role {
  text-align: left;
}

.role-badge {
  display: inline-block;
  padding: 3px 10px;
  border-radius: 20px;
  font-size: 10px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.role-badge.admin {
  background: rgba(255,255,255,0.2);
  color: white;
  border: 1px solid rgba(255,255,255,0.3);
}

.role-badge.verifikator {
  background: #17a2b8;
  color: white;
}

.role-badge.user {
  background: #28a745;
  color: white;
}

.sidebar.collapsed .sidebar-brand-text,
.sidebar.collapsed .sidebar-user-role {
  display: none;
}

.sidebar.collapsed .sidebar-brand {
  justify-content: center;
  margin-bottom: 0;
}

/* ===== SIDEBAR MENU ===== */
.sidebar-menu {
  padding: 10px 0;
  flex: 1;
  overflow-y: auto;
}

.nav flex-column {
  padding-left: 0;
  margin-bottom: 0;
}

.nav-section {
  padding: 15px 18px 5px;
  color: rgba(255,255,255,0.55);
  font-size: 10.5px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.8px;
}

.sidebar.collapsed .nav-section {
  display: none;
}

.nav-item {
  list-style: none;
  margin: 2px 0;
}

.nav-link {
  display: flex;
  align-items: center;
  padding: 10px 15px;
  margin: 0 10px;
  color: rgba(255,255,255,0.85);
  text-decoration: none;
  transition: all 0.25s ease;
  gap: 12px;
  border-radius: 8px;
  font-size: 13.5px;
  cursor: pointer;
}

.nav-link:hover,
.nav-link.active {
  background: rgba(255,255,255,0.18);
  color: white;
  font-weight: 600;
}

.nav-link i {
  width: 20px;
  text-align: center;
  font-size: 15px;
  flex-shrink: 0;
}

.nav-link span {
  flex: 1;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.nav-link .badge {
  background: #dc3545;
  color: white;
  padding: 2px 7px;
  border-radius: 10px;
  font-size: 10px;
  font-weight: 600;
}

.nav-link .badge.warning {
  background: #ffc107;
  color: #343a40;
}

.sidebar.collapsed .nav-link {
  padding: 10px;
  justify-content: center;
  margin: 0 8px;
}

.sidebar.collapsed .nav-link span,
.sidebar.collapsed .nav-link .badge {
  display: none;
}

/* ===== SIDEBAR FOOTER ===== */
.sidebar-footer {
  padding: 12px 15px;
  border-top: 1px solid rgba(255,255,255,0.1);
  background: rgba(0,0,0,0.15);
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-shrink: 0;
  gap: 8px;
}

.btn-toggle,
.btn-logout {
  background: rgba(255,255,255,0.1);
  border: none;
  color: rgba(255,255,255,0.85);
  padding: 8px 12px;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.25s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  font-size: 13.5px;
  flex: 1;
}

.btn-toggle:hover {
  background: rgba(255,255,255,0.22);
  color: white;
}

.btn-logout {
  color: #ff8080;
}

.btn-logout:hover {
  background: rgba(220, 53, 69, 0.25);
  color: #ffffff;
}

.sidebar.collapsed .btn-toggle {
  padding: 8px;
}

.sidebar.collapsed .btn-logout span {
  display: none;
}

/* ===== RESPONSIVE ===== */
@media (max-width: 991.98px) {
  .sidebar {
    left: -280px;
  }
  
  .sidebar.show {
    left: 0;
  }
}
</style>