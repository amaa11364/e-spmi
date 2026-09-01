<!-- src/Views/VisiMisiView.vue -->
<template>
  <div class="visi-misi-page">
    <div class="page-hero">
      <div class="container text-center">
        <h1 class="display-5 fw-bold mb-2">Visi & Misi</h1>
        <p class="lead mb-0 opacity-90">Lembaga Penjaminan Mutu Internal - IKIP Siliwangi</p>
      </div>
    </div>
    
    <div class="container py-5">
      <nav aria-label="breadcrumb" class="mb-5">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><router-link to="/" class="text-brown text-decoration-none">Beranda</router-link></li>
          <li class="breadcrumb-item text-muted">Tentang</li>
          <li class="breadcrumb-item active" aria-current="page">Visi & Misi</li>
        </ol>
      </nav>

      <div class="row g-4 justify-content-center">
        <!-- Visi Card -->
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

        <!-- Misi Card -->
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
  </div>
</template>

<script>
export default {
  name: 'VisiMisiView'
}
</script>

<style scoped>
.page-hero {
  background: linear-gradient(135deg, #7a5200 0%, #4a3200 100%);
  color: white;
  padding: 60px 0 40px;
}

.text-brown { color: #7a5200 !important; }
.text-gold { color: #996600 !important; }
.bg-gold { background-color: #996600 !important; }
.bg-primary-brown { background-color: #7a5200 !important; }
.bg-gold-subtle { background-color: #fef3d6 !important; }

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
  background-color: #fef3d6;
  color: #7a5200;
  font-weight: 700;
  font-size: 0.8rem;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  margin-top: 2px;
}
</style>
