<!-- src/components/PublicLayout.vue -->
<template>
  <div class="public-layout">
    <AppNavbar />
    
    <main style="padding-top: 76px; min-height: 100vh; display: flex; flex-direction: column;">
      <div style="flex: 1;">
        <router-view />
      </div>
    </main>

    <AppFooter />
  </div>
</template>

<script>
import AppNavbar from '@/components/Navbar.vue'
import AppFooter from '@/components/Footer.vue'

export default {
  name: 'PublicLayout',
  components: {
    AppNavbar,
    AppFooter
  }
}
</script>

<style scoped>
.public-layout {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}
</style>
