<template>
  <section id="programs" class="py-5 bg-light">
    <div class="container">
      <div class="text-center mb-5">
        <h2 class="section-title display-5 fw-bold">Program Studi Tersedia</h2>
        <p class="lead text-muted">Kelola berbagai program studi dalam satu sistem terpadu</p>
      </div>
      <div class="row g-4 justify-content-center">
        <div v-for="(program, index) in programs" :key="index" class="col-lg-2 col-md-4 col-6">
          <div class="text-center">
            <div :class="['program-icon-sm', `program-${index + 1}`]">
              <i :class="program.icon"></i>
            </div>
            <small class="fw-semibold">{{ program.name }}</small>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: 'ProgramStudiSection',
  data() {
    return {
      programs: [
        { icon: 'fas fa-graduation-cap', name: 'Ilmu Pendidikan' },
        { icon: 'fas fa-language', name: 'Pendidikan Bahasa' },
        { icon: 'fas fa-calculator', name: 'Matematika & Sains' },
        { icon: 'fas fa-user-graduate', name: 'Program Khusus' },
        { icon: 'fas fa-user-tie', name: 'Pascasarjana' },
        { icon: 'fas fa-laptop-code', name: 'LPM Smart Sistem' }
      ]
    }
  }
}
</script>

<style scoped>
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