<template>
  <div class="col-lg-4">
    <div class="jadwal-sidebar">
      <div class="jadwal-header">
        <h2 class="section-title mb-0">
          <i class="fas fa-calendar-alt"></i>Jadwal Kegiatan
        </h2>
        <p class="text-muted mb-0 mt-1">Jadwal mendatang</p>
      </div>
      
      <div v-if="jadwals && jadwals.length > 0">
        <div class="jadwal-list">
          <div v-for="jadwal in jadwals" :key="jadwal.id" class="jadwal-item">
            <div class="jadwal-date">
              <div class="jadwal-day">{{ jadwal.hari }}</div>
              <div class="jadwal-month">{{ jadwal.bulanSingkat }}</div>
            </div>
            <div class="jadwal-info">
              <div class="jadwal-title">{{ jadwal.kegiatan }}</div>
              <div class="jadwal-details">
                <template v-if="jadwal.waktu">
                  <i class="fas fa-clock"></i>
                  <span>{{ formatTime(jadwal.waktu) }}</span>
                  <span class="mx-1">•</span>
                </template>
                <template v-if="jadwal.tempat">
                  <i class="fas fa-map-marker-alt"></i>
                  <span>{{ jadwal.tempat }}</span>
                </template>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div v-else class="jadwal-empty">
        <i class="fas fa-calendar-times"></i>
        <h5 class="text-muted">Tidak ada jadwal</h5>
        <p class="text-muted">Jadwal akan ditampilkan di sini</p>
      </div>
      
      <div v-if="isAuthenticated" class="text-center mt-3">
        <router-link to="/dashboard" class="btn btn-outline-primary btn-sm">
          <i class="fas fa-cog me-1"></i>Kelola Jadwal
        </router-link>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'JadalSidebar',
  props: {
    jadwals: {
      type: Array,
      required: true,
      default: () => []
    },
    isAuthenticated: {
      type: Boolean,
      default: false
    }
  },
  methods: {
    formatTime(time) {
      if (!time) return ''
      const date = new Date(time)
      return date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })
    }
  }
}
</script>

<style scoped>
.jadwal-sidebar {
  background: white;
  border-radius: 15px;
  padding: 1.5rem;
  box-shadow: 0 5px 15px rgba(0,0,0,0.08);
  border: 1px solid #e9ecef;
  height: fit-content;
  position: sticky;
  top: 100px;
  max-height: 600px;
  overflow-y: auto;
}

.jadwal-sidebar::-webkit-scrollbar {
  width: 6px;
}

.jadwal-sidebar::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 3px;
}

.jadwal-sidebar::-webkit-scrollbar-thumb {
  background: var(--primary-brown);
  border-radius: 3px;
}

.jadwal-sidebar::-webkit-scrollbar-thumb:hover {
  background: var(--dark-brown);
}

.jadwal-header {
  border-bottom: 2px solid var(--primary-brown);
  padding-bottom: 1rem;
  margin-bottom: 1.5rem;
}

.jadwal-item {
  display: flex;
  align-items: center;
  padding: 0.75rem;
  border-radius: 8px;
  margin-bottom: 0.75rem;
  transition: all 0.2s ease;
  border: 1px solid #e9ecef;
}

.jadwal-item:hover {
  background: #f8f9fa;
  border-color: var(--primary-brown);
}

.jadwal-date {
  min-width: 50px;
  text-align: center;
  margin-right: 1rem;
  background: var(--light-brown);
  border-radius: 8px;
  padding: 0.5rem;
}

.jadwal-day {
  font-size: 1.25rem;
  font-weight: 700;
  color: var(--primary-brown);
  line-height: 1;
}

.jadwal-month {
  font-size: 0.75rem;
  color: var(--secondary-brown);
  text-transform: uppercase;
  font-weight: 600;
}

.jadwal-info {
  flex: 1;
}

.jadwal-title {
  font-weight: 600;
  color: #495057;
  margin-bottom: 0.25rem;
  font-size: 0.95rem;
}

.jadwal-details {
  font-size: 0.85rem;
  color: #6c757d;
}

.jadwal-details i {
  margin-right: 0.25rem;
}

.jadwal-empty {
  text-align: center;
  padding: 2rem 1rem;
  color: #6c757d;
}

.jadwal-empty i {
  font-size: 3rem;
  margin-bottom: 1rem;
  color: #dee2e6;
}

.section-title {
  color: var(--dark-brown);
  font-weight: 700;
  display: flex;
  align-items: center;
}

.section-title i {
  margin-right: 0.75rem;
  color: var(--primary-brown);
}

@media (max-width: 768px) {
  .jadwal-sidebar {
    position: static;
    margin-top: 2rem;
    max-height: none;
  }
}

@media (max-width: 576px) {
  .jadwal-date {
    min-width: 45px;
    margin-right: 0.75rem;
  }
  
  .jadwal-day {
    font-size: 1.1rem;
  }
}
</style>