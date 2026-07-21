<template>
  <div class="admin-layout">
    <!-- Sidebar Overlay untuk mobile -->
    <div 
      class="sidebar-overlay" 
      :class="{ 'active': isSidebarOpen }"
      @click="closeSidebar"
    ></div>

    <!-- Sidebar Component -->
    <Sidebar ref="sidebarRef" />

    <!-- Main Content -->
    <div class="main-content" :class="{ 'sidebar-collapsed': isSidebarCollapsed }">
      <!-- Topbar -->
      <header class="topbar">
        <div class="topbar-left">
          <button class="menu-toggle" @click="toggleSidebar">
            <i class="fas fa-bars"></i>
          </button>
          <h5 class="page-title">
            <i class="fas" :class="pageIcon"></i>
            {{ pageTitle }}
          </h5>
        </div>

        <div class="topbar-right">
          <!-- Search Box -->
          <div class="search-box">
            <i class="fas fa-search"></i>
            <input 
              type="text" 
              placeholder="Cari..." 
              v-model="searchQuery"
              @keyup.enter="handleSearch"
            >
          </div>

          <!-- Notifications -->
          <div class="notification-icon" @click="toggleNotifications">
            <i class="far fa-bell"></i>
            <span v-if="notificationCount > 0" class="notification-badge">
              {{ notificationCount }}
            </span>
          </div>

          <!-- User Profile -->
          <div class="user-profile" @click.stop="toggleUserMenu">
            <div class="user-avatar" :class="avatarColor">
              {{ userInitial }}
            </div>
            <div class="user-info">
              <span class="user-name">{{ userName }}</span>
              <span class="user-role">{{ userRole }}</span>
            </div>
            <i class="fas fa-chevron-down user-dropdown-arrow" :class="{ 'rotated': isUserMenuOpen }"></i>
          </div>

          <!-- User Dropdown Menu -->
          <div class="user-dropdown" :class="{ 'show': isUserMenuOpen }">
            <router-link to="/profile" class="dropdown-item">
              <i class="fas fa-user"></i> Profil Saya
            </router-link>
            <router-link to="/profile/change-password" class="dropdown-item">
              <i class="fas fa-key"></i> Ubah Password
            </router-link>
            <div class="dropdown-divider"></div>
            <button class="dropdown-item text-danger" @click="handleLogout">
              <i class="fas fa-sign-out-alt"></i> Keluar
            </button>
          </div>
        </div>
      </header>

      <!-- Content Area -->
      <main class="content-area">
        <!-- Flash Messages -->
        <div v-if="flashMessage" class="alert" :class="flashType">
          <i :class="flashIcon"></i>
          {{ flashMessage }}
          <button class="btn-close" @click="clearFlash"></button>
        </div>

        <slot />
      </main>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import Sidebar from './Sidebar.vue'
import axios from 'axios'

