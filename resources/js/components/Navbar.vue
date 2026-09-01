<!-- src/components/Navbar.vue -->
<template>
  <nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
      <router-link class="navbar-brand d-flex align-items-center" to="/">
        <img 
          :src="logoUrl" 
          alt="Logo IKIP"
          style="height:40px; width:auto; object-fit:contain;"
          @error="handleImageError"
        >
        <span class="ms-2">SPMI</span>
      </router-link>
      
      <button 
        class="navbar-toggler" 
        type="button" 
        data-bs-toggle="collapse" 
        data-bs-target="#navbarNav"
        aria-controls="navbarNav"
        aria-expanded="false"
        aria-label="Toggle navigation"
      >
        <span class="navbar-toggler-icon"></span>
      </button>
      
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <router-link class="nav-link fw-medium" to="/">Beranda</router-link>
          </li>
          
          <li class="nav-item dropdown">
            <a 
              class="nav-link dropdown-toggle fw-medium" 
              :class="{ 'router-link-active': isTentangActive }"
              href="#" 
              id="navbarTentangKami" 
              role="button" 
              data-bs-toggle="dropdown" 
              aria-expanded="false"
            >
              Tentang
            </a>
            <ul class="dropdown-menu border-0 shadow-sm" aria-labelledby="navbarTentangKami">
              <li><router-link class="dropdown-item" to="/tentang/profil">Profil LPMI</router-link></li>
              <li><router-link class="dropdown-item" to="/tentang/visi-misi">Visi & Misi</router-link></li>
              <li><router-link class="dropdown-item" to="/tentang/siklus-ppepp">Siklus PPEPP</router-link></li>
            </ul>
          </li>
          
          <li class="nav-item">
            <router-link class="nav-link fw-medium" to="/berita">Berita</router-link>
          </li>

          <li class="nav-item">
            <router-link class="nav-link fw-medium" to="/dokumen-publik">Dokumen Publik</router-link>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</template>

<script>
import { ref, computed } from 'vue'
import { useRoute } from 'vue-router'

export default {
  name: 'AppNavbar',
  setup() {
    const route = useRoute()
    const logoUrl = ref('/images/photos/logo-ikipsiliwangi.png')
    
    const isTentangActive = computed(() => {
      return route.path.startsWith('/tentang')
    })
    
    const handleImageError = (event) => {
      event.target.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="%23996600" stroke-width="2"%3E%3Cpath d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"%3E%3C/path%3E%3C/svg%3E'
    }
    
    return {
      logoUrl,
      isTentangActive,
      handleImageError
    }
  }
}
</script>

<style>
/* ===== NAVBAR GLOBAL STYLES ===== */
.navbar.navbar-expand-lg.fixed-top {
  background: rgba(255, 255, 255, 0.95) !important;
  backdrop-filter: blur(10px);
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  z-index: 1050 !important;
}

.navbar .navbar-brand {
  font-weight: 700;
  color: #7a5200 !important;
  font-size: 1.5rem;
}

.navbar .nav-link {
  font-weight: 500;
  color: #374151 !important;
  margin: 0 10px;
}

.navbar .nav-link:hover {
  color: #7a5200 !important;
}

.navbar .dropdown-menu {
  border: none;
  border-radius: 8px;
}

.navbar .dropdown-item:hover {
  background-color: #fef3d6;
  color: #7a5200;
}
</style>