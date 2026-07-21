// src/app.js
import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'

// ===== IMPORT GLOBAL STYLES =====
import './styles/global.css'

// ===== IMPORT CDN CSS VIA JS =====
// Bootstrap
import 'bootstrap/dist/css/bootstrap.min.css'
// Bootstrap JS
import 'bootstrap/dist/js/bootstrap.bundle.min.js'
// Font Awesome
import '@fortawesome/fontawesome-free/css/all.min.css'

const app = createApp(App)
const pinia = createPinia()

app.use(pinia)
app.use(router)

// Export axios for global use
import axios from './main'
app.config.globalProperties.$axios = axios

app.mount('#app')