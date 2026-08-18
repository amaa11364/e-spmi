<template>
  <AdminLayout pageTitle="Kelola Jadwal" pageIcon="fa-calendar-alt">
    <div class="jadwal-management-container">
      
      <!-- Filter Bar -->
      <div class="filter-card shadow-sm mb-4">
        <div class="row g-3 align-items-end">
          <!-- Pencarian -->
          <div class="col-md-4">
            <label class="form-label text-muted small mb-1">Pencarian</label>
            <input 
              type="text" 
              class="form-control filter-input" 
              placeholder="Cari kegiatan, tempat..." 
              v-model="search"
              @keyup.enter="loadData(1)"
            />
          </div>

          <!-- Status Dropdown -->
          <div class="col-md-3">
            <label class="form-label text-muted small mb-1">Status Waktu</label>
            <select class="form-select filter-input" v-model="statusFilter" @change="loadData(1)">
              <option value="all">Semua Waktu</option>
              <option value="upcoming">Akan Datang</option>
              <option value="past">Telah Berlalu</option>
            </select>
          </div>

          <!-- Tombol Tambah -->
          <div class="col-md-5 text-end">
            <button class="btn btn-tambah-jadwal" @click="openModal('create')">
              <i class="fas fa-plus me-1"></i> Tambah Jadwal
            </button>
          </div>
        </div>
      </div>

      <!-- Main Data Table Card -->
      <div class="table-card shadow-sm">
        <div class="table-responsive">
          <table class="table align-middle custom-table mb-0">
            <thead>
              <tr>
                <th style="width: 25%;">KEGIATAN</th>
                <th style="width: 15%;">TANGGAL & WAKTU</th>
                <th style="width: 20%;">TEMPAT</th>
                <th style="width: 15%;">PENYELENGGARA</th>
                <th style="width: 10%;">STATUS</th>
                <th style="width: 15%; text-align: right;">AKSI</th>
              </tr>
            </thead>
            <tbody>
              <!-- Loading -->
              <tr v-if="loading">
                <td colspan="6" class="text-center py-4 text-muted">
                  <div class="spinner-border spinner-border-sm text-warning me-2"></div>
                  Memuat data jadwal...
                </td>
              </tr>

              <!-- Empty -->
              <tr v-else-if="jadwals.length === 0">
                <td colspan="6" class="text-center py-4 text-muted">Tidak ada data jadwal ditemukan.</td>
              </tr>

              <!-- Data Rows -->
              <tr v-else v-for="item in jadwals" :key="item.id">
                <td class="fw-semibold text-dark text-truncate" style="max-width: 250px;" :title="item.kegiatan">
                  {{ item.kegiatan }}
                  <div class="text-muted small text-truncate" style="max-width: 250px; font-weight: normal;">
                    {{ item.deskripsi || '-' }}
                  </div>
                </td>
                <td>
                  <div class="d-flex flex-column">
                    <span class="text-dark fw-medium"><i class="fas fa-calendar-day me-1 text-muted"></i> {{ item.tanggal_formatted }}</span>
                    <span class="text-muted small mt-1" v-if="item.waktu_mulai">
                      <i class="fas fa-clock me-1"></i> 
                      {{ item.waktu }} <span v-if="item.waktu_selesai_formatted">- {{ item.waktu_selesai_formatted }}</span>
                    </span>
                  </div>
                </td>
                <td class="text-muted small">
                  <i class="fas fa-map-marker-alt me-1 text-danger" v-if="item.tempat"></i> {{ item.tempat || '-' }}
                </td>
                <td class="text-muted small">
                  {{ item.penyelenggara || '-' }}
                </td>
                <td>
                  <span 
                    class="badge badge-status"
                    :class="item.is_active ? 'status-active' : 'status-inactive'"
                  >
                    {{ item.is_active ? 'Aktif' : 'Tidak Aktif' }}
                  </span>
                </td>
                <td class="text-end action-links">
                  <a href="#" class="text-warning me-3 text-decoration-none fw-semibold" @click.prevent="openModal('edit', item)">
                    Edit
                  </a>
                  <a href="#" class="text-danger text-decoration-none fw-semibold" @click.prevent="confirmDelete(item.id)">
                    Hapus
                  </a>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Table Footer / Pagination -->
        <div class="table-footer d-flex justify-content-between align-items-center p-3 border-top">
          <span class="text-muted small">
            Menampilkan {{ pagination.from || 0 }} sampai {{ pagination.to || 0 }} dari {{ pagination.total || 0 }} jadwal
          </span>
          <div v-if="pagination.last_page > 1" class="pagination-buttons">
            <button 
              class="btn btn-sm btn-light border me-1" 
              :disabled="pagination.current_page === 1"
              @click="loadData(pagination.current_page - 1)"
            >
              &lt;
            </button>
            <button 
              v-for="p in pagination.last_page" 
              :key="p"
              class="btn btn-sm me-1"
              :class="p === pagination.current_page ? 'btn-pagination-active' : 'btn-light border'"
              @click="loadData(p)"
            >
              {{ p }}
            </button>
            <button 
              class="btn btn-sm btn-light border" 
              :disabled="pagination.current_page === pagination.last_page"
              @click="loadData(pagination.current_page + 1)"
            >
              &gt;
            </button>
          </div>
        </div>
      </div>

    </div>

    <!-- Modal Form (Create/Edit) -->
    <div v-if="showModal" class="modal-backdrop fade show" style="background-color: rgba(0,0,0,0.5);"></div>
    <div v-if="showModal" class="modal fade show d-block" tabindex="-1" role="dialog">
      <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg rounded-4">
          <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
            <h5 class="modal-title fw-bold text-dark">
              {{ modalMode === 'create' ? 'Tambah Jadwal Baru' : 'Edit Jadwal' }}
            </h5>
            <button type="button" class="btn-close" @click="closeModal" aria-label="Close"></button>
          </div>
          <div class="modal-body px-4 py-4">
            <form @submit.prevent="submitForm">
              <div class="row g-3">
                
                <div class="col-md-12">
                  <label class="form-label fw-semibold">Nama Kegiatan <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" v-model="form.kegiatan" :class="{ 'is-invalid': errors.kegiatan }" required>
                  <div class="invalid-feedback" v-if="errors.kegiatan">{{ errors.kegiatan[0] }}</div>
                </div>

                <div class="col-md-12">
                  <label class="form-label fw-semibold">Deskripsi</label>
                  <textarea class="form-control" v-model="form.deskripsi" rows="3" :class="{ 'is-invalid': errors.deskripsi }"></textarea>
                  <div class="invalid-feedback" v-if="errors.deskripsi">{{ errors.deskripsi[0] }}</div>
                </div>

                <div class="col-md-4">
                  <label class="form-label fw-semibold">Tanggal <span class="text-danger">*</span></label>
                  <input type="date" class="form-control" v-model="form.tanggal" :class="{ 'is-invalid': errors.tanggal }" required>
                  <div class="invalid-feedback" v-if="errors.tanggal">{{ errors.tanggal[0] }}</div>
                </div>

                <div class="col-md-4">
                  <label class="form-label fw-semibold">Waktu Mulai</label>
                  <input type="time" class="form-control" v-model="form.waktu_mulai" :class="{ 'is-invalid': errors.waktu_mulai }">
                  <div class="invalid-feedback" v-if="errors.waktu_mulai">{{ errors.waktu_mulai[0] }}</div>
                </div>

                <div class="col-md-4">
                  <label class="form-label fw-semibold">Waktu Selesai</label>
                  <input type="time" class="form-control" v-model="form.waktu_selesai" :class="{ 'is-invalid': errors.waktu_selesai }">
                  <div class="invalid-feedback" v-if="errors.waktu_selesai">{{ errors.waktu_selesai[0] }}</div>
                </div>

                <div class="col-md-6">
                  <label class="form-label fw-semibold">Tempat</label>
                  <input type="text" class="form-control" v-model="form.tempat" :class="{ 'is-invalid': errors.tempat }">
                  <div class="invalid-feedback" v-if="errors.tempat">{{ errors.tempat[0] }}</div>
                </div>

                <div class="col-md-6">
                  <label class="form-label fw-semibold">Penyelenggara</label>
                  <input type="text" class="form-control" v-model="form.penyelenggara" :class="{ 'is-invalid': errors.penyelenggara }">
                  <div class="invalid-feedback" v-if="errors.penyelenggara">{{ errors.penyelenggara[0] }}</div>
                </div>

                <div class="col-md-12 mt-3">
                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="isActiveCheck" v-model="form.is_active">
                    <label class="form-check-label ms-2 fw-medium text-dark" for="isActiveCheck">Tampilkan Jadwal (Aktif)</label>
                  </div>
                </div>

              </div>
            </form>
          </div>
          <div class="modal-footer border-top-0 px-4 pb-4 pt-0">
            <button type="button" class="btn btn-light px-4" @click="closeModal">Batal</button>
            <button type="button" class="btn btn-warning text-white px-4" @click="submitForm" :disabled="submitting">
              <span v-if="submitting" class="spinner-border spinner-border-sm me-2"></span>
              <i v-else class="fas fa-save me-2"></i> Simpan
            </button>
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
  name: 'JadwalIndex',
  components: { AdminLayout },
  data() {
    return {
      jadwals: [],
      search: '',
      statusFilter: 'all',
      loading: false,
      pagination: {
        current_page: 1,
        last_page: 1,
        total: 0,
        from: 0,
        to: 0
      },
      
      // Modal State
      showModal: false,
      modalMode: 'create', // 'create' or 'edit'
      submitting: false,
      errors: {},
      form: {
        id: null,
        kegiatan: '',
        deskripsi: '',
        tanggal: '',
        waktu_mulai: '',
        waktu_selesai: '',
        tempat: '',
        penyelenggara: '',
        kategori: '',
        is_active: true
      }
    }
  },
  mounted() {
    this.loadData()
  },
  methods: {
    async loadData(page = 1) {
      this.loading = true
      try {
        const token = localStorage.getItem('token') || ''
        const response = await axios.get(`jadwals?page=${page}&search=${this.search}&status=${this.statusFilter}`, {
          headers: { 'Authorization': `Bearer ${token}` }
        })
        
        if (response.data.success) {
          const resData = response.data.data
          this.jadwals = resData.data
          this.pagination.current_page = resData.current_page
          this.pagination.last_page = resData.last_page
          this.pagination.total = resData.total
          this.pagination.from = resData.from
          this.pagination.to = resData.to
        }
      } catch (err) {
        console.error('Gagal mengambil data jadwal:', err)
      } finally {
        this.loading = false
      }
    },
    
    openModal(mode, item = null) {
      this.modalMode = mode
      this.errors = {}
      
      if (mode === 'edit' && item) {
        this.form = {
          id: item.id,
          kegiatan: item.kegiatan,
          deskripsi: item.deskripsi || '',
          tanggal: item.tanggal, // should be YYYY-MM-DD
          waktu_mulai: item.waktu_mulai ? item.waktu_mulai.substring(11, 16) : '',
          waktu_selesai: item.waktu_selesai ? item.waktu_selesai.substring(11, 16) : '',
          tempat: item.tempat || '',
          penyelenggara: item.penyelenggara || '',
          kategori: item.kategori || '',
          is_active: item.is_active == 1
        }
      } else {
        // Reset form for create
        this.form = {
          id: null,
          kegiatan: '',
          deskripsi: '',
          tanggal: new Date().toISOString().split('T')[0],
          waktu_mulai: '',
          waktu_selesai: '',
          tempat: '',
          penyelenggara: '',
          kategori: '',
          is_active: true
        }
      }
      this.showModal = true
      document.body.classList.add('modal-open')
    },
    
    closeModal() {
      this.showModal = false
      document.body.classList.remove('modal-open')
    },
    
    async submitForm() {
      this.submitting = true
      this.errors = {}
      
      try {
        const token = localStorage.getItem('token') || ''
        
        // Prepare payload (convert empty strings to null for time if needed, though backend handles it)
        const payload = { ...this.form }
        if (!payload.waktu_mulai) delete payload.waktu_mulai;
        if (!payload.waktu_selesai) delete payload.waktu_selesai;
        
        let response
        
        if (this.modalMode === 'create') {
          response = await axios.post('jadwals', payload, {
            headers: { 'Authorization': `Bearer ${token}` }
          })
        } else {
          response = await axios.put(`jadwals/${this.form.id}`, payload, {
            headers: { 'Authorization': `Bearer ${token}` }
          })
        }
        
        if (response.data.success) {
          this.closeModal()
          this.loadData(this.modalMode === 'create' ? 1 : this.pagination.current_page)
        }
      } catch (error) {
        if (error.response && error.response.status === 422) {
          this.errors = error.response.data.errors || {}
        } else {
          alert('Gagal menyimpan jadwal: ' + (error.response?.data?.message || 'Terjadi kesalahan'))
        }
      } finally {
        this.submitting = false
      }
    },
    
    async confirmDelete(id) {
      if (confirm('Apakah Anda yakin ingin menghapus jadwal ini?')) {
        try {
          const token = localStorage.getItem('token') || ''
          const response = await axios.delete(`jadwals/${id}`, {
            headers: { 'Authorization': `Bearer ${token}` }
          })
          if (response.data.success) {
            this.loadData(this.pagination.current_page)
          }
        } catch (err) {
          alert('Gagal menghapus jadwal')
        }
      }
    }
  }
}
</script>

