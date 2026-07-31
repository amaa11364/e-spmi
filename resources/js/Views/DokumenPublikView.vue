<template>
  <div class="dokumen-publik-page">
    <!-- Navbar -->
    <nav class="navbar">
      <div class="nav-container">
         <a class="navbar-brand d-flex align-items-center" href="#">
          <img
            :src="'/images/photos/logo-ikipsiliwangi.png'"
            alt="Logo E-SPMI"
            width="40"
            height="40"
            class="me-2"
            @error="handleImageError"
          >
          <div class="brand-text">
            <span class="brand-title">E-SPMI</span>
            <span class="brand-subtitle">IKIP SILIWANGI</span>
          </div>
        </a>
        <div class="nav-links">
          <router-link to="/" class="nav-link">Beranda</router-link>
          <router-link to="/berita" class="nav-link">Berita</router-link>
          <router-link to="/dokumen-publik" class="nav-link active">Dokumen</router-link>
          <router-link to="/pengelola/login" class="nav-link btn-login">
            <i class="fas fa-sign-in-alt"></i> Login
          </router-link>
        </div>
        <button class="mobile-toggle" @click="mobileMenuOpen = !mobileMenuOpen">
          <i :class="mobileMenuOpen ? 'fas fa-times' : 'fas fa-bars'"></i>
        </button>
      </div>
      <!-- Mobile Menu -->
      <div class="mobile-menu" :class="{ open: mobileMenuOpen }">
        <router-link to="/" class="mobile-link" @click="mobileMenuOpen = false">Beranda</router-link>
        <router-link to="/berita" class="mobile-link" @click="mobileMenuOpen = false">Berita</router-link>
        <router-link to="/dokumen-publik" class="mobile-link active" @click="mobileMenuOpen = false">Dokumen</router-link>
        <router-link to="/pengelola/login" class="mobile-link" @click="mobileMenuOpen = false">
          <i class="fas fa-sign-in-alt"></i> Login
        </router-link>
      </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
      <div class="hero-bg-pattern"></div>
      <div class="hero-content">
        <span class="hero-badge">
          <i class="fas fa-folder-open"></i> Dokumen Publik
        </span>
        <h1>Dokumen Publik SPMI</h1>
        <p>Akses dokumen-dokumen resmi terkait Sistem Penjaminan Mutu Internal yang tersedia untuk publik.</p>
        <!-- Search -->
        <div class="hero-search">
          <i class="fas fa-search"></i>
          <input 
            type="text" 
            v-model="searchQuery" 
            placeholder="Cari dokumen atau folder..."
            @input="handleSearch"
          >
        </div>
      </div>
    </section>

    <!-- Stats Bar -->
    <section class="stats-bar">
      <div class="stats-container">
        <div class="stat-item">
          <i class="fas fa-folder"></i>
          <div>
            <span class="stat-number">{{ folders.length }}</span>
            <span class="stat-label">Folder</span>
          </div>
        </div>
        <div class="stat-item">
          <i class="fas fa-file-alt"></i>
          <div>
            <span class="stat-number">{{ totalFiles }}</span>
            <span class="stat-label">Dokumen</span>
          </div>
        </div>
      </div>
    </section>

    <!-- Main Content -->
    <section class="content-section">
      <div class="content-container">
        <!-- Loading -->
        <div v-if="loading" class="loading-state">
          <div class="spinner"></div>
          <p>Memuat dokumen...</p>
        </div>

        <!-- Empty State -->
        <div v-else-if="filteredFolders.length === 0" class="empty-state">
          <div class="empty-icon">
            <i class="fas fa-folder-open"></i>
          </div>
          <h3>Belum Ada Dokumen Publik</h3>
          <p v-if="searchQuery">Tidak ditemukan dokumen yang sesuai dengan pencarian "{{ searchQuery }}"</p>
          <p v-else>Dokumen publik belum tersedia saat ini. Silakan cek kembali nanti.</p>
        </div>

        <!-- Folder Grid -->
        <div v-else class="folders-grid">
          <div 
            v-for="folder in filteredFolders" 
            :key="folder.id" 
            class="folder-card"
            :class="{ expanded: expandedFolder === folder.id }"
          >
            <!-- Folder Header -->
            <div class="folder-header" @click="toggleFolder(folder.id)">
              <div class="folder-icon-wrapper">
                <i class="fas fa-folder" :class="{ 'fa-folder-open': expandedFolder === folder.id }"></i>
              </div>
              <div class="folder-info">
                <h3>{{ folder.nama }}</h3>
                <p v-if="folder.deskripsi">{{ folder.deskripsi }}</p>
                <span class="file-count">
                  <i class="fas fa-file"></i>
                  {{ folder.public_files ? folder.public_files.length : 0 }} Dokumen
                </span>
              </div>
              <div class="expand-icon">
                <i class="fas fa-chevron-down" :class="{ rotated: expandedFolder === folder.id }"></i>
              </div>
            </div>

            <!-- Folder Content (Files) -->
            <transition name="slide">
              <div v-if="expandedFolder === folder.id" class="folder-content">
                <div v-if="!folder.public_files || folder.public_files.length === 0" class="no-files">
                  <i class="fas fa-inbox"></i>
                  <p>Belum ada dokumen di folder ini</p>
                </div>
                <div v-else class="files-list">
                  <div 
                    v-for="file in folder.public_files" 
                    :key="file.id"
                    class="file-item"
                  >
                    <div class="file-icon" :class="getFileIconClass(file.file_type)">
                      <i :class="getFileIcon(file.file_type)"></i>
                    </div>
                    <div class="file-details">
                      <span class="file-name">{{ file.nama }}</span>
                      <span class="file-meta">
                        {{ formatFileSize(file.file_size) }} • {{ formatDate(file.created_at) }}
                      </span>
                    </div>
                    <a 
                      :href="'/api/dokumen/files/' + file.id + '/download'" 
                      class="btn-download"
                      target="_blank"
                    >
                      <i class="fas fa-download"></i>
                      <span>Unduh</span>
                    </a>
                  </div>
                </div>
              </div>
            </transition>
          </div>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
      <div class="footer-container">
        <div class="footer-brand">
         <a class="footer-brand d-flex align-items-center" href="#">
          <img
            :src="'/images/photos/logo-ikipsiliwangi.png'"
            alt="Logo E-SPMI"
            width="40"
            height="40"
            class="me-2"
            @error="handleImageError"
          >
          <div>
            <h4>E-SPMI</h4>
            <p>Sistem Penjaminan Mutu Internal</p>
          </div>
          </a>
        </div>
        <div class="footer-links">
          <router-link to="/">Beranda</router-link>
          <router-link to="/berita">Berita</router-link>
          <router-link to="/dokumen-publik">Dokumen</router-link>
        </div>
        <div class="footer-copy">
          <p>&copy; {{ currentYear }} E-SPMI. All rights reserved.</p>
        </div>
      </div>
    </footer>
  </div>
