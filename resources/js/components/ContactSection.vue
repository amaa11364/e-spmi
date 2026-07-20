<template>
  <section id="contact" class="py-5 bg-light">
    <div class="container">
      <div class="text-center mb-5">
        <h2 class="section-title display-5 fw-bold">Hubungi Kami</h2>
        <p class="lead text-muted">Butuh informasi lebih lanjut? Tim kami siap membantu</p>
      </div>
      <div class="row justify-content-center">
        <div class="col-lg-8">
          <div class="program-card p-5">
            <div class="row text-center">
              <div v-for="(contact, index) in contacts" :key="index" class="col-md-4 mb-4">
                <i :class="[contact.icon, 'fa-2x', 'text-primary', 'mb-3']"></i>
                <h5>{{ contact.title }}</h5>
                <p class="text-muted">{{ contact.value }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: 'ContactSection',
  data() {
    return {
      contacts: [
        { icon: 'fas fa-envelope', title: 'Email', value: 'info@qtrack-spmi.ac.id' },
        { icon: 'fas fa-phone', title: 'Telepon', value: '+62 21 1234 5678' },
        { icon: 'fas fa-clock', title: 'Jam Operasional', value: 'Senin - Jumat, 08:00 - 17:00' }
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
  border: 1px solid #e9ecef;
}

.section-title {
  color: var(--dark-brown);
  font-weight: 700;
}
</style>