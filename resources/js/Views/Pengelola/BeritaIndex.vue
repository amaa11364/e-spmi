<template>
  <AdminLayout pageTitle="Kelola Pos" pageIcon="fa-newspaper">
    <div class="pos-management-container">
      
      <!-- Filter Bar -->
      <div class="filter-card shadow-sm mb-4">
        <div class="row g-3 align-items-end">
          <!-- Pencarian -->
          <div class="col-md-4">
            <label class="form-label text-muted small mb-1">Pencarian</label>
            <input 
              type="text" 
              class="form-control filter-input" 
              placeholder="Cari judul, konten, atau keyword..." 
              v-model="search"
              @keyup.enter="loadData(1)"
            />
          </div>

          <!-- Status Dropdown -->
          <div class="col-md-3">
            <label class="form-label text-muted small mb-1">Status</label>
            <select class="form-select filter-input" v-model="statusFilter" @change="loadData(1)">
              <option value="">Semua Status</option>
              <option value="published">Published</option>
              <option value="draft">Draft</option>
            </select>
          </div>

          <!-- Kategori Dropdown -->
          <div class="col-md-3">
            <label class="form-label text-muted small mb-1">Kategori</label>
            <select class="form-select filter-input" v-model="categoryFilter" @change="loadData(1)">
              <option value="">Semua Kategori</option>
              <option value="akademik">Akademik</option>
              <option value="pengumuman">Pengumuman</option>
            </select>
          </div>

          <!-- Tombol Tambah Pos -->
          <div class="col-md-2 text-end">
            <router-link to="/pengelola/berita/create" class="btn btn-tambah-pos w-100">
              <i class="fas fa-plus me-1"></i> Tambah Pos
            </router-link>
          </div>
        </div>
      </div>

      <!-- Main Data Table Card -->
      <div class="table-card shadow-sm">
        <div class="table-responsive">
          <table class="table align-middle custom-table mb-0">
            <thead>
              <tr>
                <th style="width: 35%;">JUDUL</th>
                <th style="width: 15%;">TAGS</th>
                <th style="width: 12%;">STATUS</th>
                <th style="width: 13%;">PENULIS</th>
                <th style="width: 15%;">TANGGAL</th>
                <th style="width: 10%; text-align: right;">AKSI</th>
              </tr>
            </thead>
            <tbody>
              <!-- Loading -->
              <tr v-if="loading">
                <td colspan="6" class="text-center py-4 text-muted">
                  <div class="spinner-border spinner-border-sm text-warning me-2"></div>
                  Memuat data pos...
                </td>
              </tr>

              <!-- Empty -->
              <tr v-else-if="filteredBeritas.length === 0">
                <td colspan="6" class="text-center py-4 text-muted">Tidak ada data pos berita ditemukan.</td>
              </tr>

              <!-- Data Rows -->
              <tr v-else v-for="item in filteredBeritas" :key="item.id">
                <td class="fw-semibold text-dark text-truncate" style="max-width: 300px;">
                  {{ item.judul }}
                </td>
                <td>
                  <span class="badge badge-tag">
                    {{ item.kategori || 'akademik' }}
                  </span>
                </td>
                <td>
                  <span 
                    class="badge badge-status"
                    :class="item.is_published ? 'status-published' : 'status-draft'"
                    @click="togglePublish(item.id)"
                    style="cursor: pointer;"
                  >
                    {{ item.is_published ? 'Published' : 'Draft' }}
                  </span>
                </td>
                <td class="text-muted small">
                  {{ item.penulis || 'sysadmin' }}
                </td>
                <td class="text-muted small">
                  {{ formatDate(item.created_at) }}
                </td>
                <td class="text-end action-links">
                  <router-link :to="`/pengelola/berita/edit/${item.id}`" class="text-warning me-2 text-decoration-none fw-semibold">
                    Edit
                  </router-link>
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
            Menampilkan {{ pagination.from || 1 }} sampai {{ pagination.to || beritas.length }} dari {{ pagination.total || beritas.length }} pos
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
  </AdminLayout>
