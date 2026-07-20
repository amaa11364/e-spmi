<template>
  <section class="py-5">
    <div class="container">
      <div class="row text-center">
        <div v-for="(stat, index) in stats" :key="index" class="col-md-3 col-6 mb-4">
          <div class="p-3">
            <h3 class="fw-bold" :class="stat.color">{{ stat.value }}</h3>
            <p class="text-muted mb-0">{{ stat.label }}</p>
            <small :class="stat.color">{{ stat.subLabel }}</small>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: 'StatsSection',
  data() {
    return {
      stats: [
        { value: '12', label: 'Standar Mutu', subLabel: 'Terintegrasi', color: 'text-primary' },
        { value: '8', label: 'Audit Internal', subLabel: 'Terselesaikan', color: 'text-success' },
        { value: '24', label: 'Dokumen', subLabel: 'Terkelola', color: 'text-warning' },
        { value: '6', label: 'Program Studi', subLabel: 'Aktif', color: 'text-info' }
      ]
    }
  }
}
</script>

<style scoped>
.text-primary { color: var(--primary-brown) !important; }
.text-success { color: #996600 !important; }
.text-warning { color: #b37400 !important; }
.text-info { color: #cc9900 !important; }
</style>