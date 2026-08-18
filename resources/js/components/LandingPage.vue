<!-- src/views/LandingPage.vue -->
<template> 
  <div id="app">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top">
      <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="#">
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
              <a class="nav-link" href="#home">Beranda</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#about">Tentang LPMI</a>
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
          </ul>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <main style="padding-top: 0px;">
      <!-- Hero Section -->
      <section id="home" class="hero-section" :style="heroStyle">
        <div class="container">
          <div class="row align-items-center">
            <div class="col-lg-6 col-md-12 position-relative text-center text-lg-start">
              <h1 class="display-4 fw-bold mb-3 text-white">{{ heroContent.title }}</h1>
              <h3 class="h4 mb-4 text-white opacity-90">{{ heroContent.subtitle }}</h3>
              <div class="lead mb-4 text-white opacity-90 fw-medium" v-html="heroContent.description"></div>
              <a :href="heroContent.ctaLink" class="btn btn-light btn-lg">
                {{ heroContent.ctaText }} <i class="fas fa-arrow-right ms-2"></i>
              </a>
            </div>
            <div class="col-lg-6 col-md-12 text-center position-relative">
              <!-- Area untuk gambar tambahan -->
            </div>
          </div>
        </div>
      </section>

      <!-- Profil & Tentang LPMI Section -->
      <section id="about" class="about-section py-5 bg-light position-relative animate-on-scroll">
        <div class="container py-4">
          <!-- Header Section -->
          <div class="text-center mb-5">
            <span class="badge bg-gold-subtle text-gold px-3 py-2 rounded-pill fw-semibold mb-2">
              <i class="fas fa-university me-1"></i> Profil Institusi
            </span>
            <h2 class="section-title display-5 fw-bold text-dark">
              Lembaga Penjaminan Mutu Internal
            </h2>
            <p class="lead text-primary-brown fw-semibold">
              Institut Keguruan dan Ilmu Pendidikan Siliwangi
            </p>
            <div class="divider-line mx-auto mt-3"></div>
          </div>

          <!-- Deskripsi Utama dalam Card Modern -->
          <div class="row justify-content-center mb-5">
            <div class="col-lg-10">
              <div class="about-card p-4 p-md-5 bg-white rounded-4 shadow-sm border-0 position-relative">
                <div class="quote-icon position-absolute top-0 end-0 p-4 opacity-10">
                  <i class="fas fa-quote-right fa-5x text-gold"></i>
                </div>
                <div class="about-text lh-lg text-secondary">
                  <p class="fs-6">
                    Sesuai dengan amanat <strong class="text-dark fw-semibold">Undang-Undang Nomor 49 tahun 2014</strong> tentang Sistem Nasional Pendidikan Tinggi bahwa setiap perguruan tinggi harus memenuhi standar nasional pendidikan sesuai dengan Tri Dharma Perguruan Tinggi. Melalui penjaminan mutu diharapkan perguruan tinggi dapat mengelola dan melaksanakan pendidikan sesuai dengan standar nasional pendidikan tinggi.
                  </p>
                  <p class="fs-6">
                    Berdasarkan amanat tersebut, maka pada kesempatan ini IKIP Siliwangi menyusun sistem penjaminan mutu sesuai dengan standar nasional pendidikan. Dengan harapan pengelolaan dan pelaksanaan pendidikan di IKIP Siliwangi berdasarkan pada standar nasional pendidikan dengan tujuan menjadi perguruan tinggi yang bermutu.
                  </p>
                  
                  <!-- Highlight Box PPEPP -->
                  <div class="ppepp-box mt-4 p-4 rounded-3 border-start border-4 border-gold bg-light-brown">
                    <div class="d-flex align-items-start">
                      <div class="icon-circle bg-gold text-white me-3 mt-1">
                        <i class="fas fa-sync-alt fa-lg"></i>
                      </div>
                      <div>
                        <h6 class="fw-bold text-dark mb-1">Siklus Penjaminan Mutu (PPEPP)</h6>
                        <p class="mb-0 text-muted small">
                          Ruang lingkup operasional kerja LPMI IKIP Siliwangi dalam menjalankan sistem penjaminan mutu internal adalah menjaga <strong class="text-dark">sustainability</strong> terciptanya penyelenggaraan pendidikan yang baik dengan melaksanakan siklus <span class="badge bg-gold text-white">Penetapan</span>, <span class="badge bg-gold text-white">Pelaksanaan</span>, <span class="badge bg-gold text-white">Evaluasi</span>, <span class="badge bg-gold text-white">Pengendalian</span>, dan <span class="badge bg-gold text-white">Peningkatan</span> secara konsisten.
                        </p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Visi & Misi Section -->
          <div class="row g-4 justify-content-center">
            <!-- Card Visi -->
            <div class="col-lg-5 col-md-6">
              <div class="vision-mission-card visi-card h-100 p-4 p-lg-5 bg-white rounded-4 shadow-sm border-0 position-relative overflow-hidden">
                <div class="card-accent-bar bg-gold"></div>
                <div class="d-flex align-items-center mb-4">
                  <div class="icon-square bg-gold-subtle text-gold rounded-3 p-3 me-3">
                    <i class="fas fa-eye fa-2x"></i>
                  </div>
                  <div>
                    <span class="text-uppercase tracking-wider text-muted small fw-bold">Gambaran Masa Depan</span>
                    <h3 class="fw-bold text-dark mb-0">VISI</h3>
                  </div>
                </div>
                <p class="card-text text-secondary lh-lg mb-0 fs-6">
                  Lembaga Penjamin Mutu Internal (LPMI) sebagai unit yang mampu melaksanakan, memonitoring, dan mengevaluasi kegiatan akademik dan non-akademik secara transparan, kredibel, terukur, dan profesional demi terciptanya visi IKIP Siliwangi.
                </p>
              </div>
            </div>

            <!-- Card Misi -->
            <div class="col-lg-7 col-md-6">
              <div class="vision-mission-card misi-card h-100 p-4 p-lg-5 bg-white rounded-4 shadow-sm border-0 position-relative overflow-hidden">
                <div class="card-accent-bar bg-primary-brown"></div>
                <div class="d-flex align-items-center mb-4">
                  <div class="icon-square bg-gold-subtle text-gold rounded-3 p-3 me-3">
                    <i class="fas fa-bullseye fa-2x"></i>
                  </div>
                  <div>
                    <span class="text-uppercase tracking-wider text-muted small fw-bold">Langkah Strategis</span>
                    <h3 class="fw-bold text-dark mb-0">MISI</h3>
                  </div>
                </div>
                
                <ul class="misi-list list-unstyled mb-0">
                  <li class="d-flex mb-3">
                    <span class="misi-number me-3">1</span>
                    <span class="text-secondary">Menjamin setiap program studi melaksanakan tugas dan fungsinya sesuai dengan standar yang ditetapkan.</span>
                  </li>
                  <li class="d-flex mb-3">
                    <span class="misi-number me-3">2</span>
                    <span class="text-secondary">Menyusun laporan hasil monitoring dan evaluasi kegiatan akademik dan nonakademik.</span>
                  </li>
                  <li class="d-flex mb-3">
                    <span class="misi-number me-3">3</span>
                    <span class="text-secondary">Melaksanakan evaluasi secara berkala terhadap pelaksanaan seluruh kegiatan akademik dan non-akademik.</span>
                  </li>
                  <li class="d-flex mb-3">
                    <span class="misi-number me-3">4</span>
                    <span class="text-secondary">Meningkatkan kompetensi sivitas akademika secara terus menerus sebagai penjaminan mutu akademik secara profesional, inovatif, dan kooperatif.</span>
                  </li>
                  <li class="d-flex">
                    <span class="misi-number me-3">5</span>
                    <span class="text-secondary">Mendorong, menciptakan, mengembangkan, dan memelihara secara terus menerus sistem penjaminan mutu akademik di IKIP Siliwangi.</span>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Berita & Jadwal -->
      <div class="container">
        <div class="row">
          <!-- Berita Section (Left Column) -->
          <div class="col-lg-8 mt-4">
            <div class="berita-section">
              <div class="section-header">
                <h2 class="section-title">
                  <i class="fas fa-newspaper"></i>Berita Terbaru
                </h2>
                <p class="text-muted mb-0">Informasi dan kegiatan terbaru dari SPMI</p>
              </div>
              
              <div v-if="beritas.length > 0" class="row g-4">
                <div v-for="berita in beritas" :key="berita.id" class="col-md-6">
                  <a :href="berita.link" target="_blank" class="text-decoration-none">
                    <div class="berita-card">
                      <img :src="berita.gambar_url" :alt="berita.judul" class="berita-image" @error="handleBeritaImageError">
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
              <div v-else class="text-center py-5">
                <i class="fas fa-newspaper fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">Belum ada berita</h5>
                <p class="text-muted">Berita akan ditampilkan di sini</p>
              </div>
              
              <div class="view-more">
                <router-link to="/berita" class="btn btn-primary btn-lg">
                  <i class="fas fa-list me-2"></i>Lihat Semua Berita
                </router-link>
              </div>
            </div>
          </div>
          
          <!-- Jadwal Sidebar (Right Column) -->
          <div class="col-lg-4">
            <div class="jadwal-sidebar">
              <div class="jadwal-header">
                <h2 class="section-title mb-0">
                  <i class="fas fa-calendar-alt"></i>Jadwal Kegiatan
                </h2>
                <p class="text-muted mb-0 mt-1">Jadwal mendatang</p>
              </div>
              
              <div v-if="jadwals.length > 0" class="jadwal-list">
                <div v-for="jadwal in jadwals" :key="jadwal.id" class="jadwal-item" @click="openJadwalModal(jadwal)">
                  <div class="jadwal-date">
                    <div class="jadwal-day">{{ getDay(jadwal.tanggal) }}</div>
                    <div class="jadwal-month">{{ getShortMonth(jadwal.tanggal) }}</div>
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
              <div v-else class="jadwal-empty">
                <i class="fas fa-calendar-times"></i>
                <h5 class="text-muted">Tidak ada jadwal</h5>
                <p class="text-muted">Jadwal akan ditampilkan di sini</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Dokumen Publik Section -->
      <section class="dokumen-section animate-on-scroll">
        <div class="container">
          <div class="row align-items-center">
            <div class="col-md-8">
              <h3 class="fw-bold mb-3">
                <i class="fas fa-globe me-2 text-warning"></i>Dokumen Publik SPMI
              </h3>
              <p class="text-muted mb-4">
                Akses dokumen SPMI yang tersedia untuk umum. Lihat berbagai dokumen, 
                laporan, dan informasi sistem penjaminan mutu internal kami.
              </p>
              <router-link to="/dokumen-publik" class="btn btn-warning btn-lg">
                <i class="fas fa-external-link-alt me-2"></i>Lihat Dokumen Publik
              </router-link>
            </div>
            <div class="col-md-4 text-center">
              <i class="fas fa-folder-open fa-6x text-muted opacity-50"></i>
            </div>
          </div>
        </div>
      </section>

      <!-- Features Section -->
      <section id="features" class="py-5 animate-on-scroll">
        <div class="container">
          <div class="text-center mb-5">
            <h2 class="section-title display-5 fw-bold">Fitur Unggulan SPMI</h2>
            <p class="lead text-muted">Solusi lengkap untuk manajemen mutu pendidikan</p>
          </div>
          <div class="row g-4">
            <div class="col-lg-4 col-md-6">
              <div class="program-card p-4 h-100 text-center">
                <div class="program-icon program-1 mx-auto mb-3">
                  <i class="fas fa-tasks fa-2x"></i>
                </div>
                <h4 class="fw-semibold mb-3">Manajemen Standar</h4>
                <p class="text-muted">
                  Kelola 12 standar mutu dengan sistem terstruktur untuk penjaminan kualitas pendidikan
                </p>
              </div>
            </div>
            <div class="col-lg-4 col-md-6">
              <div class="program-card p-4 h-100 text-center">
                <div class="program-icon program-2 mx-auto mb-3">
                  <i class="fas fa-clipboard-check fa-2x"></i>
                </div>
                <h4 class="fw-semibold mb-3">Audit Digital</h4>
                <p class="text-muted">
                  Lakukan 8 audit internal dengan tools lengkap dan pelaporan otomatis
                </p>
              </div>
            </div>
            <div class="col-lg-4 col-md-6">
              <div class="program-card p-4 h-100 text-center">
                <div class="program-icon program-3 mx-auto mb-3">
                  <i class="fas fa-chart-pie fa-2x"></i>
                </div>
                <h4 class="fw-semibold mb-3">Analisis Data</h4>
                <p class="text-muted">
                  Dashboard analitik untuk monitoring 6 program studi secara real-time
                </p>
              </div>
            </div>
            <div class="col-lg-4 col-md-6">
              <div class="program-card p-4 h-100 text-center">
                <div class="program-icon program-4 mx-auto mb-3">
                  <i class="fas fa-file-contract fa-2x"></i>
                </div>
                <h4 class="fw-semibold mb-3">Dokumen Mutu</h4>
                <p class="text-muted">
                  Kelola 24 dokumen mutu secara terpusat dengan version control yang aman
                </p>
              </div>
            </div>
            <div class="col-lg-4 col-md-6">
              <div class="program-card p-4 h-100 text-center">
                <div class="program-icon program-5 mx-auto mb-3">
                  <i class="fas fa-university fa-2x"></i>
                </div>
                <h4 class="fw-semibold mb-3">Program Studi</h4>
                <p class="text-muted">
                  Monitor 6 program studi termasuk Pascasarjana dan pendidikan khusus
                </p>
              </div>
            </div>
            <div class="col-lg-4 col-md-6">
              <div class="program-card p-4 h-100 text-center">
                <div class="program-icon program-6 mx-auto mb-3">
                  <i class="fas fa-laptop-code fa-2x"></i>
                </div>
                <h4 class="fw-semibold mb-3">LPM Smart Sistem</h4>
                <p class="text-muted">
                  Sistem terintegrasi untuk Lembaga Penjaminan Mutu yang efisien
                </p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Program Studi Preview Section -->
      <section id="programs" class="py-5 bg-light animate-on-scroll">
        <div class="container">
          <div class="text-center mb-5">
            <h2 class="section-title display-5 fw-bold">Program Studi Tersedia</h2>
            <p class="lead text-muted">Kelola berbagai program studi dalam satu sistem terpadu</p>
          </div>
          <div class="row g-4 justify-content-center">
            <div class="col-lg-2 col-md-4 col-6">
              <div class="text-center">
                <div class="program-icon-sm program-1">
                  <i class="fas fa-graduation-cap"></i>
                </div>
                <small class="fw-semibold">Ilmu Pendidikan</small>
              </div>
            </div>
            <div class="col-lg-2 col-md-4 col-6">
              <div class="text-center">
                <div class="program-icon-sm program-2">
                  <i class="fas fa-language"></i>
                </div>
                <small class="fw-semibold">Pendidikan Bahasa</small>
              </div>
            </div>
            <div class="col-lg-2 col-md-4 col-6">
              <div class="text-center">
                <div class="program-icon-sm program-3">
                  <i class="fas fa-calculator"></i>
                </div>
                <small class="fw-semibold">Matematika & Sains</small>
              </div>
            </div>
            <div class="col-lg-2 col-md-4 col-6">
              <div class="text-center">
                <div class="program-icon-sm program-4">
                  <i class="fas fa-user-graduate"></i>
                </div>
                <small class="fw-semibold">Program Khusus</small>
              </div>
            </div>
            <div class="col-lg-2 col-md-4 col-6">
              <div class="text-center">
                <div class="program-icon-sm program-5">
                  <i class="fas fa-user-tie"></i>
                </div>
                <small class="fw-semibold">Pascasarjana</small>
              </div>
            </div>
            <div class="col-lg-2 col-md-4 col-6">
              <div class="text-center">
                <div class="program-icon-sm program-6">
                  <i class="fas fa-laptop-code"></i>
                </div>
                <small class="fw-semibold">LPM Smart Sistem</small>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Stats Preview Section -->
      <section class="py-5 animate-on-scroll">
        <div class="container">
          <div class="row text-center">
            <div class="col-md-3 col-6 mb-4">
              <div class="p-3">
                <h3 class="fw-bold text-primary">12</h3>
                <p class="text-muted mb-0">Standar Mutu</p>
                <small class="text-primary">Terintegrasi</small>
              </div>
            </div>
            <div class="col-md-3 col-6 mb-4">
              <div class="p-3">
                <h3 class="fw-bold text-success">8</h3>
                <p class="text-muted mb-0">Audit Internal</p>
                <small class="text-success">Terselesaikan</small>
              </div>
            </div>
            <div class="col-md-3 col-6 mb-4">
              <div class="p-3">
                <h3 class="fw-bold text-warning">24</h3>
                <p class="text-muted mb-0">Dokumen</p>
                <small class="text-warning">Terkelola</small>
              </div>
            </div>
            <div class="col-md-3 col-6 mb-4">
              <div class="p-3">
                <h3 class="fw-bold text-info">6</h3>
                <p class="text-muted mb-0">Program Studi</p>
                <small class="text-info">Aktif</small>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Team Section -->
      <section id="team" class="py-5 animate-on-scroll">
        <div class="container">
          <div class="text-center mb-5">
            <h2 class="section-title display-5 fw-bold">Tim SPMI</h2>
            <p class="lead text-muted">Mengenal lebih dekat dengan tim penjaminan mutu internal kami</p>
          </div>
          <div v-if="team.length > 0" class="row g-4 justify-content-center">
            <div v-for="member in team" :key="member.id" class="col-lg-3 col-md-4 col-sm-6">
              <div class="team-card text-center p-4">
                <div class="team-image-wrapper mx-auto mb-3">
                  <img 
                    :src="member.image_url || '/images/photos/default-avatar.png'" 
                    :alt="member.name"
                    class="team-image img-fluid rounded-circle"
                    @error="handleAvatarError"
                  >
                </div>
                <h5 class="fw-bold mb-1">{{ member.name }}</h5>
                <p class="text-primary mb-0">{{ member.position }}</p>
              </div>
            </div>
          </div>
          <div v-else class="text-center text-muted">
            <p>Data tim belum tersedia.</p>
          </div>
        </div>
      </section>

      <!-- Documentation Section -->
      <section id="documentation" class="py-5 bg-light animate-on-scroll">
        <div class="container">
          <div class="text-center mb-5">
            <h2 class="section-title display-5 fw-bold">Dokumentasi Kegiatan</h2>
            <p class="lead text-muted">Galeri kegiatan dan aktivitas terbaru dari SPMI</p>
          </div>
          <div v-if="documentations.length > 0" class="row g-4">
            <div v-for="doc in documentations" :key="doc.id" class="col-lg-4 col-md-6">
              <div class="doc-card">
                <div class="doc-image-wrapper">
                  <img :src="doc.image_url" :alt="doc.title" class="doc-image" @error="handleDocImageError">
                  <div class="doc-overlay">
                    <span class="doc-date" v-if="doc.activity_date">
                      <i class="fas fa-calendar-alt me-1"></i> {{ formatDate(doc.activity_date) }}
                    </span>
                  </div>
                </div>
                <div class="doc-content p-4">
                  <h5 class="fw-bold mb-2">{{ doc.title }}</h5>
                  <p class="text-muted mb-0 small">{{ doc.description }}</p>
                </div>
              </div>
            </div>
          </div>
          <div v-else class="text-center text-muted">
            <p>Belum ada dokumentasi kegiatan.</p>
          </div>
        </div>
      </section>

      <!-- CTA Section -->
      <section class="py-5 cta-section animate-on-scroll">
        <div class="container text-center">
          <h2 class="display-6 fw-bold mb-4">Siap Transformasi Digital SPMI?</h2>
          <p class="lead mb-4 opacity-90">
            Bergabung dengan institusi pendidikan yang telah mempercayai SPMI 
            untuk transformasi digital sistem penjaminan mutu mereka.
          </p>
          <div class="d-flex justify-content-center gap-3 flex-wrap">
            <a href="#contact" class="btn btn-outline-light btn-lg">
              <i class="fas fa-phone me-2"></i>Hubungi Kami
            </a>
          </div>
        </div>
      </section>

      <!-- Contact Section -->
      <section id="contact" class="py-5 bg-light animate-on-scroll">
        <div class="container">
          <div class="text-center mb-5">
            <h2 class="section-title display-5 fw-bold">Hubungi Kami</h2>
            <p class="lead text-muted">Butuh informasi lebih lanjut? Tim kami siap membantu</p>
          </div>
          <div class="row justify-content-center">
            <div class="col-lg-8">
              <div class="program-card p-5">
                <div class="row text-center">
                  <div class="col-md-4 mb-4">
                    <i class="fas fa-envelope fa-2x text-primary mb-3"></i>
                    <h5>Email</h5>
                    <p class="text-muted">lpmi@ikipsiliwangi.ac.id</p>
                  </div>
                  <div class="col-md-4 mb-4">
                    <i class="fas fa-phone fa-2x text-primary mb-3"></i>
                    <h5>Telepon</h5>
                    <p class="text-muted">+62 21 1234 5678</p>
                  </div>
                  <div class="col-md-4 mb-4">
                    <i class="fas fa-clock fa-2x text-primary mb-3"></i>
                    <h5>Jam Operasional</h5>
                    <p class="text-muted">Senin - Jumat, 08:00 - 17:00</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </main>

    <!-- Modal Detail Jadwal -->
    <div v-if="showJadwalModal" class="modal-backdrop fade show" style="background-color: rgba(0,0,0,0.5);"></div>
    <div v-if="showJadwalModal" class="modal fade show d-block" tabindex="-1" role="dialog" @click.self="closeJadwalModal">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg rounded-4">
          <div class="modal-header bg-light border-bottom-0 pb-3 pt-4 px-4 rounded-top-4">
            <h5 class="modal-title fw-bold text-dark">Detail Kegiatan</h5>
            <button type="button" class="btn-close" @click="closeJadwalModal" aria-label="Close"></button>
          </div>
          <div class="modal-body px-4 py-4" v-if="selectedJadwal">
            <h4 class="fw-bold mb-3 text-primary-brown">{{ selectedJadwal.kegiatan }}</h4>
            <div class="d-flex flex-column gap-3">
              <div class="d-flex align-items-start">
                <i class="fas fa-calendar-alt text-gold mt-1 me-3" style="width: 20px;"></i>
                <div>
                  <div class="small text-muted mb-1">Tanggal</div>
                  <div class="fw-medium">{{ formatDate(selectedJadwal.tanggal) }}</div>
                </div>
              </div>
              <div class="d-flex align-items-start">
                <i class="fas fa-clock text-gold mt-1 me-3" style="width: 20px;"></i>
                <div>
                  <div class="small text-muted mb-1">Waktu</div>
                  <div class="fw-medium">
                    {{ formatTime(selectedJadwal.waktu_mulai) || '-' }} 
                    <span v-if="selectedJadwal.waktu_selesai"> - {{ formatTime(selectedJadwal.waktu_selesai) }}</span>
                  </div>
                </div>
              </div>
              <div class="d-flex align-items-start">
                <i class="fas fa-map-marker-alt text-gold mt-1 me-3" style="width: 20px;"></i>
                <div>
                  <div class="small text-muted mb-1">Tempat</div>
                  <div class="fw-medium">{{ selectedJadwal.tempat || 'Tidak ditentukan' }}</div>
                </div>
              </div>
              <div class="d-flex align-items-start" v-if="selectedJadwal.penyelenggara">
                <i class="fas fa-users text-gold mt-1 me-3" style="width: 20px;"></i>
                <div>
                  <div class="small text-muted mb-1">Penyelenggara</div>
                  <div class="fw-medium">{{ selectedJadwal.penyelenggara }}</div>
                </div>
              </div>
              <div class="d-flex align-items-start mt-2 pt-3 border-top" v-if="selectedJadwal.deskripsi">
                <i class="fas fa-align-left text-gold mt-1 me-3" style="width: 20px;"></i>
                <div>
                  <div class="small text-muted mb-1">Deskripsi</div>
                  <div class="text-secondary" style="white-space: pre-line;">{{ selectedJadwal.deskripsi }}</div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer border-top-0 px-4 pb-4 pt-0">
            <button type="button" class="btn btn-primary" @click="closeJadwalModal" style="background-color: var(--primary-brown); border-color: var(--primary-brown);">Tutup</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer Kustom Modern -->
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
                  <a href="https://ikipsiliwangi.ac.id" target="_blank" class="d-flex align-items-center text-white-50 text-decoration-none hover-white">
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

            <!-- Kolom 4: Widget Pengunjung Otomatis (Opsi 1) -->
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
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import { useLandingStore } from '@/stores/landingStore'

