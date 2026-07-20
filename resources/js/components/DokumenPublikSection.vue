<template>
  <section class="dokumen-section">
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
</template>

<script>
export default {
  name: 'DokumenPublikSection'
}
</script>

<style scoped>
.dokumen-section {
  background: #f8f9fa;
  padding: 80px 0;
  margin-top: 40px;
}

.btn-warning {
  background: linear-gradient(135deg, #cc9900, #b37400);
  border: none;
  color: white;
  font-weight: 600;
}

.btn-warning:hover {
  background: linear-gradient(135deg, #b37400, #7a5200);
  color: white;
  transform: translateY(-2px);
  box-shadow: 0 10px 20px rgba(153, 102, 0, 0.3);
}

@media (max-width: 768px) {
  .dokumen-section {
    padding: 40px 0;
    text-align: center;
  }
  
  .col-md-4 {
    margin-top: 2rem;
  }
}
</style>