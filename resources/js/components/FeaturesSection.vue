<template>
  <section id="features" class="py-5">
    <div class="container">
      <div class="text-center mb-5">
        <h2 class="section-title display-5 fw-bold">Fitur Unggulan SPMI</h2>
        <p class="lead text-muted">Solusi lengkap untuk manajemen mutu pendidikan</p>
      </div>
      <div class="row g-4">
        <div v-for="(feature, index) in features" :key="index" class="col-lg-4 col-md-6">
          <div class="program-card p-4 h-100 text-center">
            <div :class="['program-icon', `program-${index + 1}`, 'mx-auto', 'mb-3']">
              <i :class="[feature.icon, 'fa-2x']"></i>
            </div>
            <h4 class="fw-semibold mb-3">{{ feature.title }}</h4>
            <p class="text-muted">{{ feature.description }}</p>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: 'FeaturesSection',
  data() {
    return {
      features: [
        {
          icon: 'fas fa-tasks',
          title: 'Manajemen Standar',
          description: 'Kelola 12 standar mutu dengan sistem terstruktur untuk penjaminan kualitas pendidikan'
        },
        {
          icon: 'fas fa-clipboard-check',
          title: 'Audit Digital',
          description: 'Lakukan 8 audit internal dengan tools lengkap dan pelaporan otomatis'
        },
        {
          icon: 'fas fa-chart-pie',
          title: 'Analisis Data',
          description: 'Dashboard analitik untuk monitoring 6 program studi secara real-time'
        },
        {
          icon: 'fas fa-file-contract',
          title: 'Dokumen Mutu',
          description: 'Kelola 24 dokumen mutu secara terpusat dengan version control yang aman'
        },
        {
          icon: 'fas fa-university',
          title: 'Program Studi',
          description: 'Monitor 6 program studi termasuk Pascasarjana dan pendidikan khusus'
        },
        {
          icon: 'fas fa-laptop-code',
          title: 'LPM Smart Sistem',
          description: 'Sistem terintegrasi untuk Lembaga Penjaminan Mutu yang efisien'
        }
      ]
    }
  }
}
</script>

<style scoped>
.program-card {
  background: white;
  border-radius: 15px;
  padding: 2rem;
  text-align: center;
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
  color: white;
}

.program-1 { background: linear-gradient(135deg, #996600 0%, #b37400 100%); }
.program-2 { background: linear-gradient(135deg, #aa7700 0%, #cc8800 100%); }
.program-3 { background: linear-gradient(135deg, #bb8800 0%, #dd9900 100%); }
.program-4 { background: linear-gradient(135deg, #cc9900 0%, #eeaa00 100%); }
.program-5 { background: linear-gradient(135deg, #ddaa00 0%, #ffbb00 100%); }
.program-6 { background: linear-gradient(135deg, #eebb00 0%, #ffcc00 100%); color: #333 !important; }

.section-title {
  color: var(--dark-brown);
  font-weight: 700;
}
</style>