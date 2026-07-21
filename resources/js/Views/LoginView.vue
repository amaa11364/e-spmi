<!-- src/Views/LoginView.vue -->
<template>
  <div class="login-container">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-12 col-sm-10 col-md-8 col-lg-5">
          <div class="login-card">
            <!-- Logo Section - CENTERED -->
            <div class="logo-section">
              <div class="logo-container">
                <img
                  :src="'/images/photos/logo-ikipsiliwangi.png'"
                  alt="Logo SPMI"
                  class="logo-img"
                  @error="handleImageError"
                >
              </div>
              <div class="brand-text">
                <div class="brand-name">SPMI</div>
                <div class="brand-subtitle">Sistem Penjaminan Mutu Internal</div>
              </div>
            </div>
            
            <!-- Login Form -->
            <div class="p-4">
              <form @submit.prevent="handleLogin">
                <!-- Alert Error -->
                <div v-if="errorMessage" class="alert alert-danger alert-dismissible fade show" role="alert">
                  <i class="fas fa-exclamation-circle me-2"></i>{{ errorMessage }}
                  <button type="button" class="btn-close" @click="errorMessage = null"></button>
                </div>

                <!-- Alert Success -->
                <div v-if="successMessage" class="alert alert-success alert-dismissible fade show" role="alert">
                  <i class="fas fa-check-circle me-2"></i>{{ successMessage }}
                  <button type="button" class="btn-close" @click="successMessage = null"></button>
                </div>

                <div class="mb-3">
                  <label for="email" class="form-label">Email</label>
                  <div class="input-group">
                    <span class="input-group-text">
                      <i class="fas fa-envelope"></i>
                    </span>
                    <input 
                      id="email" 
                      type="email" 
                      class="form-control" 
                      :class="{ 'is-invalid': errors.email }"
                      v-model="form.email" 
                      required 
                      autocomplete="email" 
                      autofocus
                      placeholder="Masukkan email"
                    >
                  </div>
                  <div v-if="errors.email" class="text-danger small mt-1">{{ errors.email[0] }}</div>
                </div>

                <div class="mb-3">
                  <label for="password" class="form-label">Password</label>
                  <div class="input-group">
                    <span class="input-group-text">
                      <i class="fas fa-lock"></i>
                    </span>
                    <input 
                      id="password" 
                      :type="showPassword ? 'text' : 'password'" 
                      class="form-control" 
                      :class="{ 'is-invalid': errors.password }"
                      v-model="form.password" 
                      required 
                      autocomplete="current-password"
                      placeholder="Masukkan password"
                    >
                    <button 
                      type="button" 
                      class="btn btn-outline-secondary" 
                      @click="togglePassword"
                      style="border-radius: 0 0.375rem 0.375rem 0;"
                    >
                      <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                    </button>
                  </div>
                  <div v-if="errors.password" class="text-danger small mt-1">{{ errors.password[0] }}</div>
                </div>

                <div class="mb-3 form-check">
                  <input 
                    class="form-check-input" 
                    type="checkbox" 
                    v-model="form.remember" 
                    id="remember"
                  >
                  <label class="form-check-label" for="remember">Ingat saya</label>
                </div>

                <button 
                  type="submit" 
                  class="btn btn-primary w-100 mb-3"
                  :disabled="loading"
                >
                  <span v-if="loading" class="spinner-border spinner-border-sm me-2" role="status"></span>
                  <i v-else class="fas fa-sign-in-alt me-2"></i>
                  {{ loading ? 'Memproses...' : 'Masuk' }}
                </button>

                <div class="text-center">
                  <router-link to="/" class="text-decoration-none text-muted">
                    <i class="fas fa-arrow-left me-1"></i>Kembali ke Beranda
                  </router-link>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import axios from 'axios'

