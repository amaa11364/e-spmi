// src/stores/landingStore.js
import { defineStore } from 'pinia'

export const useLandingStore = defineStore('landing', {
  state: () => ({
    heroContent: {
      id: 1,
      title: 'Sistem Penjaminan Mutu Internal Digital',
      subtitle: 'Transformasi Digital SPMI IKIP',
      description: 'Platform terintegrasi untuk memonitor, mengelola, dan meningkatkan mutu pendidikan secara digital.',
      cta_text: 'Mulai Sekarang',
      cta_link: '#features',
      background_image: null
    },
    beritas: [
      {
        id: 1,
        judul: 'SPMI Luncurkan Aplikasi Mobile untuk Monitoring Mutu',
        link: '#',
        gambar_url: '/images/berita-1.jpg',
        created_at: '2024-01-15T10:30:00'
      },
      {
        id: 2,
        judul: 'Audit Internal SPMI Semester Ganjil 2024/2025',
        link: '#',
        gambar_url: '/images/berita-2.jpg',
        created_at: '2024-01-10T14:20:00'
      }
    ],
    jadwals: [
      {
        id: 1,
        hari: '15',
        bulanSingkat: 'Jan',
        kegiatan: 'Rapat Koordinasi SPMI',
        waktu: '2024-01-15T09:00:00',
        tempat: 'Ruang Rapat Utama'
      },
      {
        id: 2,
        hari: '20',
        bulanSingkat: 'Jan',
        kegiatan: 'Workshop Penyusunan Dokumen Mutu',
        waktu: '2024-01-20T13:00:00',
        tempat: 'Aula Kampus'
      }
    ]
  }),
  actions: {
    async fetchHeroContent() {
      // API call untuk mengambil data hero
      // const response = await axios.get('/api/hero')
      // this.heroContent = response.data
    },
    async fetchBeritas() {
      // API call untuk mengambil data berita
      // const response = await axios.get('/api/beritas')
      // this.beritas = response.data
    },
    async fetchJadwals() {
      // API call untuk mengambil data jadwal
      // const response = await axios.get('/api/jadwals')
      // this.jadwals = response.data
    },
    async updateHeroContent(data) {
      // API call untuk update hero content
      // const response = await axios.put('/api/hero', data)
      // this.heroContent = response.data
    }
  },
  getters: {
    latestBeritas: (state) => {
      return state.beritas.slice(0, 4)
    },
    upcomingJadwals: (state) => {
      return state.jadwals.slice(0, 5)
    }
  }
})