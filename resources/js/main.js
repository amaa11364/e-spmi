// main.js - Hapus createApp, pindahkan ke app.js
import axios from 'axios'

// Konfigurasi Axios
axios.defaults.baseURL = '/api'
axios.defaults.headers.common['Accept'] = 'application/json'

// Interceptor untuk token
axios.interceptors.request.use(
  config => {
    const token = localStorage.getItem('token')
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    return config
  },
  error => {
    return Promise.reject(error)
  }
)

// Interceptor untuk response
axios.interceptors.response.use(
  response => response,
  error => {
    if (error.response?.status === 401) {
      localStorage.removeItem('token')
      localStorage.removeItem('user')
      delete axios.defaults.headers.common['Authorization']
      // Gunakan router untuk navigasi
      const router = window.__router__
      if (router) {
        router.push('/pengelola/login')
      } else {
        window.location.href = '/pengelola/login'
      }
    }
    return Promise.reject(error)
  }
)

export default axios