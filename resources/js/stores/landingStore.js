// src/stores/landingStore.js
import { defineStore } from 'pinia'
import axios from '../main'

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
    beritas: [],
    jadwals: [],
    loading: false,
    error: null
  }),
  actions: {
    async fetchHeroContent() {
      try {
        const response = await axios.get('/hero')
        if (response.data.success) {
          this.heroContent = response.data.data
        }
      } catch (error) {
        console.error('Failed to fetch hero content:', error)
      }
    },
    async fetchBeritas() {
      try {
        const response = await axios.get('/beritas/latest')
        if (response.data.success) {
          this.beritas = response.data.data
        }
      } catch (error) {
        console.error('Failed to fetch beritas:', error)
      }
    },
    async fetchJadwals() {
      try {
        const response = await axios.get('/jadwals/upcoming')
        if (response.data.success) {
          this.jadwals = response.data.data
        }
      } catch (error) {
        console.error('Failed to fetch jadwals:', error)
      }
    },
    async updateHeroContent(data) {
      try {
        const response = await axios.put('/hero', data)
        if (response.data.success) {
          this.heroContent = response.data.data
        }
      } catch (error) {
        console.error('Failed to update hero content:', error)
        throw error
      }
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