export default {
  name: 'LandingPage',
  setup() {
    const store = useLandingStore()

    // State Reactive untuk Modal Jadwal
    const showJadwalModal = ref(false)
    const selectedJadwal = ref(null)

    const openJadwalModal = (jadwal) => {
      selectedJadwal.value = jadwal
      showJadwalModal.value = true
      document.body.classList.add('modal-open')
    }

    const closeJadwalModal = () => {
      showJadwalModal.value = false
      setTimeout(() => {
        selectedJadwal.value = null
      }, 300) // wait for animation
      document.body.classList.remove('modal-open')
    }

    // State Reactive untuk Statistik Pengunjung
    const visitorStats = ref({
      today: 0,
      yesterday: 0,
      total: 0,
      online: 1
    })

    const heroContent = computed(() => {
      const h = store.heroContent
      return {
        title: h.title || 'Sistem Penjaminan Mutu Internal',
        subtitle: h.subtitle || 'Mewujudkan Budaya Mutu di Lingkungan Akademik',
        description: h.description || 'E-SPMI Digital membantu institusi pendidikan dalam mengelola, memantau, dan meningkatkan mutu secara berkelanjutan.',
        ctaText: h.cta_text || 'Mulai Sekarang',
        ctaLink: h.cta_link || '#features'
      }
    })

    const heroStyle = computed(() => {
      const bg = store.heroContent.background_image
      if (bg) {
        return {
          background: `linear-gradient(135deg, rgba(153,102,0,0.85), rgba(122,82,0,0.9)), url('/storage/${bg}') center/cover no-repeat`
        }
      }
      return {
        background: 'linear-gradient(135deg, #7a5200 0%, #4a3200 100%)'
      }
    })

    const beritas = computed(() => store.beritas)
    const jadwals = computed(() => store.jadwals)
    const team = computed(() => store.team)
    const documentations = computed(() => store.documentations)

    // LOGIC RECORD & HITUNG PENGUNJUNG OTOMATIS
    const recordAndFetchVisitors = async () => {
      try {
        // PERHATIAN: Jika backend (Laravel/Express) Anda sudah punya API visitor,
        // hilangkan comment baris berikut dan sesuaikan endpoint-nya:
        /*
        const response = await axios.post('/api/visitors/record')
        visitorStats.value = response.data
        return
        */

        // FALLBACK AUTOMATIC LOCAL STORAGE LOGIC:
        const todayStr = new Date().toISOString().slice(0, 10)
        const lastVisitDate = localStorage.getItem('site_last_visit_date')

        let totalVisits = parseInt(localStorage.getItem('site_total_visits') || '1250')
        let todayVisits = parseInt(localStorage.getItem('site_today_visits') || '24')
        let yesterdayVisits = parseInt(localStorage.getItem('site_yesterday_visits') || '18')

        // Jika berganti hari, reset hitungan hari ini
        if (lastVisitDate !== todayStr) {
          yesterdayVisits = todayVisits
          todayVisits = 0
          localStorage.setItem('site_yesterday_visits', yesterdayVisits)
          localStorage.setItem('site_last_visit_date', todayStr)
        }

        // Jika pengguna baru pertama kali masuk di sesi browser ini
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
          online: Math.floor(Math.random() * 6) + 2 // Simulasi pengguna aktif
        }
      } catch (error) {
        console.error('Gagal mencatat statistik pengunjung:', error)
      }
    }

    const handleImageError = (event) => {
      event.target.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="%237a5200" stroke-width="2"%3E%3Cpath d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"%3E%3C/path%3E%3C/svg%3E'
    }

    const handleBeritaImageError = (event) => {
      event.target.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 24 24" fill="none" stroke="%237a5200" stroke-width="2"%3E%3Crect x="2" y="2" width="20" height="20" rx="2"%3E%3C/rect%3E%3Cpath d="M8 7h8M8 12h6M8 17h4"%3E%3C/path%3E%3C/svg%3E'
    }

    const handleAvatarError = (event) => {
      event.target.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 24 24" fill="none" stroke="%23cccccc" stroke-width="2"%3E%3Ccircle cx="12" cy="8" r="4"%3E%3C/circle%3E%3Cpath d="M12 14c-6.1 0-8 4-8 4v2h16v-2s-1.9-4-8-4z"%3E%3C/path%3E%3C/svg%3E'
    }

    const handleDocImageError = (event) => {
      event.target.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 24 24" fill="none" stroke="%237a5200" stroke-width="2"%3E%3Crect x="3" y="3" width="18" height="18" rx="2"%3E%3C/rect%3E%3Ccircle cx="8.5" cy="8.5" r="1.5"%3E%3C/circle%3E%3Cpath d="M21 15l-5-5L5 21"%3E%3C/path%3E%3C/svg%3E'
    }

    const truncateText = (text, length) => {
      if (!text) return ''
      return text.length > length ? text.substring(0, length) + '...' : text
    }

    const formatDate = (date) => {
      if (!date) return ''
      const d = new Date(date)
      return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })
    }

    const getDay = (date) => {
      if (!date) return ''
      const d = new Date(date)
      return d.getDate().toString().padStart(2, '0')
    }

    const getShortMonth = (date) => {
      if (!date) return ''
      const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des']
      const d = new Date(date)
      return months[d.getMonth()]
    }

    const formatTime = (time) => {
      if (!time) return ''
      if (typeof time === 'string') {
        const parts = time.split(':')
        return `${parts[0]}:${parts[1]}`
      }
      return time
    }

    onMounted(() => {
      if (typeof window !== 'undefined') {
        import('bootstrap/dist/js/bootstrap.bundle.min.js').catch(() => {
          console.warn('Bootstrap JS not loaded')
        })
      }

      // Jalankan fungsi pencatat pengunjung otomatis
      recordAndFetchVisitors()

      store.fetchLandingContent()
      store.fetchBeritas()
      store.fetchJadwals()

      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.classList.add('animate-visible')
          }
        })
      }, { threshold: 0.1 })

      setTimeout(() => {
        document.querySelectorAll('.animate-on-scroll').forEach(el => {
          observer.observe(el)
        })
      }, 100)

      window.addEventListener('scroll', () => {
        const navbar = document.querySelector('.navbar')
        if (navbar) {
          if (window.scrollY > 50) {
            navbar.classList.add('scrolled')
          } else {
            navbar.classList.remove('scrolled')
          }
        }
      })
    })

    return {
      showJadwalModal,
      selectedJadwal,
      openJadwalModal,
      closeJadwalModal,
      visitorStats,
      heroContent,
      heroStyle,
      beritas,
      jadwals,
      team,
      documentations,
      handleImageError,
      handleBeritaImageError,
      handleAvatarError,
      handleDocImageError,
      truncateText,
      formatDate,
      getDay,
      getShortMonth,
      formatTime
    }
  }
}
</script>

