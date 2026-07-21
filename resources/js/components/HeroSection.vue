<!-- src/components/HeroSection.vue -->
<template>
  <section class="hero-section" id="home">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-6">
          <h1 class="hero-title">{{ heroContent?.title || 'Selamat Datang di SPMI' }}</h1>
          <p class="hero-subtitle">{{ heroContent?.subtitle || 'Sistem Penjaminan Mutu Internal IKIP Siliwangi' }}</p>
          <div class="hero-buttons">
            <router-link to="/dokumen-publik" class="btn btn-hero-primary">
              <i class="fas fa-file-alt me-2"></i>Lihat Dokumen
            </router-link>
            <router-link to="/pengelola/login" class="btn btn-hero-secondary">
              <i class="fas fa-sign-in-alt me-2"></i>Login
            </router-link>
          </div>
        </div>
        <div class="col-lg-6 text-center">
          <img 
            :src="'/images/hero-illustration.png'"
            alt="SPMI Illustration" 
            class="hero-image"
            v-if="heroContent?.image"
          >
          <div class="hero-stats" v-else>
            <div class="stat-item">
              <h3>100+</h3>
              <p>Dokumen</p>
            </div>
            <div class="stat-item">
              <h3>50+</h3>
              <p>Berita</p>
            </div>
            <div class="stat-item">
              <h3>30+</h3>
              <p>Jadwal</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: 'HeroSection',
  props: {
    heroContent: {
      type: Object,
      default: () => ({})
    }
  }
}
</script>

<style scoped>
/* ========================================
   HERO SECTION
   ======================================== */
.hero-section {
  background: linear-gradient(135deg, var(--primary-brown) 0%, var(--dark-brown) 100%);
  color: white;
  padding: 80px 0 60px;
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
  pointer-events: none;
}

.hero-title {
  font-size: 2.8rem;
  font-weight: 700;
  margin-bottom: 1rem;
  line-height: 1.2;
}

.hero-subtitle {
  font-size: 1.2rem;
  opacity: 0.9;
  margin-bottom: 2rem;
  line-height: 1.6;
}

.hero-buttons {
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
}

.btn-hero-primary {
  background: white;
  color: var(--primary-brown) !important;
  padding: 12px 30px;
  border-radius: var(--radius-sm);
  font-weight: 600;
  text-decoration: none;
  transition: all 0.3s ease;
  display: inline-flex;
  align-items: center;
}

.btn-hero-primary:hover {
  transform: translateY(-3px);
  box-shadow: 0 10px 30px rgba(0,0,0,0.2);
  color: var(--primary-brown) !important;
}

.btn-hero-secondary {
  background: rgba(255,255,255,0.15);
  border: 2px solid rgba(255,255,255,0.3);
  color: white !important;
  padding: 12px 30px;
  border-radius: var(--radius-sm);
  font-weight: 600;
  text-decoration: none;
  transition: all 0.3s ease;
  display: inline-flex;
  align-items: center;
}

.btn-hero-secondary:hover {
  background: rgba(255,255,255,0.25);
  transform: translateY(-3px);
  color: white !important;
}

.hero-image {
  max-width: 100%;
  height: auto;
  animation: float 3s ease-in-out infinite;
}

.hero-stats {
  display: flex;
  justify-content: center;
  gap: 3rem;
  margin-top: 2rem;
}

.stat-item {
  text-align: center;
}

.stat-item h3 {
  font-size: 2.5rem;
  font-weight: 700;
  margin-bottom: 0.25rem;
}

.stat-item p {
  opacity: 0.8;
  margin-bottom: 0;
  font-size: 0.95rem;
}

@keyframes float {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-10px); }
}

/* ========================================
   RESPONSIVE
   ======================================== */
@media (max-width: 991.98px) {
  .hero-section {
    padding: 60px 0 40px;
  }
  
  .hero-title {
    font-size: 2.2rem;
  }
  
  .hero-subtitle {
    font-size: 1.1rem;
  }
  
  .hero-stats {
    gap: 2rem;
  }
  
  .stat-item h3 {
    font-size: 2rem;
  }
}

@media (max-width: 767.98px) {
  .hero-section {
    padding: 50px 0 30px;
    text-align: center;
  }
  
  .hero-title {
    font-size: 1.8rem;
  }
  
  .hero-subtitle {
    font-size: 1rem;
  }
  
  .hero-buttons {
    justify-content: center;
  }
  
  .hero-stats {
    gap: 1.5rem;
  }
  
  .stat-item h3 {
    font-size: 1.5rem;
  }
}

@media (max-width: 575.98px) {
  .hero-section {
    padding: 40px 0 20px;
  }
  
  .hero-title {
    font-size: 1.5rem;
  }
  
  .hero-buttons {
    flex-direction: column;
    align-items: center;
  }
  
  .btn-hero-primary,
  .btn-hero-secondary {
    width: 100%;
    justify-content: center;
    padding: 10px 20px;
  }
  
  .hero-stats {
    flex-wrap: wrap;
    gap: 1rem;
  }
  
  .stat-item {
    flex: 1;
    min-width: 80px;
  }
  
  .stat-item h3 {
    font-size: 1.25rem;
  }
}
</style>