export default {
  name: 'LoginView',
  setup() {
    const router = useRouter()
    const route = useRoute()
    
    const form = reactive({
      email: '',
      password: '',
      remember: false
    })
    
    const errors = ref({})
    const errorMessage = ref(null)
    const successMessage = ref(null)
    const loading = ref(false)
    const showPassword = ref(false)

    // Redirect URL dari query params atau sessionStorage
    const redirectPath = ref('/dashboard')

    onMounted(() => {
      // Cek apakah user sudah login
      const token = localStorage.getItem('token')
      if (token) {
        router.push('/dashboard')
        return
      }

      // Ambil redirect URL dari query params
      if (route.query.redirect) {
        redirectPath.value = route.query.redirect
      } else {
        // Coba ambil dari sessionStorage
        const savedRedirect = sessionStorage.getItem('login_redirect')
        if (savedRedirect) {
          redirectPath.value = savedRedirect
          sessionStorage.removeItem('login_redirect')
        }
      }
    })

    const togglePassword = () => {
      showPassword.value = !showPassword.value
    }

    const handleImageError = (event) => {
      event.target.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100"%3E%3Crect width="100" height="100" rx="20" fill="%23996600"/%3E%3Ctext x="50" y="65" text-anchor="middle" fill="white" font-size="40" font-weight="bold"%3ES%3C/text%3E%3C/svg%3E'
    }

    const handleLogin = async () => {
      // Reset errors
      errors.value = {}
      errorMessage.value = null
      loading.value = true

      try {
        const response = await axios.post('/login', {
          email: form.email,
          password: form.password
        })

        if (response.data.success) {
          // Simpan token dan user data
          localStorage.setItem('token', response.data.data.token)
          localStorage.setItem('user', JSON.stringify(response.data.data.user))
          
          // Set header Authorization untuk request selanjutnya
          axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.data.token}`

          successMessage.value = 'Login berhasil! Mengalihkan...'

          // Redirect ke halaman tujuan
          setTimeout(() => {
            router.push(redirectPath.value)
          }, 1000)
        }
      } catch (error) {
        if (error.response) {
          if (error.response.status === 422) {
            // Validation errors
            errors.value = error.response.data.errors || {}
          } else if (error.response.status === 401) {
            errorMessage.value = 'Email atau password salah. Silakan coba lagi.'
          } else {
            errorMessage.value = error.response.data.message || 'Terjadi kesalahan. Silakan coba lagi.'
          }
        } else {
          errorMessage.value = 'Terjadi kesalahan koneksi. Silakan coba lagi.'
        }
      } finally {
        loading.value = false
      }
    }

    return {
      form,
      errors,
      errorMessage,
      successMessage,
      loading,
      showPassword,
      handleLogin,
      togglePassword,
      handleImageError
    }
  }
}
</script>

<style scoped>
.login-container {
  background: linear-gradient(135deg, #c79634ff 0%, #8b6925ff 100%);
  min-height: 100vh;
  display: flex;
  align-items: center;
  padding: 2rem 0;
}

.login-card {
  background: white;
  border-radius: 20px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
  overflow: hidden;
  transition: all 0.3s ease;
}

.login-card:hover {
  box-shadow: 0 25px 70px rgba(0, 0, 0, 0.2);
}

/* ===== LOGO SECTION ===== */
.logo-section {
  padding: 2.5rem 2rem 1rem 2rem;
  text-align: center;
  background: linear-gradient(180deg, #fff9e6 0%, #ffffff 100%);
  border-bottom: 1px solid rgba(153, 102, 0, 0.1);
}

.logo-container {
  display: flex;
  justify-content: center;
  align-items: center;
  margin-bottom: 1rem;
}

.logo-img {
  width: 100px;
  height: 100px;
  object-fit: contain;
  border-radius: 20px;
  transition: all 0.3s ease;
  filter: drop-shadow(0 4px 12px rgba(153, 102, 0, 0.15));
}

.logo-img:hover {
  transform: scale(1.05) rotate(-2deg);
  filter: drop-shadow(0 6px 20px rgba(153, 102, 0, 0.25));
}

.brand-text {
  margin-bottom: 0.5rem;
}

.brand-name {
  font-size: 1.75rem;
  font-weight: 800;
  color: #996600;
  margin-bottom: 0.25rem;
  letter-spacing: -0.5px;
}

.brand-name::after {
  content: '';
  display: block;
  width: 50px;
  height: 3px;
  background: linear-gradient(90deg, #996600, #cc9900);
  margin: 0.5rem auto 0;
  border-radius: 2px;
}

.brand-subtitle {
  font-size: 0.85rem;
  color: #6c757d;
  margin-bottom: 0;
  font-weight: 500;
  letter-spacing: 0.3px;
}

/* ===== FORM ===== */
.p-4 {
  padding: 2rem !important;
}

.form-label {
  font-weight: 600;
  color: #2c3e50;
  font-size: 0.9rem;
  margin-bottom: 0.5rem;
}

.input-group {
  border-radius: 10px;
  overflow: hidden;
}

.input-group-text {
  background: #f8f9fa;
  color: #996600;
  border: 1px solid #e9ecef;
  border-right: none;
  padding: 0 15px;
}

.form-control {
  border: 1px solid #e9ecef;
  padding: 10px 15px;
  font-size: 0.95rem;
  transition: all 0.3s ease;
}

.form-control:focus {
  border-color: #996600;
  box-shadow: 0 0 0 0.2rem rgba(153, 102, 0, 0.15);
}

.form-control.is-invalid {
  border-color: #dc3545;
}

.form-control.is-invalid:focus {
  box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.15);
}

.btn-outline-secondary {
  border-color: #e9ecef;
  border-left: none;
  background: white;
  color: #6c757d;
}

.btn-outline-secondary:hover {
  background: #f8f9fa;
  border-color: #e9ecef;
  color: #996600;
}

/* ===== BUTTON ===== */
.btn-primary {
  background: linear-gradient(135deg, #996600, #7a5200);
  border: none;
  padding: 12px;
  font-weight: 600;
  font-size: 1rem;
  border-radius: 10px;
  transition: all 0.3s ease;
  letter-spacing: 0.5px;
}

.btn-primary:hover:not(:disabled) {
  background: linear-gradient(135deg, #7a5200, #5a3a00);
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(153, 102, 0, 0.35);
}

.btn-primary:disabled {
  opacity: 0.7;
  cursor: not-allowed;
  transform: none;
}

/* ===== CHECKBOX ===== */
.form-check-input:checked {
  background-color: #996600;
  border-color: #996600;
}

.form-check-input:focus {
  border-color: #996600;
  box-shadow: 0 0 0 0.2rem rgba(153, 102, 0, 0.15);
}

.form-check-label {
  color: #495057;
  font-weight: 500;
}

/* ===== ALERT ===== */
.alert {
  border-radius: 10px;
  border: none;
  font-weight: 500;
}

.alert-danger {
  background: #fef2f2;
  color: #dc2626;
}

.alert-success {
  background: #f0fdf4;
  color: #16a34a;
}

.alert .btn-close {
  padding: 0.75rem;
}

/* ===== RESPONSIVE ===== */
@media (max-width: 576px) {
  .login-container {
    padding: 1rem 0;
  }
  
  .logo-section {
    padding: 1.5rem 1rem 0.5rem 1rem;
  }
  
  .p-4 {
    padding: 1.25rem !important;
  }
  
  .brand-name {
    font-size: 1.4rem;
  }
  
  .brand-subtitle {
    font-size: 0.75rem;
  }

  .logo-img {
    width: 75px;
    height: 75px;
  }
  
  .login-card {
    border-radius: 15px;
  }
}

@media (min-width: 768px) and (max-width: 992px) {
  .logo-img {
    width: 90px;
    height: 90px;
  }
}

/* ===== ANIMATION ===== */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.login-card {
  animation: fadeInUp 0.5s ease-out;
}

/* ===== FALLBACK LOGO ===== */
.logo-img[src*="data:image"] {
  background: #996600;
  padding: 10px;
}
</style>