<!-- src/Views/SiklusPpeppView.vue -->
<template>
  <div class="ppepp-page">
    <div class="page-hero">
      <div class="container text-center">
        <h1 class="display-5 fw-bold mb-2">Siklus PPEPP</h1>
        <p class="lead mb-0 opacity-90">Siklus Penjaminan Mutu Internal - IKIP Siliwangi</p>
      </div>
    </div>
    
    <div class="container py-5">
      <nav aria-label="breadcrumb" class="mb-5">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><router-link to="/" class="text-brown text-decoration-none">Beranda</router-link></li>
          <li class="breadcrumb-item text-muted">Tentang</li>
          <li class="breadcrumb-item active" aria-current="page">Siklus PPEPP</li>
        </ol>
      </nav>

      <div class="row justify-content-center mb-5">
        <div class="col-lg-10 text-center">
          <h2 class="fw-bold text-dark mb-4">Siklus Penjaminan Mutu</h2>
          <p class="lead text-secondary mx-auto" style="max-width: 800px;">
            Ruang lingkup operasional kerja LPMI IKIP Siliwangi dalam menjalankan sistem penjaminan mutu internal adalah menjaga <strong class="text-dark">sustainability</strong> terciptanya penyelenggaraan pendidikan yang baik dengan melaksanakan siklus secara konsisten.
          </p>
        </div>
      </div>

      <div class="row g-4 justify-content-center">
        <!-- Penetapan -->
        <div class="col-md-6 col-lg-4">
          <div class="ppepp-card h-100 bg-white rounded-4 shadow-sm p-4 text-center border-0">
            <div class="step-icon bg-gold-subtle text-gold mx-auto mb-3">
              <i class="fas fa-clipboard-list fa-2x"></i>
            </div>
            <h4 class="fw-bold text-primary-brown mb-3">P - Penetapan</h4>
            <p class="text-secondary small">
              Penetapan Standar Pendidikan Tinggi. Tahap dimana institusi merancang dan merumuskan standar mutu yang akan dicapai dalam kurun waktu tertentu berdasarkan visi dan misi institusi.
            </p>
          </div>
        </div>

        <!-- Pelaksanaan -->
        <div class="col-md-6 col-lg-4">
          <div class="ppepp-card h-100 bg-white rounded-4 shadow-sm p-4 text-center border-0">
            <div class="step-icon bg-gold-subtle text-gold mx-auto mb-3">
              <i class="fas fa-cogs fa-2x"></i>
            </div>
            <h4 class="fw-bold text-primary-brown mb-3">P - Pelaksanaan</h4>
            <p class="text-secondary small">
              Pelaksanaan Standar Pendidikan Tinggi. Tahap penerapan dan implementasi dari standar-standar mutu yang telah ditetapkan ke dalam setiap aktivitas dan proses tridharma perguruan tinggi.
            </p>
          </div>
        </div>

        <!-- Evaluasi -->
        <div class="col-md-6 col-lg-4">
          <div class="ppepp-card h-100 bg-white rounded-4 shadow-sm p-4 text-center border-0">
            <div class="step-icon bg-gold-subtle text-gold mx-auto mb-3">
              <i class="fas fa-search-chart fa-2x"></i>
            </div>
            <h4 class="fw-bold text-primary-brown mb-3">E - Evaluasi</h4>
            <p class="text-secondary small">
              Evaluasi Pelaksanaan Standar Pendidikan Tinggi. Melakukan monitoring dan audit mutu internal (AMI) untuk membandingkan antara luaran atau capaian aktual dengan standar yang ditetapkan.
            </p>
          </div>
        </div>

        <!-- Pengendalian -->
        <div class="col-md-6 col-lg-4">
          <div class="ppepp-card h-100 bg-white rounded-4 shadow-sm p-4 text-center border-0">
            <div class="step-icon bg-gold-subtle text-gold mx-auto mb-3">
              <i class="fas fa-sliders-h fa-2x"></i>
            </div>
            <h4 class="fw-bold text-primary-brown mb-3">P - Pengendalian</h4>
            <p class="text-secondary small">
              Pengendalian Pelaksanaan Standar Pendidikan Tinggi. Melakukan tindakan korektif jika ditemukan penyimpangan atau ketidaktercapaian dari hasil evaluasi untuk memastikan kesesuaian dengan standar.
            </p>
          </div>
        </div>

        <!-- Peningkatan -->
        <div class="col-md-6 col-lg-4">
          <div class="ppepp-card h-100 bg-white rounded-4 shadow-sm p-4 text-center border-0">
            <div class="step-icon bg-gold-subtle text-gold mx-auto mb-3">
              <i class="fas fa-arrow-up fa-2x"></i>
            </div>
            <h4 class="fw-bold text-primary-brown mb-3">P - Peningkatan</h4>
            <p class="text-secondary small">
              Peningkatan Standar Pendidikan Tinggi. Upaya sistematis untuk menetapkan standar mutu yang lebih tinggi dari standar sebelumnya (continuous quality improvement) setelah target standar terpenuhi.
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'SiklusPpeppView'
}
</script>

<style scoped>
.page-hero {
  background: linear-gradient(135deg, #7a5200 0%, #4a3200 100%);
  color: white;
  padding: 60px 0 40px;
}

.text-brown { color: #7a5200 !important; }
.text-primary-brown { color: #7a5200 !important; }
.text-gold { color: #996600 !important; }
.bg-gold-subtle { background-color: #fef3d6 !important; }

.step-icon {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
}

.ppepp-card {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  border: 1px solid #f8f9fa !important;
}

.ppepp-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 15px 35px rgba(122, 82, 0, 0.1) !important;
  border-color: #fef3d6 !important;
}

.ppepp-card:hover .step-icon {
  background-color: #996600 !important;
  color: white !important;
  transform: scale(1.1);
}
</style>