export default {
  name: 'AdminLayout',
  components: {
    Sidebar
  },
  props: {
    pageTitle: {
      type: String,
      default: 'Dashboard'
    },
    pageIcon: {
      type: String,
      default: 'fa-tachometer-alt'
    }
  },
  setup(props) {
    const router = useRouter()
    const route = useRoute()
    const sidebarRef = ref(null)
    const isSidebarOpen = ref(false)
    const isUserMenuOpen = ref(false)
    const searchQuery = ref('')
    const flashMessage = ref('')
    const flashType = ref('alert-success')
    const user = ref(null)
    const notificationCount = ref(0)

    // User computed properties
    const userName = computed(() => user.value?.name || 'User')
    const userRole = computed(() => {
      if (!user.value) return 'User'
      return user.value.is_admin ? 'Administrator' : 
             user.value.is_verifikator ? 'Verifikator' : 'User'
    })
    const userInitial = computed(() => {
      if (!user.value?.name) return 'U'
      return user.value.name.charAt(0).toUpperCase()
    })
    const avatarColor = computed(() => {
      const colors = ['avatar-0', 'avatar-1', 'avatar-2', 'avatar-3', 'avatar-4', 'avatar-5']
      const index = user.value?.id ? user.value.id % 6 : 0
      return colors[index]
    })

    const isSidebarCollapsed = computed(() => {
      return sidebarRef.value?.isCollapsed || false
    })

    const flashIcon = computed(() => {
      const icons = {
        'alert-success': 'fas fa-check-circle',
        'alert-danger': 'fas fa-exclamation-circle',
        'alert-warning': 'fas fa-exclamation-triangle',
        'alert-info': 'fas fa-info-circle'
      }
      return icons[flashType.value] || 'fas fa-info-circle'
    })

    // Load user data
    const loadUser = async () => {
      try {
        const userData = localStorage.getItem('user')
        if (userData) {
          user.value = JSON.parse(userData)
        }
        
        const response = await axios.get('/user')
        if (response.data?.success) {
          user.value = response.data.data
          localStorage.setItem('user', JSON.stringify(user.value))
        }
      } catch (error) {
        console.error('Error loading user:', error)
        if (error.response?.status === 401) {
          localStorage.removeItem('token')
          localStorage.removeItem('user')
          router.push('/pengelola/login')
        }
      }
    }

    // Toggle sidebar
    const toggleSidebar = () => {
      if (window.innerWidth <= 992) {
        isSidebarOpen.value = !isSidebarOpen.value
        const sidebar = document.querySelector('.sidebar')
        if (sidebar) {
          sidebar.classList.toggle('show')
        }
      } else {
        sidebarRef.value?.toggleCollapse()
      }
    }

    const closeSidebar = () => {
      isSidebarOpen.value = false
      const sidebar = document.querySelector('.sidebar')
      if (sidebar) {
        sidebar.classList.remove('show')
      }
    }

    // Toggle user menu
    const toggleUserMenu = () => {
      isUserMenuOpen.value = !isUserMenuOpen.value
    }

    // Toggle notifications
    const toggleNotifications = () => {
      console.log('Toggle notifications')
    }

    // Handle search
    const handleSearch = () => {
      if (searchQuery.value.trim()) {
        router.push({
          path: '/search',
          query: { q: searchQuery.value }
        })
      }
    }

    // Handle logout
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

    // Flash message
    const setFlash = (message, type = 'success') => {
      flashMessage.value = message
      flashType.value = `alert-${type}`
      
      setTimeout(() => {
        clearFlash()
      }, 5000)
    }

    const clearFlash = () => {
      flashMessage.value = ''
    }

    // Listen to route changes for flash messages
    watch(
      () => route.query,
      (newQuery) => {
        if (newQuery.flash) {
          setFlash(newQuery.flash, newQuery.type || 'success')
          router.replace({ query: {} })
        }
      },
      { immediate: true }
    )

    // Close user menu when clicking outside
    onMounted(() => {
      document.addEventListener('click', (event) => {
        const userProfile = document.querySelector('.user-profile')
        const userDropdown = document.querySelector('.user-dropdown')
        if (userProfile && userDropdown) {
          if (!userProfile.contains(event.target) && !userDropdown.contains(event.target)) {
            isUserMenuOpen.value = false
          }
        }
      })

      loadUser()
    })

    return {
      sidebarRef,
      isSidebarOpen,
      isSidebarCollapsed,
      isUserMenuOpen,
      searchQuery,
      flashMessage,
      flashType,
      flashIcon,
      user,
      userName,
      userRole,
      userInitial,
      avatarColor,
      notificationCount,
      toggleSidebar,
      closeSidebar,
      toggleUserMenu,
      toggleNotifications,
      handleSearch,
      handleLogout,
      setFlash,
      clearFlash
    }
  }
}
</script>

<style scoped>
@import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');

.admin-layout {
  display: flex;
  min-height: 100vh;
  width: 100%;
  background: #f4f6f9;
}

/* ===== SIDEBAR OVERLAY ===== */
.sidebar-overlay {
  display: none;
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0,0,0,0.5);
  z-index: 1045;
  backdrop-filter: blur(2px);
}

.sidebar-overlay.active {
  display: block;
}

/* ===== MAIN CONTENT ===== */
.main-content {
  flex: 1;
  margin-left: 280px;
  min-height: 100vh;
  transition: margin-left 0.3s ease;
  display: flex;
  flex-direction: column;
  width: calc(100% - 280px);
}

.main-content.sidebar-collapsed {
  margin-left: 72px;
  width: calc(100% - 72px);
}

/* ===== TOPBAR ===== */
.topbar {
  background: white;
  padding: 0.75rem 2rem;
  box-shadow: 0 2px 10px rgba(0,0,0,0.05);
  display: flex;
  justify-content: space-between;
  align-items: center;
  position: sticky;
  top: 0;
  z-index: 1040;
  min-height: 65px;
}

.topbar-left {
  display: flex;
  align-items: center;
  gap: 15px;
}

.menu-toggle {
  background: none;
  border: none;
  font-size: 1.3rem;
  color: #6c757d;
  padding: 0.5rem;
  border-radius: 8px;
  transition: all 0.3s ease;
  display: none;
  cursor: pointer;
}

.menu-toggle:hover {
  background: #f8f9fa;
  color: #996600;
}

.page-title {
  font-size: 1.1rem;
  font-weight: 600;
  color: #2c3e50;
  margin: 0;
  display: flex;
  align-items: center;
  gap: 10px;
}

.page-title i {
  color: #996600;
}

.topbar-right {
  display: flex;
  align-items: center;
  gap: 15px;
  position: relative;
}

/* Search Box */
.search-box {
  background: #f8f9fa;
  border: 1px solid #e9ecef;
  border-radius: 8px;
  padding: 6px 12px;
  display: flex;
  align-items: center;
  width: 250px;
  transition: all 0.3s ease;
}

.search-box:focus-within {
  border-color: #996600;
  box-shadow: 0 0 0 3px rgba(153, 102, 0, 0.1);
}

.search-box i {
  color: #adb5bd;
  margin-right: 10px;
}

.search-box input {
  background: transparent;
  border: none;
  outline: none;
  width: 100%;
  font-size: 0.9rem;
}

