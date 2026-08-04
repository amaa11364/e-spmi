<template>
  <AdminLayout pageTitle="Tambah Berita Baru" pageIcon="fa-newspaper">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-3">
          <div class="card-body p-4">
            <form @submit.prevent="submitForm" enctype="multipart/form-data">
              <!-- Judul Berita -->
              <div class="mb-3">
                <label class="form-label fw-semibold">Judul Berita <span class="text-danger">*</span></label>
                <input 
                  type="text" 
                  class="form-control" 
                  v-model="form.judul" 
                  :class="{ 'is-invalid': errors.judul }"
                  placeholder="Masukkan judul berita..."
                  required 
                />
                <div class="invalid-feedback" v-if="errors.judul">{{ errors.judul[0] }}</div>
              </div>

              <!-- Deskripsi -->
              <div class="mb-3">
                <label class="form-label fw-semibold">Deskripsi / Konten <span class="text-danger">*</span></label>
                <textarea 
                  class="form-control" 
                  rows="5" 
                  v-model="form.deskripsi" 
                  :class="{ 'is-invalid': errors.deskripsi }"
                  placeholder="Tuliskan deskripsi ringkas berita..."
                  required
                ></textarea>
                <div class="invalid-feedback" v-if="errors.deskripsi">{{ errors.deskripsi[0] }}</div>
              </div>

              <!-- External Link -->
              <div class="mb-3">
                <label class="form-label fw-semibold">Link / Tautan Luar (Opsional)</label>
                <input 
                  type="url" 
                  class="form-control" 
                  v-model="form.link" 
                  :class="{ 'is-invalid': errors.link }"
                  placeholder="https://example.com/berita-lengkap" 
                />
                <div class="invalid-feedback" v-if="errors.link">{{ errors.link[0] }}</div>
              </div>

              <!-- File Upload Gambar -->
              <div class="mb-3">
                <label class="form-label fw-semibold">Gambar Utama</label>
                <input 
                  type="file" 
                  class="form-control" 
                  @change="handleImageUpload" 
                  accept="image/png, image/jpeg, image/jpg, image/webp"
                  :class="{ 'is-invalid': errors.gambar }"
                />
                <div class="form-text">Format yang diperbolehkan: JPG, PNG, WEBP (Maksimal 5MB).</div>
                <div class="invalid-feedback" v-if="errors.gambar">{{ errors.gambar[0] }}</div>
                
                <!-- Preview Gambar -->
                <div v-if="imagePreview" class="mt-3">
                  <p class="small text-muted mb-1">Preview Gambar:</p>
                  <img :src="imagePreview" class="img-thumbnail" style="max-height: 180px;" alt="Preview" />
                </div>
              </div>

              <!-- Publish Status Checkbox -->
              <div class="form-check mb-4">
                <input 
                  class="form-check-input" 
                  type="checkbox" 
                  id="isPublished" 
                  v-model="form.is_published" 
                />
                <label class="form-check-label" for="isPublished">
                  Langsung Publikasikan Berita
                </label>
              </div>

              <!-- Submit Buttons -->
              <div class="d-flex justify-content-end gap-2">
                <router-link to="/pengelola/berita" class="btn btn-light">Batal</router-link>
                <button type="submit" class="btn btn-warning text-white" :disabled="submitting">
                  <span v-if="submitting" class="spinner-border spinner-border-sm me-1"></span>
                  <i v-else class="fas fa-save me-1"></i> Simpan Berita
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script>
import AdminLayout from '@/components/AdminLayout.vue'
import axios from 'axios'

export default {
  name: 'BeritaCreate',
  components: { AdminLayout },
  data() {
    return {
      form: {
        judul: '',
        deskripsi: '',
        link: '',
        is_published: true,
        gambar: null
      },
      imagePreview: null,
      errors: {},
      submitting: false
    }
  },
  methods: {
    handleImageUpload(event) {
      const file = event.target.files[0]
      if (file) {
        this.form.gambar = file
        this.imagePreview = URL.createObjectURL(file)
      } else {
        this.form.gambar = null
        this.imagePreview = null
      }
    },
    async submitForm() {
      this.submitting = true
      this.errors = {}

      const formData = new FormData()
      formData.append('judul', this.form.judul)
      formData.append('deskripsi', this.form.deskripsi)
      if (this.form.link) formData.append('link', this.form.link)
      formData.append('is_published', this.form.is_published ? '1' : '0')
      
      if (this.form.gambar) {
        formData.append('gambar', this.form.gambar)
      }

      try {
        const token = localStorage.getItem('token') || ''
        
        // PERBAIKAN DI SINI: Gunakan 'beritas' (bukan '/api/beritas')
        // Karena baseURL Axios biasanya sudah diset ke '/api'
        const response = await axios.post('beritas', formData, {
          headers: {
            'Content-Type': 'multipart/form-data',
            'Authorization': `Bearer ${token}`
          }
        })

        if (response.data.success) {
          this.$router.push({
            path: '/pengelola/berita',
            query: { flash: 'Berita berhasil ditambahkan!', type: 'success' }
          })
        }
      } catch (error) {
        if (error.response) {
          if (error.response.status === 422) {
            this.errors = error.response.data.errors || {}
          } else if (error.response.status === 413) {
            alert('Gagal: Ukuran gambar terlalu besar melampaui batas server.')
          } else {
            alert('Gagal menyimpan berita: ' + (error.response.data.message || 'Terjadi kesalahan server.'))
          }
        } else {
          alert('Terjadi kesalahan jaringan atau server tidak merespon.')
        }
      } finally {
        this.submitting = false
      }
    }
  }
}
</script>