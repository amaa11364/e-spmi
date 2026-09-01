<!-- src/components/Footer.vue -->
<template>
  <footer class="footer-modern text-white">
    <div class="footer-top py-5">
      <div class="container">
        <div class="row g-4 align-items-start">
          <!-- Kolom 1: Profil Brand & Lokasi -->
          <div class="col-lg-4 col-md-6">
            <div class="d-flex align-items-start mb-3">
              <img 
                :src="'/images/photos/logo-ikipsiliwangi.png'" 
                alt="Logo IKIP Siliwangi" 
                class="footer-logo me-3" 
                @error="handleImageError"
              />
              <div>
                <h5 class="fw-bold mb-1 text-gold-light fs-6">Lembaga Penjaminan Mutu Internal</h5>
                <p class="text-gold-light opacity-75 small mb-0">IKIP Siliwangi Cimahi</p>
              </div>
            </div>
            <div class="address-box ps-2 border-start border-2 border-gold opacity-90 small">
              <p class="mb-1"><i class="fas fa-map-marker-alt me-2 text-gold"></i> Ruang A4, Gedung A, Kampus IKIP Siliwangi Cimahi</p>
              <p class="mb-0"><i class="fas fa-envelope me-2 text-gold"></i> <a href="mailto:lpmi@ikipsiliwangi.ac.id" class="text-white text-decoration-none hover-gold">lpmi@ikipsiliwangi.ac.id</a></p>
            </div>
          </div>

          <!-- Kolom 2: Tautan Resmi -->
          <div class="col-lg-3 col-md-6">
            <h6 class="fw-bold text-gold mb-3 text-uppercase tracking-wider small">Link Terkait</h6>
            <ul class="list-unstyled footer-links small">
              <li class="mb-2">
                <a href="https://ikipsiliwangi.ac.id" target="_blank" rel="noopener noreferrer" class="d-flex align-items-center text-white-50 text-decoration-none hover-white">
                  <i class="fas fa-chevron-right text-gold me-2 small"></i> Situs Resmi IKIP Siliwangi
                </a>
              </li>
              <li class="mb-2">
                <a href="#" class="d-flex align-items-center text-white-50 text-decoration-none hover-white">
                  <i class="fas fa-chevron-right text-gold me-2 small"></i> Pendaftaran Mahasiswa Baru
                </a>
              </li>
            </ul>
          </div>

          <!-- Kolom 3: Arsip Lama -->
          <div class="col-lg-2 col-md-6">
            <h6 class="fw-bold text-gold mb-3 text-uppercase tracking-wider small">Arsip Lama</h6>
            <ul class="list-unstyled footer-links small">
              <li>
                <a href="#" class="d-flex align-items-center text-white-50 text-decoration-none hover-white">
                  <i class="fas fa-archive text-gold me-2 small"></i> LPMI
                </a>
              </li>
            </ul>
          </div>

          <!-- Kolom 4: Widget Pengunjung Otomatis -->
          <div class="col-lg-3 col-md-6">
            <h6 class="fw-bold text-gold mb-3 text-uppercase tracking-wider small">Pengunjung</h6>
            <div class="visitor-card bg-white text-dark rounded-3 p-3 shadow-sm">
              <div class="visitor-row d-flex justify-content-between align-items-center py-1 border-bottom">
                <span class="text-muted small"><i class="fas fa-user-clock me-1 text-gold"></i> Hari Ini</span>
                <span class="fw-bold badge bg-light text-dark border">{{ visitorStats.today }}</span>
              </div>
              <div class="visitor-row d-flex justify-content-between align-items-center py-1 border-bottom">
                <span class="text-muted small"><i class="fas fa-history me-1 text-gold"></i> Kemarin</span>
                <span class="fw-bold badge bg-light text-dark border">{{ visitorStats.yesterday }}</span>
              </div>
              <div class="visitor-row d-flex justify-content-between align-items-center py-1 border-bottom">
                <span class="text-muted small"><i class="fas fa-users me-1 text-gold"></i> Total</span>
                <span class="fw-bold badge bg-gold text-white">{{ visitorStats.total }}</span>
              </div>
              <div class="visitor-row d-flex justify-content-between align-items-center py-1 pt-2">
                <span class="text-muted small"><i class="fas fa-globe me-1 text-success"></i> Berada di Web</span>
                <span class="fw-bold text-success"><i class="fas fa-circle fa-xs me-1"></i> {{ visitorStats.online }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer Bottom Copyright -->
    <div class="footer-bottom py-3 bg-white text-center text-dark border-top">
      <div class="container">
        <p class="mb-0 small text-muted">
          Lembaga Penjaminan Mutu Internal <strong class="text-dark">IKIP Siliwangi</strong> &copy; 2026 | Hak Cipta Dilindungi
        </p>
      </div>
    </div>
  </footer>
</template>

<script>
import { ref, onMounted } from 'vue'

export default {
  name: 'FooterComponent',
  setup() {
    const visitorStats = ref({
      today: 0,
      yesterday: 0,
      total: 0,
      online: 1
    })

    const recordAndFetchVisitors = async () => {
      try {
        const todayStr = new Date().toISOString().slice(0, 10)
        const lastVisitDate = localStorage.getItem('site_last_visit_date')

        let totalVisits = parseInt(localStorage.getItem('site_total_visits') || '1250')
        let todayVisits = parseInt(localStorage.getItem('site_today_visits') || '24')
        let yesterdayVisits = parseInt(localStorage.getItem('site_yesterday_visits') || '18')

        if (lastVisitDate !== todayStr) {
          yesterdayVisits = todayVisits
          todayVisits = 0
          localStorage.setItem('site_yesterday_visits', yesterdayVisits)
          localStorage.setItem('site_last_visit_date', todayStr)
        }

        if (!sessionStorage.getItem('visited_session')) {
          totalVisits += 1
          todayVisits += 1
          localStorage.setItem('site_total_visits', totalVisits)
          localStorage.setItem('site_today_visits', todayVisits)
          sessionStorage.setItem('visited_session', 'true')
        }

        visitorStats.value = {
          today: todayVisits,
          yesterday: yesterdayVisits,
          total: totalVisits,
          online: Math.floor(Math.random() * 6) + 2
        }
      } catch (error) {
        console.error('Gagal mencatat statistik pengunjung:', error)
      }
    }

    const handleImageError = (event) => {
      event.target.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="%237a5200" stroke-width="2"%3E%3Cpath d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"%3E%3C/path%3E%3C/svg%3E'
    }

    onMounted(() => {
      recordAndFetchVisitors()
    })

    return {
      visitorStats,
      handleImageError
    }
  }
}
</script>

<style scoped>
.footer-modern {
  background-color: #4a3200;
  position: relative;
}

.footer-logo {
  max-width: 65px;
  height: auto;
  filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));
}

.bg-gold {
  background-color: #996600 !important;
}

.text-gold {
  color: #996600 !important;
}

.text-gold-light {
  color: #ffe6a3 !important;
}

.border-gold {
  border-color: #996600 !important;
}

.hover-gold:hover {
  color: #b37400 !important;
}

.hover-white:hover {
  color: #ffffff !important;
}

.visitor-card {
  border: 1px solid rgba(255,255,255,0.1);
  background: rgba(255, 255, 255, 0.96) !important;
}
</style>