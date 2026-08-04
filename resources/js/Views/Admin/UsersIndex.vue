<template>
  <AdminLayout pageTitle="Kelola Akun" pageIcon="fa-users-cog">
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
      <div class="col-12">
        <div class="card shadow-sm border-0">
          <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <div class="d-flex align-items-center gap-3">
              <h5 class="mb-0"><i class="fas fa-users text-primary me-2"></i>Daftar Pengguna</h5>
            </div>
            <button class="btn btn-primary btn-sm" @click="openModal()">
              <i class="fas fa-plus me-1"></i> Tambah Pengguna
            </button>
          </div>
          <div class="card-body">
            <!-- Filters -->
            <div class="row g-3 mb-4">
              <div class="col-md-4">
                <div class="input-group">
                  <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                  <input type="text" class="form-control" placeholder="Cari nama atau email..." v-model="searchQuery" @keyup.enter="fetchUsers">
                </div>
              </div>
              <div class="col-md-3">
                <select class="form-select" v-model="filterRole" @change="fetchUsers">
                  <option value="">Semua Role</option>
                  <option value="admin">Administrator</option>
                  <option value="verifikator">Verifikator</option>
                  <option value="user">User</option>
                </select>
              </div>
              <div class="col-md-2">
                <button class="btn btn-outline-secondary w-100" @click="resetFilters">
                  <i class="fas fa-undo me-1"></i> Reset
                </button>
              </div>
            </div>

            <!-- Stats Cards -->
            <div class="row g-3 mb-4">
              <div class="col-md-3 col-6">
                <div class="border rounded-3 p-3 text-center" style="background: linear-gradient(135deg, #f8f9fa, #e9ecef);">
                  <div class="fw-bold fs-4 text-primary">{{ stats.total }}</div>
                  <small class="text-muted">Total Pengguna</small>
                </div>
              </div>
              <div class="col-md-3 col-6">
                <div class="border rounded-3 p-3 text-center" style="background: linear-gradient(135deg, #fff3e0, #ffe0b2);">
                  <div class="fw-bold fs-4" style="color: #996600;">{{ stats.admin }}</div>
                  <small class="text-muted">Administrator</small>
                </div>
              </div>
              <div class="col-md-3 col-6">
                <div class="border rounded-3 p-3 text-center" style="background: linear-gradient(135deg, #e0f7fa, #b2ebf2);">
                  <div class="fw-bold fs-4 text-info">{{ stats.verifikator }}</div>
                  <small class="text-muted">Verifikator</small>
                </div>
              </div>
              <div class="col-md-3 col-6">
                <div class="border rounded-3 p-3 text-center" style="background: linear-gradient(135deg, #e8f5e9, #c8e6c9);">
                  <div class="fw-bold fs-4 text-success">{{ stats.user }}</div>
                  <small class="text-muted">User</small>
                </div>
              </div>
            </div>

            <!-- Table -->
            <div class="table-responsive">
              <table class="table table-hover align-middle">
                <thead class="table-light">
                  <tr>
                    <th style="width: 50px;">#</th>
                    <th>Pengguna</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Terdaftar</th>
                    <th class="text-end">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-if="loading">
                    <td colspan="6" class="text-center py-4">
                      <i class="fas fa-spinner fa-spin me-2"></i>Memuat data...
                    </td>
                  </tr>
                  <tr v-else-if="users.length === 0">
                    <td colspan="6" class="text-center py-4 text-muted">
                      <i class="fas fa-users-slash fs-1 d-block mb-2"></i>
                      Tidak ada pengguna ditemukan
                    </td>
                  </tr>
                  <tr v-for="(user, index) in users" :key="user.id">
                    <td class="text-muted">{{ (currentPage - 1) * 10 + index + 1 }}</td>
                    <td>
                      <div class="d-flex align-items-center gap-2">
                        <div class="user-avatar-sm" :class="'avatar-' + (user.id % 6)">
                          {{ user.name.charAt(0).toUpperCase() }}
                        </div>
                        <div>
                          <div class="fw-semibold">{{ user.name }}</div>
                        </div>
                      </div>
                    </td>
                    <td>{{ user.email }}</td>
                    <td>
                      <span class="badge rounded-pill" :class="getRoleBadgeClass(user.role || (user.is_admin ? 'admin' : 'user'))">
                        <i :class="getRoleIcon(user.role || (user.is_admin ? 'admin' : 'user'))" class="me-1"></i>
                        {{ getRoleLabel(user.role || (user.is_admin ? 'admin' : 'user')) }}
                      </span>
                    </td>
                    <td>
                      <small class="text-muted">{{ formatDate(user.created_at) }}</small>
                    </td>
                    <td class="text-end">
                      <button class="btn btn-sm btn-outline-primary me-1" @click="openModal(user)" title="Edit">
                        <i class="fas fa-edit"></i>
                      </button>
                      <button 
                        class="btn btn-sm btn-outline-danger" 
                        @click="deleteUser(user)" 
                        title="Hapus"
                        :disabled="user.id === currentUserId"
                      >
                        <i class="fas fa-trash"></i>
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Pagination -->
            <div v-if="totalPages > 1" class="d-flex justify-content-between align-items-center mt-3">
              <small class="text-muted">Menampilkan {{ users.length }} dari {{ totalUsers }} pengguna</small>
              <nav>
                <ul class="pagination pagination-sm mb-0">
                  <li class="page-item" :class="{ disabled: currentPage === 1 }">
                    <button class="page-link" @click="goToPage(currentPage - 1)">&laquo;</button>
                  </li>
                  <li v-for="page in visiblePages" :key="page" class="page-item" :class="{ active: page === currentPage }">
                    <button class="page-link" @click="goToPage(page)">{{ page }}</button>
                  </li>
                  <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                    <button class="page-link" @click="goToPage(currentPage + 1)">&raquo;</button>
                  </li>
                </ul>
              </nav>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- User Modal -->
    <div class="modal fade" id="userModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              <i :class="isEditing ? 'fas fa-user-edit' : 'fas fa-user-plus'" class="me-2" style="color: #996600;"></i>
              {{ isEditing ? 'Edit' : 'Tambah' }} Pengguna
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="saveUser">
              <div class="mb-3">
                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                <input type="text" class="form-control" :class="{'is-invalid': errors.name}" v-model="form.name" required placeholder="Masukkan nama lengkap">
                <div v-if="errors.name" class="invalid-feedback">{{ errors.name[0] }}</div>
              </div>
              <div class="mb-3">
                <label class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control" :class="{'is-invalid': errors.email}" v-model="form.email" required placeholder="Masukkan alamat email">
                <div v-if="errors.email" class="invalid-feedback">{{ errors.email[0] }}</div>
              </div>
              <div class="mb-3">
                <label class="form-label">Password {{ isEditing ? '(kosongkan jika tidak diubah)' : '' }} <span v-if="!isEditing" class="text-danger">*</span></label>
                <div class="input-group">
                  <input :type="showPassword ? 'text' : 'password'" class="form-control" :class="{'is-invalid': errors.password}" v-model="form.password" :required="!isEditing" placeholder="Masukkan password" minlength="8">
                  <button class="btn btn-outline-secondary" type="button" @click="showPassword = !showPassword">
                    <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                  </button>
                </div>
                <div v-if="errors.password" class="invalid-feedback d-block">{{ errors.password[0] }}</div>
                <small class="form-text text-muted">Minimal 8 karakter</small>
              </div>
              <div class="mb-3">
                <label class="form-label">Role <span class="text-danger">*</span></label>
                <select class="form-select" :class="{'is-invalid': errors.role}" v-model="form.role" required>
                  <option value="" disabled>Pilih role pengguna</option>
                  <option value="admin">Administrator</option>
                  <option value="verifikator">Verifikator</option>
                  <option value="user">User</option>
                </select>
                <div v-if="errors.role" class="invalid-feedback">{{ errors.role[0] }}</div>
                <div class="mt-2">
                  <div v-if="form.role === 'admin'" class="alert alert-warning py-2 mb-0">
                    <small><i class="fas fa-shield-alt me-1"></i> Administrator memiliki akses penuh ke semua fitur sistem.</small>
                  </div>
                  <div v-else-if="form.role === 'verifikator'" class="alert alert-info py-2 mb-0">
                    <small><i class="fas fa-check-double me-1"></i> Verifikator dapat memverifikasi dan mengelola dokumen yang diajukan.</small>
                  </div>
                  <div v-else-if="form.role === 'user'" class="alert alert-success py-2 mb-0">
                    <small><i class="fas fa-user me-1"></i> User dapat mengupload dan mengelola dokumen sendiri.</small>
                  </div>
                </div>
              </div>
              <div class="text-end mt-4">
                <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary" :disabled="saving">
                  <i class="fas fa-save me-1"></i> {{ saving ? 'Menyimpan...' : 'Simpan' }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header border-0">
            <h5 class="modal-title text-danger">
              <i class="fas fa-exclamation-triangle me-2"></i>Konfirmasi Hapus
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body text-center">
            <p>Yakin ingin menghapus pengguna <strong>{{ deleteTarget?.name }}</strong>?</p>
            <small class="text-muted">Tindakan ini tidak dapat dibatalkan.</small>
          </div>
          <div class="modal-footer border-0 justify-content-center">
            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
            <button type="button" class="btn btn-danger btn-sm" @click="confirmDelete" :disabled="deleting">
              <i class="fas fa-trash me-1"></i> {{ deleting ? 'Menghapus...' : 'Hapus' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script>
import AdminLayout from '@/components/AdminLayout.vue'
import axios from '@/main'
import { Modal } from 'bootstrap'

export default {
  name: 'UsersIndex',
  components: {
    AdminLayout
  },
  data() {
    return {
      users: [],
      loading: false,
      saving: false,
      deleting: false,
      showPassword: false,
      searchQuery: '',
      filterRole: '',
      currentPage: 1,
      totalPages: 1,
      totalUsers: 0,
      currentUserId: null,
      stats: {
        total: 0,
        admin: 0,
        verifikator: 0,
        user: 0
      },
      toast: {
        show: false,
        message: '',
        type: 'success'
      },
      errors: {},
      
      // Modal
      userModal: null,
      deleteModal: null,
      isEditing: false,
      deleteTarget: null,
      form: {
        id: null,
        name: '',
        email: '',
        password: '',
        role: ''
      }
    }
  },
  computed: {
    visiblePages() {
      const pages = []
      const start = Math.max(1, this.currentPage - 2)
      const end = Math.min(this.totalPages, this.currentPage + 2)
      for (let i = start; i <= end; i++) {
        pages.push(i)
      }
      return pages
    }
  },
  mounted() {
    // Get current user ID
    const userData = localStorage.getItem('user')
    if (userData) {
      this.currentUserId = JSON.parse(userData).id
    }
    
    this.fetchUsers()
    this.fetchStats()

    this.$nextTick(() => {
      this.userModal = new Modal(document.getElementById('userModal'))
      this.deleteModal = new Modal(document.getElementById('deleteModal'))
    })
  },
  methods: {
    showToast(message, type = 'success') {
      this.toast = { show: true, message, type }
      setTimeout(() => { this.toast.show = false }, 3000)
    },

    async fetchUsers() {
      this.loading = true
      try {
        const params = { page: this.currentPage }
        if (this.searchQuery) params.search = this.searchQuery
        if (this.filterRole) params.role = this.filterRole

        const response = await axios.get('/users', { params })
        if (response.data.success) {
          const data = response.data.data
          this.users = data.data
          this.currentPage = data.current_page
          this.totalPages = data.last_page
          this.totalUsers = data.total
        }
      } catch (error) {
        console.error('Fetch users error:', error)
        this.showToast('Gagal memuat data pengguna', 'danger')
      } finally {
        this.loading = false
      }
    },

    async fetchStats() {
      try {
        const response = await axios.get('/users', { params: { per_page: 9999 } })
        if (response.data.success) {
          const allUsers = response.data.data.data
          this.stats.total = response.data.data.total
          this.stats.admin = allUsers.filter(u => u.role === 'admin' || u.is_admin).length
          this.stats.verifikator = allUsers.filter(u => u.role === 'verifikator').length
          this.stats.user = allUsers.filter(u => (u.role === 'user' || !u.role) && !u.is_admin).length
        }
      } catch (error) {
        console.error('Fetch stats error:', error)
      }
    },

    openModal(user = null) {
      this.errors = {}
      this.showPassword = false
      if (user) {
        this.isEditing = true
        this.form = {
          id: user.id,
          name: user.name,
          email: user.email,
          password: '',
          role: user.role || (user.is_admin ? 'admin' : 'user')
        }
      } else {
        this.isEditing = false
        this.form = {
          id: null,
          name: '',
          email: '',
          password: '',
          role: ''
        }
      }
      this.userModal?.show()
    },

    async saveUser() {
      this.saving = true
      this.errors = {}
      try {
        let response
        if (this.isEditing) {
          const data = { ...this.form }
          if (!data.password) delete data.password
          response = await axios.put(`/users/${this.form.id}`, data)
        } else {
          response = await axios.post('/users', this.form)
        }
        
        if (response.data.success) {
          this.showToast(response.data.message || 'Pengguna berhasil disimpan')
          this.userModal?.hide()
          this.fetchUsers()
          this.fetchStats()
        }
      } catch (error) {
        console.error('Save user error:', error)
        if (error.response?.status === 422 && error.response?.data?.errors) {
          this.errors = error.response.data.errors
        } else {
          this.showToast('Gagal menyimpan data pengguna', 'danger')
        }
      } finally {
        this.saving = false
      }
    },

    deleteUser(user) {
      this.deleteTarget = user
      this.deleteModal?.show()
    },

    async confirmDelete() {
      if (!this.deleteTarget) return
      this.deleting = true
      try {
        const response = await axios.delete(`/users/${this.deleteTarget.id}`)
        if (response.data.success) {
          this.showToast('Pengguna berhasil dihapus')
          this.deleteModal?.hide()
          this.fetchUsers()
          this.fetchStats()
        }
      } catch (error) {
        console.error('Delete user error:', error)
        this.showToast(error.response?.data?.message || 'Gagal menghapus pengguna', 'danger')
      } finally {
        this.deleting = false
      }
    },

    goToPage(page) {
      if (page >= 1 && page <= this.totalPages) {
        this.currentPage = page
        this.fetchUsers()
      }
    },

    resetFilters() {
      this.searchQuery = ''
      this.filterRole = ''
      this.currentPage = 1
      this.fetchUsers()
    },

    getRoleBadgeClass(role) {
      const classes = {
        admin: 'bg-warning text-dark',
        verifikator: 'bg-info text-white',
        user: 'bg-success text-white'
      }
      return classes[role] || 'bg-secondary'
    },

    getRoleIcon(role) {
      const icons = {
        admin: 'fas fa-shield-alt',
        verifikator: 'fas fa-check-double',
        user: 'fas fa-user'
      }
      return icons[role] || 'fas fa-user'
    },

    getRoleLabel(role) {
      const labels = {
        admin: 'Administrator',
        verifikator: 'Verifikator',
        user: 'User'
      }
      return labels[role] || 'User'
    },

    formatDate(dateStr) {
      if (!dateStr) return '-'
      const date = new Date(dateStr)
      return date.toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric'
      })
    }
  }
}
</script>

<style scoped>
.user-avatar-sm {
  width: 35px;
  height: 35px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: bold;
  font-size: 14px;
  flex-shrink: 0;
}

.avatar-0 { background: #996600; }
.avatar-1 { background: #aa7700; }
.avatar-2 { background: #bb8800; }
.avatar-3 { background: #cc9900; }
.avatar-4 { background: #ddaa00; }
.avatar-5 { background: #eebb00; }

.badge {
  font-size: 0.75rem;
  padding: 0.4em 0.8em;
}

.page-link {
  color: #996600;
}

.page-item.active .page-link {
  background-color: #996600;
  border-color: #996600;
  color: white;
}

.table th {
  font-weight: 600;
  font-size: 0.85rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: #6c757d;
}

.btn-primary {
  background-color: #996600;
  border-color: #996600;
}

.btn-primary:hover {
  background-color: #7a5200;
  border-color: #7a5200;
}

.text-primary {
  color: #996600 !important;
}
</style>