</template>

<script>
import axios from '@/main'

export default {
  name: 'DokumenPublikView',
  data() {
    return {
      folders: [],
      loading: true,
      searchQuery: '',
      expandedFolder: null,
      mobileMenuOpen: false,
      searchTimeout: null
    }
  },
  computed: {
    currentYear() {
      return new Date().getFullYear()
    },
    totalFiles() {
      return this.folders.reduce((total, folder) => {
        return total + (folder.public_files ? folder.public_files.length : 0)
      }, 0)
    },
    filteredFolders() {
      if (!this.searchQuery) return this.folders
      const q = this.searchQuery.toLowerCase()
      return this.folders.filter(folder => {
        const nameMatch = folder.nama.toLowerCase().includes(q)
        const descMatch = folder.deskripsi && folder.deskripsi.toLowerCase().includes(q)
        const fileMatch = folder.public_files && folder.public_files.some(f => f.nama.toLowerCase().includes(q))
        return nameMatch || descMatch || fileMatch
      })
    }
  },
  mounted() {
    this.fetchDokumen()
  },
  methods: {
    async fetchDokumen() {
      this.loading = true
      try {
        const response = await axios.get('/dokumen/public')
        if (response.data.success) {
          this.folders = response.data.data
        }
      } catch (error) {
        console.error('Error fetching dokumen:', error)
      } finally {
        this.loading = false
      }
    },
    toggleFolder(folderId) {
      this.expandedFolder = this.expandedFolder === folderId ? null : folderId
    },
    handleSearch() {
      clearTimeout(this.searchTimeout)
      this.searchTimeout = setTimeout(() => {
        // Client-side filtering is handled by computed property
      }, 300)
    },
    getFileIcon(mimeType) {
      if (!mimeType) return 'fas fa-file'
      if (mimeType.includes('pdf')) return 'fas fa-file-pdf'
      if (mimeType.includes('word') || mimeType.includes('document')) return 'fas fa-file-word'
      if (mimeType.includes('excel') || mimeType.includes('spreadsheet')) return 'fas fa-file-excel'
      if (mimeType.includes('powerpoint') || mimeType.includes('presentation')) return 'fas fa-file-powerpoint'
      if (mimeType.includes('image')) return 'fas fa-file-image'
      if (mimeType.includes('zip') || mimeType.includes('rar') || mimeType.includes('archive')) return 'fas fa-file-archive'
      if (mimeType.includes('video')) return 'fas fa-file-video'
      if (mimeType.includes('audio')) return 'fas fa-file-audio'
      if (mimeType.includes('text')) return 'fas fa-file-alt'
      return 'fas fa-file'
    },
    getFileIconClass(mimeType) {
      if (!mimeType) return 'icon-default'
      if (mimeType.includes('pdf')) return 'icon-pdf'
      if (mimeType.includes('word') || mimeType.includes('document')) return 'icon-word'
      if (mimeType.includes('excel') || mimeType.includes('spreadsheet')) return 'icon-excel'
      if (mimeType.includes('powerpoint') || mimeType.includes('presentation')) return 'icon-ppt'
      if (mimeType.includes('image')) return 'icon-image'
      if (mimeType.includes('zip') || mimeType.includes('rar') || mimeType.includes('archive')) return 'icon-archive'
      return 'icon-default'
    },
    formatFileSize(bytes) {
      if (!bytes) return '0 B'
      const k = 1024
      const sizes = ['B', 'KB', 'MB', 'GB']
      const i = Math.floor(Math.log(bytes) / Math.log(k))
      return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i]
    },
    formatDate(dateStr) {
      if (!dateStr) return ''
      const date = new Date(dateStr)
      return date.toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
      })
    }
  }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

