<template>
  <AdminLayout pageTitle="Pengaturan Landing Page" pageIcon="fa-laptop-house">
    <!-- Toast Notification -->
    <div v-if="toast.show" class="position-fixed top-0 end-0 p-3" style="z-index: 9999;">
      <div class="toast show" :class="'bg-' + (toast.type === 'danger' ? 'danger' : 'success')" role="alert">
        <div class="d-flex align-items-center text-white px-3 py-2">
          <i :class="toast.type === 'danger' ? 'fas fa-exclamation-circle' : 'fas fa-check-circle'" class="me-2"></i>
          <span class="me-auto">{{ toast.message }}</span>
          <button type="button" class="btn-close btn-close-white ms-2" @click="toast.show = false"></button>
        </div>
      </div>
    </div>
    <div class="row">
      <!-- Hero Settings -->
      <div class="col-12 mb-4">
        <div class="card shadow-sm border-0">
          <div class="card-header bg-white py-3">
            <h5 class="mb-0"><i class="fas fa-home text-primary me-2"></i>Konten Hero</h5>
          </div>
          <div class="card-body">
            <form @submit.prevent="saveHero">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Judul Utama</label>
                  <input type="text" class="form-control" v-model="heroForm.title" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Sub Judul</label>
                  <input type="text" class="form-control" v-model="heroForm.subtitle" required>
                </div>
                <div class="col-12 mb-3">
                  <label class="form-label">Deskripsi</label>
                  <textarea class="form-control" v-model="heroForm.description" rows="3" required></textarea>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Teks Tombol (CTA)</label>
                  <input type="text" class="form-control" v-model="heroForm.cta_text" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Link Tombol (CTA)</label>
                  <input type="text" class="form-control" v-model="heroForm.cta_link" required>
                </div>
                <div class="col-12 mb-3">
                  <label class="form-label">Upload Gambar Latar (Hero Image)</label>
                  <input type="file" class="form-control" @change="uploadHeroImage" :disabled="uploadingHero">
                  <div v-if="uploadingHero" class="text-primary mt-1"><i class="fas fa-spinner fa-spin me-1"></i>Mengupload gambar...</div>
                  <div v-else class="form-text">Biarkan kosong jika tidak ingin mengubah gambar latar.</div>
                </div>
              </div>
              <div class="text-end">
                <button type="submit" class="btn btn-primary" :disabled="savingHero">
                  <i class="fas fa-save me-1"></i> {{ savingHero ? 'Menyimpan...' : 'Simpan Perubahan Hero' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- Team Settings -->
      <div class="col-12 mb-4">
        <div class="card shadow-sm border-0">
          <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0"><i class="fas fa-users text-primary me-2"></i>Tim SPMI</h5>
            <button class="btn btn-primary btn-sm" @click="openTeamModal()">
              <i class="fas fa-plus me-1"></i> Tambah Anggota
            </button>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover align-middle">
                <thead>
                  <tr>
                    <th>Foto</th>
                    <th>Nama</th>
                    <th>Jabatan</th>
                    <th>Urutan</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-if="loading.team">
                    <td colspan="6" class="text-center py-4">Memuat data...</td>
                  </tr>
                  <tr v-else-if="team.length === 0">
                    <td colspan="6" class="text-center py-4 text-muted">Belum ada anggota tim</td>
                  </tr>
                  <tr v-for="member in team" :key="member.id">
                    <td>
                      <img 
                        :src="member.image_url || '/images/photos/default-avatar.png'" 
                        class="rounded-circle border" 
                        style="width: 40px; height: 40px; object-fit: cover;"
                        @error="handleAvatarError"
                      >
                    </td>
                    <td>{{ member.name }}</td>
                    <td>{{ member.position }}</td>
                    <td>{{ member.order }}</td>
                    <td>
                      <span class="badge" :class="member.is_active ? 'bg-success' : 'bg-secondary'">
                        {{ member.is_active ? 'Aktif' : 'Non-aktif' }}
                      </span>
                    </td>
                    <td class="text-end">
                      <button class="btn btn-sm btn-outline-primary me-1" @click="openTeamModal(member)">
                        <i class="fas fa-edit"></i>
                      </button>
                      <button class="btn btn-sm btn-outline-danger" @click="deleteTeamMember(member.id)">
                        <i class="fas fa-trash"></i>
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Documentation Settings -->
      <div class="col-12">
        <div class="card shadow-sm border-0">
          <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0"><i class="fas fa-camera text-primary me-2"></i>Dokumentasi Kegiatan</h5>
            <button class="btn btn-primary btn-sm" @click="openDocModal()">
              <i class="fas fa-plus me-1"></i> Tambah Dokumentasi
            </button>
          </div>
          <div class="card-body">
            <div class="row g-3">
              <div v-if="loading.docs" class="col-12 text-center py-4">Memuat data...</div>
              <div v-else-if="documentations.length === 0" class="col-12 text-center py-4 text-muted">Belum ada dokumentasi</div>
              <div v-for="doc in documentations" :key="doc.id" class="col-md-4 col-sm-6">
                <div class="card h-100 border">
                  <img 
                    :src="doc.image_url" 
                    class="card-img-top" 
                    style="height: 150px; object-fit: cover;"
                    @error="handleDocImageError"
                  >
                  <div class="card-body">
                    <h6 class="card-title text-truncate">{{ doc.title }}</h6>
                    <p class="card-text small text-muted text-truncate">{{ doc.description }}</p>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                      <span class="badge" :class="doc.is_active ? 'bg-success' : 'bg-secondary'">
                        {{ doc.is_active ? 'Aktif' : 'Non-aktif' }}
                      </span>
                      <div>
                        <button class="btn btn-sm btn-outline-primary me-1" @click="openDocModal(doc)">
                          <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" @click="deleteDocumentation(doc.id)">
                          <i class="fas fa-trash"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Team Modal -->
    <div class="modal fade" id="teamModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ isEditingTeam ? 'Edit' : 'Tambah' }} Anggota Tim</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="saveTeamMember">
              <div class="mb-3">
                <label class="form-label">Nama</label>
                <input type="text" class="form-control" v-model="teamForm.name" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Jabatan</label>
                <input type="text" class="form-control" v-model="teamForm.position" required>
              </div>
              <div class="mb-3">
                <label class="form-label">URL Foto (opsional)</label>
                <input type="text" class="form-control" v-model="teamForm.image_url">
                <div class="form-text">Atau upload gambar di bawah</div>
              </div>
              <div class="mb-3">
                <label class="form-label">Upload Foto</label>
                <input type="file" class="form-control" @change="uploadImage($event, 'team')" :disabled="uploadingImage">
                <div v-if="uploadingImage" class="text-primary mt-1"><i class="fas fa-spinner fa-spin me-1"></i>Mengupload...</div>
              </div>
              <div class="row">
                <div class="col-6 mb-3">
                  <label class="form-label">Urutan</label>
                  <input type="number" class="form-control" v-model="teamForm.order">
                </div>
                <div class="col-6 mb-3">
                  <label class="form-label">Status</label>
                  <select class="form-select" v-model="teamForm.is_active">
                    <option :value="true">Aktif</option>
                    <option :value="false">Non-aktif</option>
                  </select>
                </div>
              </div>
              <div class="text-end mt-3">
                <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary" :disabled="saving">
                  {{ saving ? 'Menyimpan...' : 'Simpan' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Doc Modal -->
    <div class="modal fade" id="docModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ isEditingDoc ? 'Edit' : 'Tambah' }} Dokumentasi</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="saveDocumentation">
              <div class="mb-3">
                <label class="form-label">Judul</label>
                <input type="text" class="form-control" v-model="docForm.title" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Deskripsi</label>
                <textarea class="form-control" v-model="docForm.description" rows="2"></textarea>
              </div>
              <div class="mb-3">
                <label class="form-label">Tanggal Kegiatan</label>
                <input type="date" class="form-control" v-model="docForm.activity_date">
              </div>
              <div class="mb-3">
                <label class="form-label">URL Gambar</label>
                <input type="text" class="form-control" v-model="docForm.image_url" required>
                <div class="form-text">Atau upload gambar di bawah</div>
              </div>
              <div class="mb-3">
                <label class="form-label">Upload Gambar</label>
                <input type="file" class="form-control" @change="uploadImage($event, 'doc')" :disabled="uploadingImage">
                <div v-if="uploadingImage" class="text-primary mt-1"><i class="fas fa-spinner fa-spin me-1"></i>Mengupload...</div>
              </div>
              <div class="row">
                <div class="col-6 mb-3">
                  <label class="form-label">Urutan</label>
                  <input type="number" class="form-control" v-model="docForm.order">
                </div>
                <div class="col-6 mb-3">
                  <label class="form-label">Status</label>
                  <select class="form-select" v-model="docForm.is_active">
                    <option :value="true">Aktif</option>
                    <option :value="false">Non-aktif</option>
                  </select>
                </div>
              </div>
              <div class="text-end mt-3">
                <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary" :disabled="saving">
                  {{ saving ? 'Menyimpan...' : 'Simpan' }}
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
import axios from '@/main'
import { useLandingStore } from '@/stores/landingStore'
import { Modal } from 'bootstrap'

export default {
  name: 'LandingPageSettings',
  components: {
    AdminLayout
  },
  setup() {
    const store = useLandingStore()
    return { store }
  },
  data() {
    return {
      team: [],
      documentations: [],
      loading: {
        team: false,
        docs: false
      },
      saving: false,
      savingHero: false,
      toast: {
        show: false,
        message: '',
        type: 'success'
      },
      uploadingHero: false,
      uploadingImage: false,
      
      heroForm: {
        title: '',
        subtitle: '',
        description: '',
        cta_text: '',
        cta_link: ''
      },
      
      // Team Modal
      teamModal: null,
      isEditingTeam: false,
      teamForm: {
        id: null,
        name: '',
        position: '',
        image_url: '',
        order: 0,
        is_active: true
      },

      // Doc Modal
      docModal: null,
      isEditingDoc: false,
      docForm: {
        id: null,
        title: '',
        description: '',
        image_url: '',
        activity_date: '',
        order: 0,
        is_active: true
      }
    }
  },
  mounted() {
    this.fetchTeam()
    this.fetchDocs()
    this.loadHeroData()

    this.$nextTick(() => {
      this.teamModal = new Modal(document.getElementById('teamModal'))
      this.docModal = new Modal(document.getElementById('docModal'))
    })
  },
  methods: {
    showToast(message, type = 'success') {
      this.toast = { show: true, message, type }
      setTimeout(() => { this.toast.show = false }, 3000)
    },
    handleAvatarError(event) {
      event.target.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 24 24" fill="none" stroke="%23cccccc" stroke-width="2"%3E%3Ccircle cx="12" cy="8" r="4"%3E%3C/circle%3E%3Cpath d="M12 14c-6.1 0-8 4-8 4v2h16v-2s-1.9-4-8-4z"%3E%3C/path%3E%3C/svg%3E'
    },
    handleDocImageError(event) {
      event.target.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 24 24" fill="none" stroke="%23996600" stroke-width="2"%3E%3Crect x="3" y="3" width="18" height="18" rx="2"%3E%3C/rect%3E%3Ccircle cx="8.5" cy="8.5" r="1.5"%3E%3C/circle%3E%3Cpath d="M21 15l-5-5L5 21"%3E%3C/path%3E%3C/svg%3E'
    },

    // --- Hero ---
    async loadHeroData() {
      await this.store.fetchHeroContent()
      this.heroForm = { ...this.store.heroContent }
    },
    async saveHero() {
      this.savingHero = true
      try {
        await this.store.updateHeroContent(this.heroForm)
        this.showToast('Data Hero berhasil disimpan!')
      } catch (error) {
        this.showToast('Gagal menyimpan data Hero', 'danger')
      } finally {
        this.savingHero = false
      }
    },
    async uploadHeroImage(event) {
      const file = event.target.files[0]
      if (!file) return

      this.uploadingHero = true
      const formData = new FormData()
      formData.append('image', file)

      try {
        const response = await axios.post('/hero/image', formData, {
          headers: { 'Content-Type': 'multipart/form-data' }
        })
        if (response.data.success) {
          this.showToast('Gambar latar hero berhasil diupload!')
          this.loadHeroData()
        }
      } catch (error) {
        console.error('Upload hero image error:', error)
        this.showToast('Gagal upload gambar latar hero', 'danger')
      } finally {
        this.uploadingHero = false
      }
    },

    // --- Team ---
    async fetchTeam() {
      this.loading.team = true
      try {
        const response = await axios.get('/landing/team')
        if (response.data.success) {
          this.team = response.data.data
        }
      } catch (error) {
        console.error('Fetch team error:', error)
      } finally {
        this.loading.team = false
      }
    },
    openTeamModal(member = null) {
      if (member) {
        this.isEditingTeam = true
        this.teamForm = { ...member }
      } else {
        this.isEditingTeam = false
        this.teamForm = {
          id: null,
          name: '',
          position: '',
          image_url: '',
          order: this.team.length,
          is_active: true
        }
      }
      this.teamModal?.show()
    },
    async saveTeamMember() {
      this.saving = true
      try {
        const url = this.isEditingTeam ? `/landing/team/${this.teamForm.id}` : '/landing/team'
        const method = this.isEditingTeam ? 'put' : 'post'
        
        const response = await axios[method](url, this.teamForm)
        if (response.data.success) {
          this.fetchTeam()
          this.teamModal?.hide()
        }
      } catch (error) {
        console.error('Save team error:', error)
        this.showToast('Gagal menyimpan data anggota tim', 'danger')
      } finally {
        this.saving = false
      }
    },
    async deleteTeamMember(id) {
      if (confirm('Yakin ingin menghapus anggota tim ini?')) {
        try {
          await axios.delete(`/landing/team/${id}`)
          this.fetchTeam()
        } catch (error) {
          console.error('Delete team error:', error)
        }
      }
    },

    // --- Documentations ---
    async fetchDocs() {
      this.loading.docs = true
      try {
        const response = await axios.get('/landing/docs')
        if (response.data.success) {
          this.documentations = response.data.data
        }
      } catch (error) {
        console.error('Fetch docs error:', error)
      } finally {
        this.loading.docs = false
      }
    },
    openDocModal(doc = null) {
      if (doc) {
        this.isEditingDoc = true
        this.docForm = { ...doc }
        // Format date for input type="date"
        if (this.docForm.activity_date) {
          this.docForm.activity_date = this.docForm.activity_date.split('T')[0]
        }
      } else {
        this.isEditingDoc = false
        this.docForm = {
          id: null,
          title: '',
          description: '',
          image_url: '',
          activity_date: '',
          order: this.documentations.length,
          is_active: true
        }
      }
      this.docModal?.show()
    },
    async saveDocumentation() {
      this.saving = true
      try {
        const url = this.isEditingDoc ? `/landing/docs/${this.docForm.id}` : '/landing/docs'
        const method = this.isEditingDoc ? 'put' : 'post'
        
        const response = await axios[method](url, this.docForm)
        if (response.data.success) {
          this.fetchDocs()
          this.docModal?.hide()
        }
      } catch (error) {
        console.error('Save doc error:', error)
        this.showToast('Gagal menyimpan data dokumentasi', 'danger')
      } finally {
        this.saving = false
      }
    },
    async deleteDocumentation(id) {
      if (confirm('Yakin ingin menghapus dokumentasi ini?')) {
        try {
          await axios.delete(`/landing/docs/${id}`)
          this.fetchDocs()
        } catch (error) {
          console.error('Delete doc error:', error)
        }
      }
    },

    // --- Upload ---
    async uploadImage(event, target) {
      const file = event.target.files[0]
      if (!file) return

      this.uploadingImage = true
      const formData = new FormData()
      formData.append('image', file)

      try {
        const response = await axios.post('/landing/image', formData, {
          headers: { 'Content-Type': 'multipart/form-data' }
        })
        if (response.data.success) {
          if (target === 'team') {
            this.teamForm.image_url = response.data.url
          } else {
            this.docForm.image_url = response.data.url
          }
          this.showToast('Gambar berhasil diupload!')
        }
      } catch (error) {
        console.error('Upload error:', error)
        this.showToast('Gagal upload gambar', 'danger')
      } finally {
        this.uploadingImage = false
      }
    }
  }
}
</script>
