<template>
  <div id="app">
    <!-- Navbar (same as LandingPage) -->
    <nav class="navbar navbar-expand-lg fixed-top">
      <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="/">
          <img
            :src="'/images/photos/logo-ikipsiliwangi.png'"
            alt="Logo E-SPMI"
            width="40"
            height="40"
            class="me-2"
            @error="handleImageError"
          >
          <span>E-SPMI</span>
        </a>
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
              <router-link class="nav-link" to="/">Beranda</router-link>
            </li>
            <li class="nav-item">
              <router-link class="nav-link" to="/berita">Berita</router-link>
            </li>
            <li class="nav-item">
              <router-link class="nav-link active" to="/dokumen-publik">Dokumen</router-link>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <main style="padding-top: 0px;">
      <!-- Hero Section (same brown gradient as LandingPage) -->
      <section class="hero-section">
        <div class="container">
          <router-link to="/" class="btn btn-outline-light btn-sm mb-3" style="opacity: 0.9;">
            <i class="fas fa-arrow-left me-1"></i>Kembali ke Beranda
          </router-link>
          <div class="row align-items-center">
            <div class="col-lg-8 col-md-12 position-relative text-center text-lg-start">
              <span class="hero-badge">
                <i class="fas fa-folder-open"></i> Dokumen Publik
              </span>
              <h1 class="display-4 fw-bold mb-3 text-white">Dokumen Publik SPMI</h1>
              <div class="lead mb-4 text-white opacity-90 fw-medium">
                Akses dokumen-dokumen resmi terkait Sistem Penjaminan Mutu Internal yang tersedia untuk publik.
              </div>
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
            <div class="col-lg-4 col-md-12 text-center position-relative d-none d-lg-block">
              <i class="fas fa-folder-open hero-icon-big"></i>
            </div>
          </div>
        </div>
      </section>

      <!-- Stats Bar -->
      <section class="stats-bar">
        <div class="container">
          <div class="row">
            <div class="col-auto">
              <div class="stat-item">
                <div class="stat-icon-box">
                  <i class="fas fa-folder"></i>
                </div>
                <div>
                  <span class="stat-number">{{ folders.length }}</span>
                  <span class="stat-label">Folder</span>
                </div>
              </div>
            </div>
            <div class="col-auto">
              <div class="stat-item">
                <div class="stat-icon-box">
                  <i class="fas fa-file-alt"></i>
                </div>
                <div>
                  <span class="stat-number">{{ totalFiles }}</span>
                  <span class="stat-label">Dokumen</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Content Section -->
      <section class="content-section">
        <div class="container">
          <!-- Toolbar (Back & View Toggle) -->
          <div class="d-flex justify-content-between align-items-center mb-4">
            <router-link to="/" class="btn btn-outline-secondary btn-sm" style="border-radius: 8px;">
              <i class="fas fa-arrow-left me-2"></i> Kembali ke Beranda
            </router-link>
            <div class="btn-group shadow-sm">
              <button 
                class="btn btn-sm" 
                :class="viewMode === 'card' ? 'btn-primary' : 'btn-light'" 
                @click="viewMode = 'card'"
              >
                <i class="fas fa-th-large me-1"></i> Grid
              </button>
              <button 
                class="btn btn-sm" 
                :class="viewMode === 'list' ? 'btn-primary' : 'btn-light'" 
                @click="viewMode = 'list'"
              >
                <i class="fas fa-list me-1"></i> List
              </button>
            </div>
          </div>

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
          <div v-else :class="viewMode === 'card' ? 'folders-grid-card' : 'folders-list'">
            <div 
              v-for="folder in filteredFolders" 
              :key="folder.id" 
              class="folder-card"
              :class="{ 'expanded': expandedFolder === folder.id, 'card-layout': viewMode === 'card' }"
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
    </main>

    <!-- Footer (same as LandingPage) -->
    <footer class="py-4">
      <div class="container text-center">
        <p class="mb-0">&copy; 2024 E-SPMI Digital. All rights reserved.</p>
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
      searchTimeout: null,
      viewMode: 'list'
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

    // Load Bootstrap JS (same as LandingPage)
    if (typeof window !== 'undefined') {
      import('bootstrap/dist/js/bootstrap.bundle.min.js').catch(() => {
        console.warn('Bootstrap JS not loaded')
      })
    }
  },
  methods: {
    handleImageError(event) {
      event.target.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="%23996600" stroke-width="2"%3E%3Cpath d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"%3E%3C/path%3E%3C/svg%3E'
    },
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
/* ===== CSS Variables (same as LandingPage) ===== */
:root {
  --primary-brown: #996600;
  --secondary-brown: #b37400;
  --accent-brown: #cc9900;
  --dark-brown: #7a5200;
  --light-brown: #fff9e6;
}

/* ===== NAVBAR (identical to LandingPage) ===== */
.navbar {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.navbar-brand {
  font-weight: 700;
  color: var(--primary-brown) !important;
  font-size: 1.5rem;
}

.nav-link {
  font-weight: 500;
  color: #374151 !important;
  margin: 0 10px;
}

.nav-link:hover,
.nav-link.active {
  color: var(--primary-brown) !important;
}

.btn-primary {
  background: linear-gradient(135deg, var(--secondary-brown), var(--primary-brown));
  border: none;
  padding: 10px 25px;
  font-weight: 600;
  border-radius: 8px;
}

.btn-primary:hover {
  background: linear-gradient(135deg, var(--primary-brown), var(--dark-brown));
  transform: translateY(-2px);
  box-shadow: 0 10px 20px rgba(153, 102, 0, 0.3);
}

/* ===== HERO SECTION (same brown gradient as LandingPage) ===== */
.hero-section {
  background: linear-gradient(135deg, var(--primary-brown) 0%, var(--dark-brown) 100%);
  color: white;
  padding: 140px 0 80px;
  position: relative;
  overflow: hidden;
}

.hero-section::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1000 1000'%3E%3Cpolygon fill='%23ffffff' fill-opacity='0.03' points='0,1000 1000,0 1000,1000'/%3E%3C/svg%3E");
  background-size: cover;
}

.hero-section .container {
  position: relative;
  z-index: 1;
}

.hero-badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: rgba(255, 255, 255, 0.15);
  color: white;
  padding: 8px 20px;
  border-radius: 24px;
  font-size: 0.85rem;
  font-weight: 600;
  margin-bottom: 20px;
  border: 1px solid rgba(255, 255, 255, 0.25);
  backdrop-filter: blur(5px);
}

