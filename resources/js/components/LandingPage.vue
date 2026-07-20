<template>
  <div id="app">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top">
      <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="#">
          <img
            src="/logo-ikipsiliwangi.png"
            alt="Logo E-SPMI"
            width="40"
            height="40"
            class="me-2"
          >
          <span>E-SPMI</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav ms-auto">
            <li class="nav-item">
              <a class="nav-link" href="#home">Beranda</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#features">Fitur</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#programs">Program</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#contact">Kontak</a>
            </li>
            <li class="nav-item">
              <router-link to="/login" class="btn btn-primary btn-sm ms-2">
                <i class="fas fa-sign-in-alt me-1"></i>Login
              </router-link>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <main style="padding-top: 0px;">
      <HeroSection :heroContent="heroContent" />
      
      <div class="container">
        <div class="row">
          <BeritaSection :beritas="beritas" />
          <JadwalSidebar :jadwals="jadwals" />
        </div>
      </div>

      <DokumenPublikSection />
      <FeaturesSection />
      <ProgramStudiSection />
      <StatsSection />
      <CTASection />
      <ContactSection />
    </main>

    <!-- Footer -->
    <footer class="py-4">
      <div class="container text-center">
        <p class="mb-0">&copy; 2024 Q-TRACK SPMI Digital. All rights reserved.</p>
      </div>
    </footer>
  </div>
</template>

<script>
import HeroSection from './HeroSection.vue'
import BeritaSection from './BeritaSection.vue'
import JadwalSidebar from './JadwalSidebar.vue'
import DokumenPublikSection from './DokumenPublikSection.vue'
import FeaturesSection from './FeaturesSection.vue'
import ProgramStudiSection from './ProgramStudiSection.vue'
import StatsSection from './StatsSection.vue'
import CTASection from './CTASection.vue'
import ContactSection from './ContactSection.vue'
import { useLandingStore } from '@/stores/landingStore'
import { mapState } from 'pinia'

export default {
  name: 'LandingPage',
  components: {
    HeroSection,
    BeritaSection,
    JadwalSidebar,
    DokumenPublikSection,
    FeaturesSection,
    ProgramStudiSection,
    StatsSection,
    CTASection,
    ContactSection
  },
  computed: {
    ...mapState(useLandingStore, ['heroContent', 'beritas', 'jadwals'])
  },
  mounted() {
    // Load Bootstrap JS
    if (typeof window !== 'undefined') {
      import('bootstrap/dist/js/bootstrap.bundle.min.js')
    }
  }
}
</script>

<style scoped>
/* Reuse all styles from blade file */
:root {
  --primary-brown: #996600;
  --secondary-brown: #b37400;
  --accent-brown: #cc9900;
  --dark-brown: #7a5200;
  --light-brown: #fff9e6;
}

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

.nav-link:hover {
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

footer {
  background: #7a5200;
  color: white;
}

/* Responsive */
@media (max-width: 768px) {
  .navbar-nav {
    padding: 1rem 0;
  }
  
  .nav-item {
    margin: 5px 0;
  }
  
  .btn-primary {
    width: 100%;
    text-align: center;
  }
}
</style>