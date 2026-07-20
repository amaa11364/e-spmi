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
                  src="/logo-ikipsiliwangi.png" 
                  alt="Logo E-SPMI" 
                  class="logo-img"
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

    const handleLogin = async () => {
      // Reset errors
      errors.value = {}
      errorMessage.value = null
      loading.value = true

      try {
        const response = await axios.post('/api/login', {
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
      togglePassword
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
  border-radius: 15px;
  box-shadow: 0 20px 40px rgba(0,0,0,0.1);
  overflow: hidden;
}

.logo-section {
  padding: 2.5rem 2rem 1rem 2rem;
  text-align: center;
  background: white;
}

.logo-container {
  display: flex;
  justify-content: center;
  align-items: center;
  margin-bottom: 1rem;
  width: 100%;
}

.logo-img {
  width: 80px;
  height: 80px;
  object-fit: contain;
  filter: drop-shadow(0 4px 8px rgba(0,0,0,0.1));
  display: block;
  margin: 0 auto;
}

.brand-text {
  margin-bottom: 0.5rem;
}

.brand-name {
  font-size: 1.5rem;
  font-weight: 700;
  color: #996600;
  margin-bottom: 0.25rem;
}

.brand-subtitle {
  font-size: 0.9rem;
  color: #6c757d;
  margin-bottom: 0;
}

.btn-primary {
  background: linear-gradient(135deg, #996600, #7a5200);
  border: none;
  padding: 12px;
  transition: all 0.3s ease;
}

.btn-primary:hover:not(:disabled) {
  background: linear-gradient(135deg, #7a5200, #5a3a00);
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(153, 102, 0, 0.3);
}

.btn-primary:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.btn-outline-secondary {
  border-color: #ced4da;
  background: white;
}

.btn-outline-secondary:hover {
  background: #f8f9fa;
  border-color: #ced4da;
}

.form-control:focus {
  border-color: #996600;
  box-shadow: 0 0 0 0.2rem rgba(153, 102, 0, 0.25);
}

.input-group-text {
  background: #f8f9fa;
  color: #6c757d;
}

.form-check-input:checked {
  background-color: #996600;
  border-color: #996600;
}

.alert {
  border-radius: 10px;
}

/* Responsive */
@media (max-width: 576px) {
  .logo-section {
    padding: 1.5rem 1rem 0.5rem 1rem;
  }
  
  .p-4 {
    padding: 1.5rem !important;
  }
  
  .brand-name {
    font-size: 1.25rem;
  }

  .logo-img {
    width: 60px;
    height: 60px;
  }
}

/* Untuk logo yang lebih besar di desktop */
@media (min-width: 768px) {
  .logo-img {
    width: 100px;
    height: 100px;
  }
}
</style>