.dokumen-publik-page {
  font-family: 'Inter', sans-serif;
  color: #1e293b;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

/* ===== NAVBAR ===== */
.navbar {
  background: white;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
  position: sticky;
  top: 0;
  z-index: 100;
}

.nav-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 24px;
  height: 70px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.nav-brand {
  display: flex;
  align-items: center;
  gap: 12px;
  text-decoration: none;
}

.brand-icon {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  background: linear-gradient(135deg, #996600, #cc9900);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.1rem;
}

.brand-title {
  font-size: 1.2rem;
  font-weight: 700;
  color: #1e293b;
  display: block;
  line-height: 1.2;
}

.brand-subtitle {
  font-size: 0.7rem;
  color: #94a3b8;
  display: block;
}

.nav-links {
  display: flex;
  align-items: center;
  gap: 8px;
}

.nav-link {
  padding: 8px 16px;
  color: #64748b;
  text-decoration: none;
  font-size: 0.9rem;
  font-weight: 500;
  border-radius: 8px;
  transition: all 0.2s;
}

.nav-link:hover,
.nav-link.active {
  color: #996600;
  background: rgba(153, 102, 0, 0.06);
}

.btn-login {
  background: linear-gradient(135deg, #996600, #b37700) !important;
  color: white !important;
  padding: 8px 20px !important;
}

.btn-login:hover {
  background: linear-gradient(135deg, #7a5200, #996600) !important;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(153, 102, 0, 0.3);
}

.mobile-toggle {
  display: none;
  background: none;
  border: none;
  font-size: 1.3rem;
  color: #64748b;
  cursor: pointer;
}

.mobile-menu {
  display: none;
  flex-direction: column;
  padding: 0 24px 16px;
  gap: 4px;
}

.mobile-menu.open {
  display: flex;
}

.mobile-link {
  padding: 12px 16px;
  color: #475569;
  text-decoration: none;
  border-radius: 8px;
  font-size: 0.9rem;
  transition: all 0.2s;
}

.mobile-link:hover,
.mobile-link.active {
  background: rgba(153, 102, 0, 0.06);
  color: #996600;
}

@media (max-width: 768px) {
  .nav-links { display: none; }
  .mobile-toggle { display: block; }
}

/* ===== HERO SECTION ===== */
.hero-section {
  background: linear-gradient(135deg, #1e293b 0%, #334155 50%, #475569 100%);
  padding: 80px 24px 60px;
  position: relative;
  overflow: hidden;
}

.hero-bg-pattern {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-image: radial-gradient(circle at 20% 50%, rgba(153, 102, 0, 0.15) 0%, transparent 50%),
                    radial-gradient(circle at 80% 20%, rgba(204, 153, 0, 0.1) 0%, transparent 50%);
}

.hero-content {
  max-width: 700px;
  margin: 0 auto;
  text-align: center;
  position: relative;
  z-index: 1;
}

.hero-badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: rgba(153, 102, 0, 0.2);
  color: #fbbf24;
  padding: 8px 20px;
  border-radius: 24px;
  font-size: 0.85rem;
  font-weight: 600;
  margin-bottom: 20px;
  border: 1px solid rgba(153, 102, 0, 0.3);
}

.hero-content h1 {
  font-size: 2.5rem;
  font-weight: 800;
  color: white;
  margin-bottom: 12px;
  line-height: 1.2;
}

.hero-content p {
  font-size: 1.05rem;
  color: #94a3b8;
  margin-bottom: 32px;
  line-height: 1.7;
}

.hero-search {
  max-width: 500px;
  margin: 0 auto;
  position: relative;
}

.hero-search i {
  position: absolute;
  left: 20px;
  top: 50%;
  transform: translateY(-50%);
  color: #94a3b8;
  font-size: 1rem;
}

.hero-search input {
  width: 100%;
  padding: 16px 20px 16px 50px;
  border: 2px solid rgba(255, 255, 255, 0.1);
  border-radius: 14px;
  background: rgba(255, 255, 255, 0.08);
  color: white;
  font-size: 0.95rem;
  transition: all 0.3s;
  backdrop-filter: blur(10px);
}

.hero-search input::placeholder {
  color: #94a3b8;
}

.hero-search input:focus {
  outline: none;
  border-color: #cc9900;
  background: rgba(255, 255, 255, 0.12);
  box-shadow: 0 0 0 4px rgba(204, 153, 0, 0.15);
}

@media (max-width: 768px) {
  .hero-section { padding: 50px 16px 40px; }
  .hero-content h1 { font-size: 1.8rem; }
}

/* ===== STATS BAR ===== */
.stats-bar {
  background: white;
  border-bottom: 1px solid #e2e8f0;
}

.stats-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px 24px;
  display: flex;
  gap: 40px;
}

.stat-item {
  display: flex;
  align-items: center;
  gap: 12px;
}

.stat-item i {
  width: 42px;
  height: 42px;
  border-radius: 10px;
  background: rgba(153, 102, 0, 0.08);
  color: #996600;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1rem;
}

.stat-number {
  font-size: 1.5rem;
  font-weight: 700;
  color: #1e293b;
  display: block;
  line-height: 1.2;
}

.stat-label {
  font-size: 0.8rem;
  color: #94a3b8;
  display: block;
}

/* ===== CONTENT ===== */
.content-section {
  flex: 1;
  background: #f8fafc;
  padding: 40px 24px;
}

.content-container {
  max-width: 1200px;
  margin: 0 auto;
}

/* Loading */
.loading-state {
  text-align: center;
  padding: 80px 20px;
}

.spinner {
  width: 44px;
  height: 44px;
  border: 3px solid #e2e8f0;
  border-top: 3px solid #996600;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
  margin: 0 auto 16px;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.loading-state p {
  color: #94a3b8;
  font-size: 0.95rem;
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: 80px 20px;
}

.empty-icon {
  width: 80px;
  height: 80px;
  border-radius: 20px;
  background: rgba(153, 102, 0, 0.08);
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 20px;
}

.empty-icon i {
  font-size: 2rem;
  color: #cc9900;
}

.empty-state h3 {
  font-size: 1.3rem;
  color: #334155;
  margin-bottom: 8px;
}

.empty-state p {
  color: #94a3b8;
  font-size: 0.95rem;
  max-width: 400px;
  margin: 0 auto;
}

/* Folder Grid */
.folders-grid {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.folder-card {
  background: white;
  border-radius: 16px;
  border: 1px solid #e2e8f0;
  overflow: hidden;
  transition: all 0.3s ease;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04);
}

.folder-card:hover {
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  border-color: #cbd5e1;
}

.folder-card.expanded {
  border-color: #cc9900;
  box-shadow: 0 4px 20px rgba(153, 102, 0, 0.1);
}

.folder-header {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 20px 24px;
  cursor: pointer;
  transition: background 0.2s;
}

.folder-header:hover {
  background: #fefce8;
}

.folder-icon-wrapper {
  width: 50px;
  height: 50px;
  border-radius: 12px;
  background: linear-gradient(135deg, #fef3c7, #fde68a);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.folder-icon-wrapper i {
  font-size: 1.3rem;
  color: #92400e;
  transition: all 0.3s;
}

.folder-info {
  flex: 1;
  min-width: 0;
}

.folder-info h3 {
  font-size: 1.05rem;
  font-weight: 600;
  color: #1e293b;
  margin-bottom: 4px;
}

.folder-info p {
  font-size: 0.85rem;
  color: #64748b;
  margin-bottom: 6px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.file-count {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 0.78rem;
  color: #94a3b8;
  background: #f1f5f9;
  padding: 3px 10px;
  border-radius: 6px;
}

.expand-icon {
  flex-shrink: 0;
}

.expand-icon i {
  color: #94a3b8;
  font-size: 0.85rem;
  transition: transform 0.3s;
}

.expand-icon i.rotated {
  transform: rotate(180deg);
}

/* Folder Content */
.folder-content {
  border-top: 1px solid #f1f5f9;
  background: #fafbfc;
  padding: 16px 24px;
}

.no-files {
  text-align: center;
  padding: 30px;
  color: #94a3b8;
}

.no-files i {
  font-size: 1.5rem;
  margin-bottom: 8px;
  display: block;
}

.files-list {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.file-item {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 12px 16px;
  background: white;
  border-radius: 10px;
  border: 1px solid #e2e8f0;
  transition: all 0.2s;
}

.file-item:hover {
  border-color: #cbd5e1;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
}

.file-icon {
  width: 40px;
  height: 40px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  font-size: 1rem;
}

.icon-pdf { background: #fef2f2; color: #dc2626; }
.icon-word { background: #eff6ff; color: #2563eb; }
.icon-excel { background: #f0fdf4; color: #16a34a; }
.icon-ppt { background: #fff7ed; color: #ea580c; }
.icon-image { background: #faf5ff; color: #9333ea; }
.icon-archive { background: #fefce8; color: #ca8a04; }
.icon-default { background: #f1f5f9; color: #64748b; }

.file-details {
  flex: 1;
  min-width: 0;
}

.file-name {
  display: block;
  font-size: 0.9rem;
  font-weight: 500;
  color: #1e293b;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.file-meta {
  display: block;
  font-size: 0.78rem;
  color: #94a3b8;
  margin-top: 2px;
}

.btn-download {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 8px 16px;
  background: linear-gradient(135deg, #996600, #b37700);
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 0.8rem;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.2s;
  white-space: nowrap;
  flex-shrink: 0;
}

.btn-download:hover {
  background: linear-gradient(135deg, #7a5200, #996600);
  transform: translateY(-1px);
  box-shadow: 0 3px 10px rgba(153, 102, 0, 0.25);
  color: white;
}

@media (max-width: 640px) {
  .btn-download span { display: none; }
  .btn-download { padding: 8px 12px; }
  .file-item { padding: 10px 12px; gap: 10px; }
}

/* Slide transition */
.slide-enter-active, .slide-leave-active {
  transition: all 0.3s ease;
  max-height: 1000px;
  overflow: hidden;
}

.slide-enter-from, .slide-leave-to {
  max-height: 0;
  padding-top: 0;
  padding-bottom: 0;
  opacity: 0;
}

/* ===== FOOTER ===== */
.footer {
  background: #1e293b;
  color: #94a3b8;
  padding: 40px 24px;
}

.footer-container {
  max-width: 1200px;
  margin: 0 auto;
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 24px;
}

.footer-brand {
  display: flex;
  align-items: center;
  gap: 14px;
}

.footer-logo {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  background: linear-gradient(135deg, #996600, #cc9900);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
}

.footer-brand h4 {
  color: white;
  margin: 0;
  font-size: 1rem;
}

.footer-brand p {
  font-size: 0.75rem;
  margin: 0;
}

.footer-links {
  display: flex;
  gap: 20px;
}

.footer-links a {
  color: #94a3b8;
  text-decoration: none;
  font-size: 0.85rem;
  transition: color 0.2s;
}

.footer-links a:hover {
  color: #fbbf24;
}

.footer-copy p {
  font-size: 0.8rem;
  margin: 0;
}

@media (max-width: 768px) {
  .footer-container {
    flex-direction: column;
    text-align: center;
  }
}
</style>
