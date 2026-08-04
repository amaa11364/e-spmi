<template>
  <div class="col-lg-8 mt-4">
    <div class="berita-section">
      <div class="section-header">
        <h2 class="section-title">
          <i class="fas fa-newspaper"></i>Berita Terbaru
        </h2>
        <p class="text-muted mb-0">Informasi dan kegiatan terbaru dari SPMI</p>
      </div>
      
      <div v-if="beritas && beritas.length > 0">
        <div class="row g-4">
          <div v-for="berita in beritas" :key="berita.id" class="col-md-6">
            <a :href="berita.link" target="_blank" class="text-decoration-none">
              <div class="berita-card">
                <img 
                  :src="berita.gambar_url || defaultImage" 
                  :alt="berita.judul" 
                  class="berita-image"
                  @error="handleImageError"
                >
                <div class="berita-content">
                  <h3 class="berita-title">{{ truncateText(berita.judul, 60) }}</h3>
                  <div class="berita-meta">
                    <i class="fas fa-calendar-alt"></i>
                    <span>{{ formatDate(berita.created_at) }}</span>
                  </div>
                </div>
              </div>
            </a>
          </div>
        </div>
        
        <div class="view-more">
          <router-link to="/berita" class="btn btn-primary btn-lg">
            <i class="fas fa-list me-2"></i>Lihat Semua Berita
          </router-link>
        </div>
      </div>
      
      <div v-else class="text-center py-5">
        <i class="fas fa-newspaper fa-4x text-muted mb-3"></i>
        <h5 class="text-muted">Belum ada berita</h5>
        <p class="text-muted">Berita akan ditampilkan di sini</p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'BeritaSection',
  props: {
    beritas: {
      type: Array,
      required: true,
      default: () => []
    }
  },
  data() {
    return {
      // SVG Data URI sebagai fallback default placeholder
      defaultImage: 'data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22400%22%20height%3D%22200%22%20viewBox%3D%220%200%20400%20200%22%3E%3Crect%20fill%3D%22%23f3f4f6%22%20width%3D%22400%22%20height%3D%22200%22%2F%3E%3Ctext%20fill%3D%22%239ca3af%22%20font-family%3D%22sans-serif%22%20font-size%3D%2218%22%20font-weight%3D%22bold%22%20x%3D%2250%25%22%20y%3D%2250%25%22%20text-anchor%3D%22middle%22%20dy%3D%22.3em%22%3ENo%20Image%20Available%3C%2Ftext%3E%3C%2Fsvg%3E'
    }
  },
  methods: {
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
    }
  }
}
</script>

<style scoped>
.berita-section {
  margin-bottom: 40px;
}

.berita-card {
  background: white;
  border-radius: 15px;
  overflow: hidden;
  box-shadow: 0 5px 15px rgba(0,0,0,0.08);
  transition: all 0.3s ease;
  height: 100%;
  border: 1px solid #e9ecef;
}

.berita-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}

.berita-image {
  width: 100%;
  height: 200px;
  object-fit: cover;
  background-color: #f1f5f9;
}

.berita-content {
  padding: 1.5rem;
}

.berita-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: #495057;
  margin-bottom: 0.75rem;
  line-height: 1.4;
}

.berita-title:hover {
  color: var(--primary-brown);
}

.berita-meta {
  font-size: 0.875rem;
  color: #adb5bd;
  display: flex;
  align-items: center;
}

.berita-meta i {
  margin-right: 0.5rem;
}

.section-header {
  margin-bottom: 2rem;
  padding-bottom: 1rem;
  border-bottom: 2px solid var(--primary-brown);
}

.section-title {
  color: var(--dark-brown);
  font-weight: 700;
  margin-bottom: 0.5rem;
  display: flex;
  align-items: center;
}

.section-title i {
  margin-right: 0.75rem;
  color: var(--primary-brown);
}

.view-more {
  text-align: center;
  margin-top: 2rem;
  padding-top: 1.5rem;
  border-top: 1px solid #e9ecef;
}

@media (max-width: 768px) {
  .berita-image {
    height: 180px;
  }
  
  .section-header {
    text-align: center;
  }
  
  .section-title {
    justify-content: center;
  }
}

@media (max-width: 576px) {
  .berita-card {
    margin-bottom: 1.5rem;
  }
}
</style>