<style scoped>
/* ===== COLOR PALETTE & VARIABLES ===== */
:root {
  --primary-brown: #7a5200;
  --secondary-brown: #996600;
  --accent-gold: #b37400;
  --dark-brown: #4a3200;
  --light-brown: #fefcf5;
  --gold-subtle: #fef3d6;
}

/* Color Classes */
.bg-gold { background-color: var(--secondary-brown) !important; }
.bg-gold-subtle { background-color: var(--gold-subtle) !important; }
.bg-light-brown { background-color: var(--light-brown) !important; }
.text-gold { color: var(--secondary-brown) !important; }
.text-gold-light { color: #ffe6a3 !important; }
.text-primary-brown { color: var(--primary-brown) !important; }
.border-gold { border-color: var(--secondary-brown) !important; }

/* ===== NAVBAR ===== */
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

/* ===== HERO SECTION ===== */
.hero-section {
  color: white;
  padding: 140px 0 100px;
  position: relative;
  overflow: hidden;
}

/* ===== ABOUT SECTION STYLING ===== */
.about-section {
  background: linear-gradient(180deg, #f8f9fa 0%, #fff9ed 100%);
}

.divider-line {
  width: 60px;
  height: 4px;
  background: var(--secondary-brown);
  border-radius: 2px;
}

.about-card {
  box-shadow: 0 10px 30px rgba(122, 82, 0, 0.05) !important;
}

.icon-circle {
  width: 38px;
  height: 38px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.icon-square {
  width: 54px;
  height: 54px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.vision-mission-card {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 8px 25px rgba(0,0,0,0.04) !important;
}

.vision-mission-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 15px 35px rgba(122, 82, 0, 0.1) !important;
}

.card-accent-bar {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
}

.misi-number {
  width: 24px;
  height: 24px;
  background-color: var(--gold-subtle);
  color: var(--primary-brown);
  font-weight: 700;
  font-size: 0.8rem;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  margin-top: 2px;
}

/* ===== FOOTER MODERN STYLING ===== */
.footer-modern {
  background-color: var(--dark-brown);
  position: relative;
}

.footer-logo {
  max-width: 65px;
  height: auto;
  filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));
}

.hover-gold:hover {
  color: var(--accent-gold) !important;
}

.hover-white:hover {
  color: #ffffff !important;
}

.visitor-card {
  border: 1px solid rgba(255,255,255,0.1);
  background: rgba(255, 255, 255, 0.96) !important;
}

/* ===== PROGRAM & OTHER CARDS ===== */
.program-card {
  background: white;
  border-radius: 15px;
  padding: 2rem;
  box-shadow: 0 5px 15px rgba(0,0,0,0.08);
  transition: all 0.3s ease;
  height: 100%;
  border: 1px solid #e9ecef;
}

.program-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}

