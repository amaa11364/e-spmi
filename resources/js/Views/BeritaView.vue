<template>
  <div id="berita-page">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top shadow-sm">
      <div class="container">
        <router-link class="navbar-brand d-flex align-items-center" to="/">
          <img
            :src="'/images/photos/logo-ikipsiliwangi.png'"
            alt="Logo E-SPMI"
            width="40"
            height="40"
            class="me-2"
            @error="handleLogoError"
          >
          <span>E-SPMI</span>
        </router-link>

        <button 
          class="navbar-toggler border-0 shadow-none p-1" 
          type="button" 
          data-bs-toggle="collapse" 
          data-bs-target="#navbarNav"
        >
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav ms-auto align-items-lg-center">
            <li class="nav-item">
              <router-link class="nav-link" to="/">Beranda</router-link>
            </li>
            <li class="nav-item">
              <router-link class="nav-link active" to="/berita">Berita</router-link>
            </li>
            <li class="nav-item">
              <router-link class="nav-link" to="/dokumen-publik">Dokumen Publik</router-link>
            </li>
            <li class="nav-item ms-lg-3 mt-2 mt-lg-0">
              <router-link to="/" class="btn btn-outline-brown text-nowrap">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Beranda
              </router-link>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <main class="content-wrapper">
      <div class="container px-3 px-sm-4">
        
        <div class="mb-3">
          <router-link to="/" class="btn btn-sm btn-light border text-secondary shadow-sm">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Beranda
          </router-link>
        </div>

        <div class="section-header mb-4">
          <h2 class="section-title">
            <i class="fas fa-newspaper me-2"></i>Berita & Informasi SPMI
          </h2>
          <p class="text-muted mb-0">
            Dapatkan berita terbaru, pengumuman, dan agenda kegiatan penjaminan mutu
          </p>
        </div>

        <!-- Search Bar -->
        <div class="search-container card border-0 shadow-sm mb-4">
          <div class="card-body p-2 p-md-3">
            <form @submit.prevent="executeSearch">
              <div class="row g-2 align-items-center">
                <div class="col-12 col-md">
                  <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted ps-3">
                      <i class="fas fa-search"></i>
                    </span>
                    <input 
                      type="text" 
                      class="form-control border-start-0 ps-0 shadow-none" 
                      placeholder="Cari kata kunci judul atau isi berita..." 
                      v-model="searchQuery"
                      @input="handleLiveSearch"
                    >
                    <button 
                      v-if="searchQuery" 
                      type="button" 
                      class="btn btn-link text-muted text-decoration-none border-0 pe-3" 
                      @click="clearSearch"
                    >
                      <i class="fas fa-times"></i>
                    </button>
                  </div>
                </div>

                <div class="col-12 col-md-auto d-flex gap-2">
                  <button type="submit" class="btn btn-brown w-100 px-4">
                    <i class="fas fa-search me-1"></i> Cari
                  </button>
                  <button 
                    v-if="searchQuery" 
                    type="button" 
                    class="btn btn-light border text-nowrap" 
                    @click="clearSearch"
                  >
                    Reset
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>

        <!-- Tag Filter Aktif -->
        <div v-if="activeSearchKeyword" class="mb-3 d-flex align-items-center gap-2">
          <span class="small text-muted">Hasil pencarian untuk:</span>
          <span class="badge bg-brown text-white py-2 px-3 fw-normal">
            "{{ activeSearchKeyword }}"
            <i class="fas fa-times ms-2 cursor-pointer" @click="clearSearch"></i>
          </span>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="text-center py-5">
          <div class="spinner-border text-brown" role="status"></div>
          <p class="mt-2 text-muted small">Memuat data berita...</p>
        </div>
        
        <!-- Berita Grid -->
        <div v-else-if="filteredBeritas.length > 0" class="row g-3 g-md-4">
          <div 
            v-for="berita in filteredBeritas" 
            :key="berita.id" 
            class="col-12 col-sm-6 col-lg-4"
          >
            <!-- PERBAIKAN: Menggunakan method openBeritaDetail -->
            <div class="berita-card h-100 cursor-pointer" @click="openBeritaDetail(berita)">
              <div class="berita-image-wrapper">
                <img 
                  :src="berita.gambar_url || defaultImage" 
                  :alt="berita.judul" 
                  class="berita-image"
                  @error="handleImageError"
                >
                <span class="berita-category-tag">
                  {{ berita.kategori || 'Berita' }}
                </span>
              </div>

              <div class="berita-content d-flex flex-column justify-content-between">
                <div>
                  <h3 class="berita-title mb-2">
                    {{ truncateText(berita.judul, 65) }}
                  </h3>
                  <p class="berita-excerpt text-muted mb-3">
                    {{ truncateText(berita.deskripsi || 'Klik untuk membaca detail berita selengkapnya...', 90) }}
                  </p>
                </div>
                
                <div class="berita-meta pt-2 border-top d-flex align-items-center justify-content-between">
                  <span class="text-muted small">
                    <i class="fas fa-calendar-alt me-1"></i> {{ formatDate(berita.created_at) }}
                  </span>
                  <span class="read-more-text text-brown fw-semibold small">
                    Baca Selengkapnya <i class="fas fa-chevron-right ms-1"></i>
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-5 card border-0 shadow-sm my-4">
          <div class="card-body py-5">
            <i class="fas fa-newspaper fa-3x text-muted mb-3 opacity-50"></i>
            <h5 class="fw-bold text-dark mb-1">Berita tidak ditemukan</h5>
            <p class="text-muted small mb-3">
              Tidak ada berita yang cocok dengan kata kunci "{{ searchQuery || activeSearchKeyword }}"
            </p>
            <button class="btn btn-outline-brown btn-sm" @click="clearSearch">
              <i class="fas fa-sync-alt me-1"></i> Tampilkan Semua Berita
            </button>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="pagination.last_page > 1 && !searchQuery" class="d-flex justify-content-center mt-4">
          <nav aria-label="Navigasi Halaman">
            <ul class="pagination pagination-sm flex-wrap justify-content-center mb-0">
              <li class="page-item" :class="{ disabled: pagination.current_page === 1 }">
                <button class="page-link" @click="changePage(pagination.current_page - 1)">&laquo;</button>
              </li>
              <li 
                v-for="page in pagination.last_page" 
                :key="page" 
                class="page-item"
                :class="{ active: page === pagination.current_page }"
              >
                <button class="page-link" @click="changePage(page)">{{ page }}</button>
              </li>
              <li class="page-item" :class="{ disabled: pagination.current_page === pagination.last_page }">
                <button class="page-link" @click="changePage(pagination.current_page + 1)">&raquo;</button>
              </li>
            </ul>
          </nav>
        </div>

      </div>
    </main>

    <!-- Modal Detail Berita (Jika tidak ada link eksternal) -->
    <div class="modal fade" id="detailBeritaModal" tabindex="-1" aria-hidden="true" ref="detailModal">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
          <div class="modal-header border-0 pb-0">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body p-4" v-if="selectedBerita">
            <img 
              :src="selectedBerita.gambar_url || defaultImage" 
              class="img-fluid rounded-3 mb-3 w-100" 
              style="max-height: 350px; object-fit: cover;"
            >
            <span class="badge bg-brown text-white mb-2">{{ selectedBerita.kategori || 'Berita' }}</span>
            <h3 class="fw-bold text-dark mb-2">{{ selectedBerita.judul }}</h3>
            <p class="text-muted small mb-4">
              <i class="fas fa-calendar-alt me-1"></i> Dipublikasikan pada {{ formatDate(selectedBerita.created_at) }}
            </p>
            <div class="berita-full-description text-secondary" style="white-space: pre-line; line-height: 1.7;">
              {{ selectedBerita.deskripsi }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <footer class="py-4 mt-5">
      <div class="container text-center">
        <p class="mb-0 small">&copy; 2026 E-SPMI Digital. All rights reserved.</p>
      </div>
    </footer>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'BeritaView',
  data() {
    return {
      beritas: [],
      selectedBerita: null,
      searchQuery: '',
      activeSearchKeyword: '',
      loading: false,
      searchDebounce: null,
      defaultImage: 'data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22400%22%20height%3D%22200%22%20viewBox%3D%220%200%20400%20200%22%3E%3Crect%20fill%3D%22%23f3f4f6%22%20width%3D%22400%22%20height%3D%22200%22%2F%3E%3Ctext%20fill%3D%22%239ca3af%22%20font-family%3D%22sans-serif%22%20font-size%3D%2218%22%20font-weight%3D%22bold%22%20x%3D%2250%25%22%20y%3D%2250%25%22%20text-anchor%3D%22middle%22%20dy%3D%22.3em%22%3ENo%20Image%20Available%3C%2Ftext%3E%3C%2Fsvg%3E',
      pagination: {
        current_page: 1,
        last_page: 1,
        total: 0
      }
    }
  },
  computed: {
    filteredBeritas() {
      if (!this.searchQuery) return this.beritas
      const q = this.searchQuery.toLowerCase()
      return this.beritas.filter(b => 
        (b.judul && b.judul.toLowerCase().includes(q)) ||
        (b.deskripsi && b.deskripsi.toLowerCase().includes(q)) ||
        (b.kategori && b.kategori.toLowerCase().includes(q))
      )
    }
  },
  mounted() {
    this.fetchBeritas()
  },
  methods: {
    async fetchBeritas(page = 1) {
      this.loading = true
      try {
        // PERBAIKAN: Gunakan 'beritas' (tanpa awalan /api/)
        const response = await axios.get(`beritas?page=${page}&search=${this.activeSearchKeyword}`)
        if (response.data.success) {
          // Hanya ambil berita yang di-publish untuk halaman publik
          const allData = response.data.data.data
          this.beritas = allData.filter(b => b.is_published == 1 || b.is_published == true)
          this.pagination.current_page = response.data.data.current_page
          this.pagination.last_page = response.data.data.last_page
          this.pagination.total = response.data.data.total
        }
      } catch (error) {
        console.error('Gagal mengambil berita:', error)
      } finally {
        this.loading = false
      }
    },
    openBeritaDetail(berita) {
      if (berita.link && berita.link.trim() !== '') {
        window.open(berita.link, '_blank')
      } else {
        this.selectedBerita = berita
        const modalElement = this.$refs.detailModal
        if (window.bootstrap && modalElement) {
          const modal = new window.bootstrap.Modal(modalElement)
          modal.show()
        } else {
          alert(`=== ${berita.judul} ===\n\n${berita.deskripsi}`)
        }
      }
    },
    handleLiveSearch() {
      clearTimeout(this.searchDebounce)
      this.searchDebounce = setTimeout(() => {
        if (this.searchQuery.length > 2 || this.searchQuery.length === 0) {
          this.executeSearch()
        }
      }, 400)
    },
    executeSearch() {
      this.activeSearchKeyword = this.searchQuery
      this.fetchBeritas(1)
    },
    clearSearch() {
      this.searchQuery = ''
      this.activeSearchKeyword = ''
      this.fetchBeritas(1)
    },
    changePage(page) {
      if (page >= 1 && page <= this.pagination.last_page) {
        this.fetchBeritas(page)
        window.scrollTo({ top: 0, behavior: 'smooth' })
      }
    },
    truncateText(text, length) {
      if (!text) return ''
      return text.length > length ? text.substring(0, length) + '...' : text
    },
    formatDate(date) {
      if (!date) return ''
      const d = new Date(date)
      return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
    },
    handleImageError(e) {
      e.target.src = this.defaultImage
    },
    handleLogoError(e) {
      e.target.src = this.defaultImage
    }
  }
}
</script>