/* Notification Icon */
.notification-icon {
  position: relative;
  color: #6c757d;
  font-size: 1.2rem;
  cursor: pointer;
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 8px;
  transition: all 0.3s ease;
}

.notification-icon:hover {
  background: #f8f9fa;
}

.notification-badge {
  position: absolute;
  top: 5px;
  right: 5px;
  background: #dc3545;
  color: white;
  border-radius: 50%;
  width: 18px;
  height: 18px;
  font-size: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* User Profile */
.user-profile {
  display: flex;
  align-items: center;
  gap: 10px;
  cursor: pointer;
  padding: 5px 12px;
  border-radius: 8px;
  transition: all 0.3s ease;
  background: #fff9e6;
  position: relative;
}

.user-profile:hover {
  background: #ffe6cc;
}

.user-avatar {
  width: 35px;
  height: 35px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: bold;
  font-size: 14px;
  flex-shrink: 0;
}

.avatar-0 { background: #996600; }
.avatar-1 { background: #aa7700; }
.avatar-2 { background: #bb8800; }
.avatar-3 { background: #cc9900; }
.avatar-4 { background: #ddaa00; }
.avatar-5 { background: #eebb00; }

.user-info {
  display: flex;
  flex-direction: column;
  line-height: 1.2;
}

.user-name {
  font-weight: 600;
  font-size: 0.85rem;
  color: #2c3e50;
}

.user-role {
  font-size: 0.7rem;
  color: #6c757d;
}

.user-dropdown-arrow {
  font-size: 10px;
  color: #6c757d;
  transition: transform 0.3s ease;
}

.user-dropdown-arrow.rotated {
  transform: rotate(180deg);
}

/* User Dropdown */
.user-dropdown {
  position: absolute;
  top: 100%;
  right: 0;
  margin-top: 8px;
  background: white;
  border-radius: 10px;
  box-shadow: 0 5px 25px rgba(0,0,0,0.15);
  width: 200px;
  display: none;
  z-index: 1060;
  border: 1px solid #e9ecef;
  overflow: hidden;
}

.user-dropdown.show {
  display: block;
}

.dropdown-item {
  padding: 10px 16px;
  color: #2c3e50;
  text-decoration: none;
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 0.9rem;
  background: none;
  border: none;
  width: 100%;
  cursor: pointer;
  transition: all 0.3s ease;
}

.dropdown-item:hover {
  background: #fff9e6;
}

.dropdown-item i {
  width: 18px;
  color: #996600;
}

.dropdown-divider {
  height: 1px;
  background: #e9ecef;
  margin: 5px 0;
}

/* ===== CONTENT AREA ===== */
.content-area {
  padding: 20px;
  flex: 1;
}

/* ===== ALERT ===== */
.alert {
  border-radius: 8px;
  border: none;
  box-shadow: 0 4px 12px rgba(0,0,0,0.05);
  padding: 12px 15px;
  margin-bottom: 20px;
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 14px;
  position: relative;
}

.alert i {
  font-size: 1.1rem;
}

.alert-success {
  background: #d4edda;
  color: #155724;
  border-left: 4px solid #28a745;
}

.alert-danger {
  background: #f8d7da;
  color: #721c24;
  border-left: 4px solid #dc3545;
}

.alert-warning {
  background: #fff3cd;
  color: #856404;
  border-left: 4px solid #ffc107;
}

.alert-info {
  background: #d1ecf1;
  color: #0c5460;
  border-left: 4px solid #17a2b8;
}

.alert .btn-close {
  margin-left: auto;
  background: none;
  border: none;
  font-size: 1.2rem;
  cursor: pointer;
  opacity: 0.5;
}

.alert .btn-close:hover {
  opacity: 1;
}

/* ===== RESPONSIVE ===== */
@media (max-width: 991.98px) {
  .main-content,
  .main-content.sidebar-collapsed {
    margin-left: 0;
    width: 100%;
  }
  
  .menu-toggle {
    display: flex;
    align-items: center;
    justify-content: center;
  }
  
  .search-box {
    width: 200px;
  }
}

@media (max-width: 767.98px) {
  .topbar {
    padding: 0.5rem 1rem;
  }
  
  .page-title {
    font-size: 0.95rem;
  }
  
  .search-box {
    width: 150px;
  }
  
  .user-info {
    display: none;
  }
  
  .user-profile {
    padding: 5px 8px;
    background: transparent;
  }
  
  .user-profile:hover {
    background: #f8f9fa;
  }
  
  .content-area {
    padding: 15px;
  }
}

@media (max-width: 575.98px) {
  .topbar {
    padding: 0.5rem;
  }
  
  .page-title {
    font-size: 0.85rem;
  }
  
  .search-box {
    display: none;
  }
  
  .notification-icon {
    width: 35px;
    height: 35px;
    font-size: 1rem;
  }
  
  .user-avatar {
    width: 30px;
    height: 30px;
    font-size: 12px;
  }
  
  .content-area {
    padding: 10px;
  }
}
</style>