<style scoped>
.jadwal-management-container {
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  color: #333;
}
.filter-card {
  background: white;
  border-radius: 8px;
  padding: 1.25rem;
  border: 1px solid #eaeaea;
}
.filter-input {
  background-color: #f1f3f5;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  font-size: 0.9rem;
  padding: 0.5rem 0.75rem;
}
.filter-input:focus {
  background-color: #ffffff;
  border-color: #996600;
  box-shadow: 0 0 0 2px rgba(153, 102, 0, 0.15);
}
.btn-tambah-jadwal {
  background-color: #8B6200;
  color: white;
  font-weight: 600;
  font-size: 0.9rem;
  border-radius: 6px;
  padding: 0.55rem 1rem;
  border: none;
  transition: background-color 0.2s ease;
}
.btn-tambah-jadwal:hover {
  background-color: #725000;
  color: white;
}
.table-card {
  background: white;
  border-radius: 8px;
  border: 1px solid #eaeaea;
  overflow: hidden;
}
.custom-table {
  font-size: 0.875rem;
}
.custom-table thead tr {
  border-bottom: 2px solid #edf2f7;
}
.custom-table th {
  color: #a0aec0;
  font-weight: 700;
  font-size: 0.75rem;
  letter-spacing: 0.05em;
  padding: 1rem;
  background-color: #ffffff;
}
.custom-table td {
  padding: 1rem;
  border-bottom: 1px solid #f7fafc;
}
.badge-status {
  font-size: 0.75rem;
  font-weight: 600;
  padding: 0.35em 0.8em;
  border-radius: 12px;
}
.status-active {
  background-color: #d1fae5;
  color: #059669;
}
.status-inactive {
  background-color: #f3f4f6;
  color: #6b7280;
}
.action-links a {
  font-size: 0.85rem;
}
.action-links a:hover {
  text-decoration: underline !important;
}
.btn-pagination-active {
  background-color: #8B6200;
  color: white;
  border-color: #8B6200;
}
</style>