</template>

<script>
import AdminLayout from '@/components/AdminLayout.vue'
import axios from 'axios'

export default {
  name: 'BeritaIndex',
  components: { AdminLayout },
  data() {
    return {
      beritas: [],
      search: '',
      statusFilter: '',
      categoryFilter: '',
      loading: false,
      pagination: {
        current_page: 1,
        last_page: 1,
        total: 0,
        from: 0,
        to: 0
      }
    }
  },
  computed: {
    // Client-side filter tambahan jika memilih filter status / kategori di dropdown
    filteredBeritas() {
      return this.beritas.filter(item => {
        let matchStatus = true
        let matchCategory = true

        if (this.statusFilter === 'published') matchStatus = item.is_published == 1
        if (this.statusFilter === 'draft') matchStatus = item.is_published == 0

        if (this.categoryFilter) {
          matchCategory = (item.kategori || 'akademik').toLowerCase() === this.categoryFilter.toLowerCase()
        }

        return matchStatus && matchCategory
      })
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
        // PERBAIKAN: Gunakan 'beritas' (tanpa awalan /api/)
        const response = await axios.get(`beritas?page=${page}&search=${this.search}`, {
          headers: { 'Authorization': `Bearer ${token}` }
        })
        
        if (response.data.success) {
          const resData = response.data.data
          this.beritas = resData.data
          this.pagination.current_page = resData.current_page
          this.pagination.last_page = resData.last_page
          this.pagination.total = resData.total
          this.pagination.from = resData.from
          this.pagination.to = resData.to
        }
      } catch (err) {
        console.error('Gagal mengambil data berita:', err)
      } finally {
        this.loading = false
      }
    },
    async togglePublish(id) {
      try {
        const token = localStorage.getItem('token') || ''
        // PERBAIKAN: Gunakan 'beritas/' (tanpa awalan /api/)
        const response = await axios.patch(`beritas/${id}/toggle-publish`, {}, {
          headers: { 'Authorization': `Bearer ${token}` }
        })
        if (response.data.success) {
          this.loadData(this.pagination.current_page)
        }
      } catch (err) {
        alert('Gagal mengubah status publish')
      }
    },
    async confirmDelete(id) {
      if (confirm('Apakah Anda yakin ingin menghapus pos berita ini?')) {
        try {
          const token = localStorage.getItem('token') || ''
          // PERBAIKAN: Gunakan 'beritas/' (tanpa awalan /api/)
          const response = await axios.delete(`beritas/${id}`, {
            headers: { 'Authorization': `Bearer ${token}` }
          })
          if (response.data.success) {
            this.loadData(this.pagination.current_page)
          }
        } catch (err) {
          alert('Gagal menghapus berita')
        }
      }
    },
    formatDate(date) {
      if (!date) return '-'
      const d = new Date(date)
      return d.toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
      })
    }
  }
}
</script>

<style scoped>
/* CSS bawaan kamu tetap sama */
.pos-management-container {
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
.btn-tambah-pos {
  background-color: #8B6200;
  color: white;
  font-weight: 600;
  font-size: 0.9rem;
  border-radius: 6px;
  padding: 0.55rem 1rem;
  border: none;
  transition: background-color 0.2s ease;
}
.btn-tambah-pos:hover {
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
.badge-tag {
  background-color: #e2e8f0;
  color: #4a5568;
  font-weight: 500;
  font-size: 0.75rem;
  padding: 0.35em 0.65em;
  border-radius: 4px;
}
.badge-status {
  font-size: 0.75rem;
  font-weight: 600;
  padding: 0.35em 0.8em;
  border-radius: 12px;
}
.status-published {
  background-color: #d1fae5;
  color: #059669;
}
.status-draft {
  background-color: #fee2e2;
  color: #dc2626;
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