.hero-icon-big {
  font-size: 8rem;
  color: rgba(255, 255, 255, 0.15);
}

/* Hero Search */
.hero-search {
  max-width: 500px;
  position: relative;
  margin-top: 1rem;
}

.hero-search i {
  position: absolute;
  left: 20px;
  top: 50%;
  transform: translateY(-50%);
  color: rgba(255, 255, 255, 0.6);
  font-size: 1rem;
}

.hero-search input {
  width: 100%;
  padding: 16px 20px 16px 50px;
  border: 2px solid rgba(255, 255, 255, 0.2);
  border-radius: 12px;
  background: rgba(255, 255, 255, 0.1);
  color: white;
  font-size: 0.95rem;
  transition: all 0.3s;
  backdrop-filter: blur(10px);
}

.hero-search input::placeholder {
  color: rgba(255, 255, 255, 0.6);
}

.hero-search input:focus {
  outline: none;
  border-color: rgba(255, 255, 255, 0.5);
  background: rgba(255, 255, 255, 0.15);
  box-shadow: 0 0 0 4px rgba(255, 255, 255, 0.1);
}

/* ===== STATS BAR ===== */
.stats-bar {
  background: white;
  border-bottom: 2px solid var(--light-brown);
  padding: 20px 0;
}

.stat-item {
  display: flex;
  align-items: center;
  gap: 12px;
}

.stat-icon-box {
  width: 42px;
  height: 42px;
  border-radius: 10px;
  background: var(--light-brown);
  color: var(--primary-brown);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1rem;
}

.stat-number {
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--dark-brown);
  display: block;
  line-height: 1.2;
}

.stat-label {
  font-size: 0.8rem;
  color: #6c757d;
  display: block;
}

/* ===== CONTENT SECTION ===== */
.content-section {
  background: #f8fafc;
  padding: 40px 0;
  min-height: 400px;
}

/* Loading */
.loading-state {
  text-align: center;
  padding: 80px 20px;
}

