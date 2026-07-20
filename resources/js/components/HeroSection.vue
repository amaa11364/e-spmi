<template>
  <section id="home" class="hero-section" :style="heroStyle">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-6 col-md-12 position-relative text-center text-lg-start">
          <h1 class="display-4 fw-bold mb-3 text-white">{{ heroContent.title }}</h1>
          <h3 class="h4 mb-4 text-white opacity-90">{{ heroContent.subtitle }}</h3>
          <div class="lead mb-4 text-white opacity-90 fw-medium" v-html="formattedDescription"></div>
          <a :href="heroContent.cta_link" class="btn btn-light btn-lg">
            {{ heroContent.cta_text }} <i class="fas fa-arrow-right ms-2"></i>
          </a>
        </div>
        <div class="col-lg-6 col-md-12 text-center position-relative">
          <div v-if="isAdmin" class="mt-4">
            <button class="btn btn-sm btn-warning" @click="editHero">
              <i class="fas fa-edit me-1"></i>Edit Hero Section
            </button>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: 'HeroSection',
  props: {
    heroContent: {
      type: Object,
      required: true,
      default: () => ({
        title: 'Sistem Penjaminan Mutu Internal Digital',
        subtitle: 'Transformasi Digital SPMI IKIP',
        description: 'Platform terintegrasi untuk memonitor, mengelola, dan meningkatkan mutu pendidikan secara digital.',
        cta_text: 'Mulai Sekarang',
        cta_link: '#features',
        background_image: null
      })
    },
    isAdmin: {
      type: Boolean,
      default: false
    }
  },
  computed: {
    heroStyle() {
      if (this.heroContent.background_image) {
        return {
          background: `linear-gradient(rgba(153, 102, 0, 0.85), rgba(122, 82, 0, 0.85)),
                      url('${this.heroContent.background_image}')`,
          backgroundSize: 'cover',
          backgroundPosition: 'center',
          backgroundAttachment: 'fixed'
        }
      }
      return {
        background: 'linear-gradient(135deg, var(--primary-brown) 0%, var(--dark-brown) 100%)'
      }
    },
    formattedDescription() {
      return this.heroContent.description ? this.heroContent.description.replace(/\n/g, '<br>') : ''
    }
  },
  methods: {
    editHero() {
      this.$emit('edit-hero')
    }
  }
}
</script>

<style scoped>
.hero-section {
  color: white;
  padding: 140px 0 100px;
  position: relative;
  overflow: hidden;
}

.hero-section::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><polygon fill="%23ffffff" fill-opacity="0.03" points="0,1000 1000,0 1000,1000"/></svg>');
  background-size: cover;
}

@media (max-width: 768px) {
  .hero-section {
    padding: 100px 0 60px;
    text-align: center;
  }
}

@media (max-width: 576px) {
  .hero-section {
    padding: 80px 0 40px;
  }
}
</style>