<style scoped>
/* CSS bawaan kamu tetap sama */
.bg-brown { background-color: #996600 !important; }
.text-brown { color: #996600 !important; }
.navbar {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}
.navbar-brand {
  font-weight: 700;
  color: #996600 !important;
  font-size: 1.5rem;
}
.nav-link {
  font-weight: 500;
  color: #374151 !important;
  margin: 0 10px;
}
.nav-link.active, .nav-link:hover {
  color: #996600 !important;
}
.btn-outline-brown {
  border: 1px solid #996600;
  color: #996600;
  font-weight: 600;
  padding: 6px 16px;
  border-radius: 8px;
  transition: all 0.2s ease;
}
.btn-outline-brown:hover {
  background-color: #996600;
  color: white;
  transform: translateY(-1px);
}
.btn-brown {
  background: linear-gradient(135deg, #b37400, #996600);
  color: white;
  border: none;
  font-weight: 600;
  border-radius: 8px;
}
.btn-brown:hover {
  background: linear-gradient(135deg, #996600, #7a5200);
  color: white;
}
.content-wrapper {
  padding-top: 110px;
  min-height: calc(100vh - 100px);
  background-color: #f8fafc;
}
.section-header {
  border-bottom: 2px solid #996600;
  padding-bottom: 1rem;
}
.section-title {
  color: #7a5200;
  font-weight: 700;
  display: flex;
  align-items: center;
}
.search-container {
  border-radius: 12px;
}
.berita-card {
  background: white;
  border-radius: 15px;
  overflow: hidden;
  box-shadow: 0 5px 15px rgba(0,0,0,0.08);
  transition: all 0.3s ease;
  border: 1px solid #e9ecef;
}
.berita-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}
.berita-image-wrapper {
  position: relative;
  width: 100%;
  height: 180px;
  overflow: hidden;
  background-color: #f1f5f9;
}
.berita-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.berita-category-tag {
  position: absolute;
  top: 10px;
  left: 10px;
  background: rgba(122, 82, 0, 0.85);
  backdrop-filter: blur(4px);
  color: white;
  font-size: 0.7rem;
  font-weight: 600;
  padding: 4px 10px;
  border-radius: 20px;
  text-transform: uppercase;
}
.berita-content {
  padding: 1.25rem;
  height: calc(100% - 180px);
}
.berita-title {
  font-size: 1.1rem;
  font-weight: 600;
  color: #495057;
  line-height: 1.4;
}
.berita-excerpt {
  font-size: 0.875rem;
  line-height: 1.5;
}
.page-link {
  color: #996600;
}
.page-item.active .page-link {
  background-color: #996600;
  border-color: #996600;
  color: white;
}
footer {
  background: #7a5200;
  color: white;
}
.cursor-pointer {
  cursor: pointer;
}
@media (max-width: 991.98px) {
  .nav-link { margin: 5px 0; }
}
@media (max-width: 575.98px) {
  .content-wrapper { padding-top: 90px; }
  .navbar-brand { font-size: 1.25rem; }
  .section-title { font-size: 1.35rem; }
  .berita-image-wrapper { height: 160px; }
}
</style>