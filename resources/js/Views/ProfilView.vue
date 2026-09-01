<!-- src/Views/ProfilView.vue -->
<template>
  <div class="profil-page">
    <div class="page-hero">
      <div class="container text-center">
        <h1 class="display-5 fw-bold mb-2">Profil LPMI</h1>
        <p class="lead mb-0 opacity-90">Lembaga Penjaminan Mutu Internal - IKIP Siliwangi</p>
      </div>
    </div>
    
    <div class="container py-5">
      <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><router-link to="/" class="text-brown text-decoration-none">Beranda</router-link></li>
          <li class="breadcrumb-item text-muted">Tentang</li>
          <li class="breadcrumb-item active" aria-current="page">Profil LPMI</li>
        </ol>
      </nav>

      <div class="row justify-content-center">
        <div class="col-lg-10">
          <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5">
            <div class="card-body">
              <div class="text-center mb-4">
                <i class="fas fa-university fa-3x text-gold mb-3"></i>
                <h2 class="fw-bold text-dark mb-3">Tentang LPMI IKIP Siliwangi</h2>
                <div class="divider-line mx-auto"></div>
              </div>
              
              <div class="content-text lh-lg text-secondary fs-5 mt-4">
                <p>
                  Sesuai dengan amanat <strong class="text-dark fw-semibold">Undang-Undang Nomor 49 tahun 2014</strong> tentang Sistem Nasional Pendidikan Tinggi bahwa setiap perguruan tinggi harus memenuhi standar nasional pendidikan sesuai dengan Tri Dharma Perguruan Tinggi. Melalui penjaminan mutu diharapkan perguruan tinggi dapat mengelola dan melaksanakan pendidikan sesuai dengan standar nasional pendidikan tinggi.
                </p>
                <p>
                  Berdasarkan amanat tersebut, maka pada kesempatan ini IKIP Siliwangi menyusun sistem penjaminan mutu sesuai dengan standar nasional pendidikan. Dengan harapan pengelolaan dan pelaksanaan pendidikan di IKIP Siliwangi berdasarkan pada standar nasional pendidikan dengan tujuan menjadi perguruan tinggi yang bermutu.
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ProfilView'
}
</script>

<style scoped>
.page-hero {
  background: linear-gradient(135deg, #7a5200 0%, #4a3200 100%);
  color: white;
  padding: 60px 0 40px;
}

.text-brown {
  color: #7a5200 !important;
}

.text-gold {
  color: #996600 !important;
}

.divider-line {
  width: 60px;
  height: 4px;
  background: #996600;
  border-radius: 2px;
}
</style>