.spinner {
  width: 44px;
  height: 44px;
  border: 3px solid #e9ecef;
  border-top: 3px solid var(--primary-brown);
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
  margin: 0 auto 16px;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.loading-state p {
  color: #6c757d;
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
  background: var(--light-brown);
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 20px;
}

.empty-icon i {
  font-size: 2rem;
  color: var(--accent-brown);
}

.empty-state h3 {
  font-size: 1.3rem;
  color: var(--dark-brown);
  margin-bottom: 8px;
}

.empty-state p {
  color: #6c757d;
  font-size: 0.95rem;
  max-width: 400px;
  margin: 0 auto;
}

/* ===== FOLDER LIST / GRID ===== */
.folders-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.folders-grid-card {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 20px;
}

.folder-card {
  background: white;
  border-radius: 15px;
  border: 1px solid #e9ecef;
  overflow: hidden;
  transition: all 0.3s ease;
  box-shadow: 0 5px 15px rgba(0,0,0,0.08);
}

.folder-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 25px rgba(0,0,0,0.12);
  border-color: #dee2e6;
}

.folder-card.expanded {
  border-color: var(--accent-brown);
  box-shadow: 0 10px 25px rgba(153, 102, 0, 0.15);
}

.folder-header {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 20px 24px;
  cursor: pointer;
  transition: background 0.2s;
}

.card-layout .folder-header {
  flex-direction: column;
  text-align: center;
}

.folder-header:hover {
  background: var(--light-brown);
}

.folder-icon-wrapper {
  width: 50px;
  height: 50px;
  border-radius: 12px;
  background: linear-gradient(135deg, var(--light-brown), #fde68a);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.folder-icon-wrapper i {
  font-size: 1.3rem;
  color: var(--dark-brown);
  transition: all 0.3s;
}

.folder-info {
  flex: 1;
  min-width: 0;
}

.folder-info h3 {
  font-size: 1.05rem;
  font-weight: 600;
  color: #374151;
  margin-bottom: 4px;
}

.folder-info p {
  font-size: 0.85rem;
  color: #6c757d;
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
  color: var(--primary-brown);
  background: var(--light-brown);
  padding: 3px 10px;
  border-radius: 6px;
  font-weight: 500;
}

.expand-icon {
  flex-shrink: 0;
}

.expand-icon i {
  color: #adb5bd;
  font-size: 0.85rem;
  transition: transform 0.3s;
}

.expand-icon i.rotated {
  transform: rotate(180deg);
}

/* ===== FOLDER CONTENT ===== */
.folder-content {
  border-top: 1px solid #e9ecef;
  background: #fafbfc;
  padding: 16px 24px;
}

.no-files {
  text-align: center;
  padding: 30px;
  color: #6c757d;
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
  border: 1px solid #e9ecef;
  transition: all 0.2s;
}

.file-item:hover {
  border-color: var(--secondary-brown);
  box-shadow: 0 2px 8px rgba(153, 102, 0, 0.08);
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
.icon-archive { background: var(--light-brown); color: var(--primary-brown); }
.icon-default { background: #f1f5f9; color: #64748b; }

.file-details {
  flex: 1;
  min-width: 0;
}

.file-name {
  display: block;
  font-size: 0.9rem;
  font-weight: 500;
  color: #374151;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.file-meta {
  display: block;
  font-size: 0.78rem;
  color: #adb5bd;
  margin-top: 2px;
}

.btn-download {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 8px 16px;
  background: linear-gradient(135deg, var(--secondary-brown), var(--primary-brown));
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
  background: linear-gradient(135deg, var(--primary-brown), var(--dark-brown));
  transform: translateY(-1px);
  box-shadow: 0 10px 20px rgba(153, 102, 0, 0.3);
  color: white;
}

/* ===== SLIDE TRANSITION ===== */
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

/* ===== FOOTER (identical to LandingPage) ===== */
footer {
  background: #7a5200;
  color: white;
}

/* ===== RESPONSIVE (same breakpoints as LandingPage) ===== */
@media (max-width: 768px) {
  .hero-section {
    padding: 100px 0 60px;
    text-align: center;
  }

  .hero-search {
    max-width: 100%;
  }

  .hero-section .text-lg-start {
    text-align: center !important;
  }

  .stat-item {
    margin-bottom: 0.5rem;
  }
}

@media (max-width: 576px) {
  .hero-section {
    padding: 80px 0 40px;
  }

  .navbar-brand {
    font-size: 1.2rem;
  }

  .btn-download span {
    display: none;
  }

  .btn-download {
    padding: 8px 12px;
  }

  .file-item {
    padding: 10px 12px;
    gap: 10px;
  }
}
</style>