.program-icon {
  width: 80px;
  height: 80px;
  border-radius: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 1rem;
  font-size: 2rem;
  color: white;
}

.program-icon-sm {
  width: 50px;
  height: 50px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  margin: 0 auto 10px;
}

.program-1 { background: linear-gradient(135deg, #7a5200 0%, #996600 100%); }
.program-2 { background: linear-gradient(135deg, #8c5d00 0%, #b37400 100%); }
.program-3 { background: linear-gradient(135deg, #a06a00 0%, #cc8800 100%); }
.program-4 { background: linear-gradient(135deg, #b37400 0%, #dd9900 100%); }
.program-5 { background: linear-gradient(135deg, #c68100 0%, #eeaa00 100%); }
.program-6 { background: linear-gradient(135deg, #d98e00 0%, #ffbb00 100%); color: #333 !important; }

/* ===== SECTION TITLE ===== */
.section-title {
  color: var(--dark-brown);
  font-weight: 700;
  margin-bottom: 0.5rem;
}

.text-primary { color: var(--primary-brown) !important; }
.text-success { color: #2e7d32 !important; }
.text-warning { color: #b37400 !important; }
.text-info { color: #0288d1 !important; }

/* ===== BERITA SECTION ===== */
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

.view-more {
  text-align: center;
  margin-top: 2rem;
  padding-top: 1.5rem;
  border-top: 1px solid #e9ecef;
}

/* ===== JADWAL SIDEBAR ===== */
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
  cursor: pointer;
  box-shadow: 0 4px 6px rgba(0,0,0,0.05);
}

.jadwal-date {
  min-width: 50px;
  text-align: center;
  margin-right: 1rem;
  background: var(--gold-subtle);
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

/* ===== DOKUMEN SECTION ===== */
.dokumen-section {
  background: #f8f9fa;
  padding: 80px 0;
  margin-top: 40px;
}

.btn-warning {
  background: linear-gradient(135deg, var(--secondary-brown), var(--primary-brown));
  border: none;
  color: white;
  padding: 10px 25px;
  font-weight: 600;
  border-radius: 8px;
}

.btn-warning:hover {
  background: linear-gradient(135deg, var(--primary-brown), var(--dark-brown));
  transform: translateY(-2px);
  box-shadow: 0 10px 20px rgba(122, 82, 0, 0.3);
  color: white;
}

/* ===== TEAM SECTION ===== */
.team-card {
  background: white;
  border-radius: 15px;
  box-shadow: 0 5px 15px rgba(0,0,0,0.05);
  transition: all 0.3s ease;
  height: 100%;
  border: 1px solid #e9ecef;
}

.team-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 25px rgba(0,0,0,0.1);
  border-color: var(--primary-brown);
}

.team-image-wrapper {
  width: 120px;
  height: 120px;
  border-radius: 50%;
  padding: 5px;
  background: linear-gradient(135deg, var(--gold-subtle), var(--primary-brown));
}

.team-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border: 3px solid white;
}

/* ===== DOCUMENTATION SECTION ===== */
.doc-card {
  background: white;
  border-radius: 15px;
  overflow: hidden;
  box-shadow: 0 5px 15px rgba(0,0,0,0.08);
  transition: all 0.3s ease;
  height: 100%;
  border: 1px solid #e9ecef;
}

.doc-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}

.doc-image-wrapper {
  position: relative;
  width: 100%;
  height: 200px;
  overflow: hidden;
}

.doc-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.doc-card:hover .doc-image {
  transform: scale(1.05);
}

.doc-overlay {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  padding: 15px;
  background: linear-gradient(to top, rgba(0,0,0,0.7), transparent);
  color: white;
}

.doc-date {
  font-size: 0.85rem;
  background: var(--primary-brown);
  padding: 4px 10px;
  border-radius: 20px;
  display: inline-block;
}

/* ===== CTA SECTION ===== */
.cta-section {
  background: linear-gradient(135deg, var(--primary-brown) 0%, var(--dark-brown) 100%);
  color: white;
}

/* ===== ANIMATIONS ===== */
.animate-on-scroll {
  opacity: 0;
  transform: translateY(40px);
  transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
}

.animate-on-scroll.animate-visible {
  opacity: 1;
  transform: translateY(0);
}

@media (max-width: 768px) {
  .hero-section {
    padding: 100px 0 60px;
    text-align: center;
  }
  
  .jadwal-sidebar {
    position: static;
    margin-top: 2rem;
    max-height: none;
  }
}
</style>