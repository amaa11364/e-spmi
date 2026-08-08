<template>
  <AdminLayout pageTitle="Kelola IKU" pageIcon="fa-chart-line">
    <div class="container-fluid py-4">
      <!-- Toast Notification -->
      <div
        class="toast-container position-fixed top-0 end-0 p-3"
        style="z-index: 1055"
      >
        <div
          class="toast align-items-center text-white border-0 shadow-lg"
          :class="['bg-' + toast.type, { show: toast.show }]"
          role="alert"
          aria-live="assertive"
          aria-atomic="true"
        >
          <div class="d-flex">
            <div class="toast-body d-flex align-items-center">
              <i class="fas me-2 fs-5" :class="toast.type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'"></i>
              {{ toast.message }}
            </div>
            <button
              type="button"
              class="btn-close btn-close-white me-2 m-auto"
              @click="toast.show = false"
              aria-label="Close"
            ></button>
          </div>
        </div>
      </div>

      <!-- Stats Cards Row -->
      <div class="row mb-4">
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
          <div class="card h-100 shadow-sm border-0 stat-card total">
            <div class="card-body p-3">
              <div class="row align-items-center">
                <div class="col-8">
                  <div class="numbers">
                    <p class="text-sm mb-0 text-uppercase font-weight-bold opacity-7">Total IKU</p>
                    <h5 class="font-weight-bolder mb-0 fs-3">{{ stats.total }}</h5>
                  </div>
                </div>
                <div class="col-4 text-end">
                  <div class="icon-shape bg-white text-primary rounded-circle shadow text-center d-inline-flex align-items-center justify-content-center p-3">
                    <i class="fas fa-chart-line fs-4"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
          <div class="card h-100 shadow-sm border-0 stat-card active">
            <div class="card-body p-3">
              <div class="row align-items-center">
                <div class="col-8">
                  <div class="numbers">
                    <p class="text-sm mb-0 text-uppercase font-weight-bold opacity-7">Aktif</p>
                    <h5 class="font-weight-bolder mb-0 fs-3">{{ stats.aktif }}</h5>
                  </div>
                </div>
                <div class="col-4 text-end">
                  <div class="icon-shape bg-white text-success rounded-circle shadow text-center d-inline-flex align-items-center justify-content-center p-3">
                    <i class="fas fa-check-circle fs-4"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
          <div class="card h-100 shadow-sm border-0 stat-card inactive">
            <div class="card-body p-3">
              <div class="row align-items-center">
                <div class="col-8">
                  <div class="numbers">
                    <p class="text-sm mb-0 text-uppercase font-weight-bold opacity-7">Nonaktif</p>
                    <h5 class="font-weight-bolder mb-0 fs-3">{{ stats.nonaktif }}</h5>
                  </div>
                </div>
                <div class="col-4 text-end">
                  <div class="icon-shape bg-white text-danger rounded-circle shadow text-center d-inline-flex align-items-center justify-content-center p-3">
                    <i class="fas fa-times-circle fs-4"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-sm-6">
          <div class="card h-100 shadow-sm border-0 stat-card info">
            <div class="card-body p-3">
              <div class="row align-items-center">
                <div class="col-12">
                  <div class="d-flex align-items-center">
                    <div class="icon-shape bg-white text-warning rounded-circle shadow text-center d-inline-flex align-items-center justify-content-center p-2 me-3">
                      <i class="fas fa-info-circle fs-5" style="color: #996600"></i>
                    </div>
                    <div>
                      <p class="text-sm mb-0 font-weight-bold opacity-9">Sistem IKU</p>
                      <h6 class="font-weight-bolder mb-0">IKU1-IKU8 Standar Dikti</h6>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Card -->
      <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-bottom py-3">
          <div class="row align-items-center">
            <div class="col-md-6 mb-md-0 mb-3">
              <h5 class="mb-0 fw-bold">
                <i class="fas fa-chart-line text-primary me-2"></i>Daftar Indikator Kinerja Utama
              </h5>
            </div>
            <div class="col-md-6 text-md-end">
              <button class="btn btn-primary btn-sm px-3 py-2 fw-semibold shadow-sm" @click="openModal()">
                <i class="fas fa-plus me-1"></i> Tambah IKU
              </button>
            </div>
          </div>
        </div>

        <div class="card-body p-0">
          <!-- Search & Filters -->
          <div class="p-3 bg-light border-bottom">
            <div class="row g-2 align-items-center">
              <div class="col-md-5">
                <div class="input-group">
                  <span class="input-group-text bg-white border-end-0 text-muted">
                    <i class="fas fa-search"></i>
                  </span>
                  <input
                    type="text"
                    class="form-control border-start-0 ps-0"
                    placeholder="Cari kode atau nama IKU..."
                    v-model="searchQuery"
                    @keyup.enter="fetchData(1)"
                  />
                </div>
              </div>
              <div class="col-md-4">
                <select class="form-select" v-model="filterStatus" @change="fetchData(1)">
                  <option value="">Semua Status</option>
                  <option value="1">Aktif</option>
                  <option value="0">Nonaktif</option>
                </select>
              </div>
              <div class="col-md-3 text-md-end">
                <button class="btn btn-outline-secondary w-100" @click="resetFilters">
                  <i class="fas fa-sync-alt me-1"></i> Reset Filter
                </button>
              </div>
            </div>
          </div>

          <!-- Data Table -->
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="bg-light">
                <tr>
                  <th class="text-center" width="5%">#</th>
                  <th width="15%">Kode IKU</th>
                  <th width="25%">Nama IKU</th>
                  <th width="35%">Deskripsi</th>
                  <th class="text-center" width="10%">Status</th>
                  <th class="text-center" width="10%">Aksi</th>
                </tr>
              </thead>
              <tbody class="border-top-0">
                <tr v-if="loading">
                  <td colspan="6" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                      <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="text-muted mt-2 mb-0">Memuat data IKU...</p>
                  </td>
                </tr>
                <tr v-else-if="ikus.length === 0">
                  <td colspan="6" class="text-center py-5">
                    <div class="empty-state">
                      <i class="fas fa-chart-line fs-1 text-muted mb-3 opacity-25"></i>
                      <h6 class="text-muted fw-normal">Tidak ada data IKU ditemukan.</h6>
                    </div>
                  </td>
                </tr>
                <tr v-else v-for="(iku, index) in ikus" :key="iku.id">
                  <td class="text-center text-secondary">{{ (currentPage - 1) * 10 + index + 1 }}</td>
                  <td>
                    <span class="badge kode-badge fs-6 fw-bold shadow-sm">{{ iku.kode }}</span>
                  </td>
                  <td class="fw-semibold text-dark">{{ iku.nama }}</td>
                  <td>
                    <span class="text-muted text-truncate d-inline-block" style="max-width: 300px;" :title="iku.deskripsi">
                      {{ truncateText(iku.deskripsi, 80) }}
                    </span>
                  </td>
                  <td class="text-center">
                    <span
                      class="badge rounded-pill px-3 py-2 fw-normal"
                      :class="iku.status ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger border border-danger-subtle'"
                    >
                      <i class="fas me-1" :class="iku.status ? 'fa-check-circle' : 'fa-times-circle'"></i>
                      {{ iku.status ? 'Aktif' : 'Nonaktif' }}
                    </span>
                  </td>
                  <td class="text-center">
                    <div class="btn-group shadow-sm rounded">
                      <button
                        class="btn btn-sm btn-light border"
                        title="Edit"
                        @click="openModal(iku)"
                      >
                        <i class="fas fa-edit text-primary"></i>
                      </button>
                      <button
                        class="btn btn-sm btn-light border"
                        title="Hapus"
                        @click="confirmDelete(iku)"
                      >
                        <i class="fas fa-trash-alt text-danger"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div class="card-footer bg-white border-top py-3" v-if="totalItems > 0">
            <div class="row align-items-center">
              <div class="col-sm-6 text-center text-sm-start mb-3 mb-sm-0">
                <span class="text-muted text-sm">
                  Menampilkan <strong>{{ (currentPage - 1) * 10 + 1 }}</strong> hingga
                  <strong>{{ Math.min(currentPage * 10, totalItems) }}</strong> dari
                  <strong>{{ totalItems }}</strong> entri
                </span>
              </div>
              <div class="col-sm-6">
                <nav aria-label="Page navigation">
                  <ul class="pagination pagination-sm justify-content-center justify-content-sm-end mb-0">
                    <li class="page-item" :class="{ disabled: currentPage === 1 }">
                      <a class="page-link shadow-none" href="#" @click.prevent="goToPage(currentPage - 1)">
                        <i class="fas fa-chevron-left"></i>
                      </a>
                    </li>
                    <li
                      class="page-item"
                      v-for="page in totalPages"
                      :key="page"
                      :class="{ active: currentPage === page }"
                    >
                      <a class="page-link shadow-none" href="#" @click.prevent="goToPage(page)">
                        {{ page }}
                      </a>
                    </li>
                    <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                      <a class="page-link shadow-none" href="#" @click.prevent="goToPage(currentPage + 1)">
                        <i class="fas fa-chevron-right"></i>
                      </a>
                    </li>
                  </ul>
                </nav>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <div
      class="modal fade"
      id="ikuModal"
      tabindex="-1"
      aria-labelledby="ikuModalLabel"
      aria-hidden="true"
      data-bs-backdrop="static"
    >
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
          <div class="modal-header border-bottom-0 pb-0">
            <h5 class="modal-title fw-bold d-flex align-items-center" id="ikuModalLabel">
              <div class="icon-shape bg-primary-subtle text-primary rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                <i class="fas" :class="isEditing ? 'fa-edit' : 'fa-plus-circle'" style="color: #996600"></i>
              </div>
              {{ isEditing ? 'Edit IKU' : 'Tambah IKU' }}
            </h5>
            <button
              type="button"
              class="btn-close shadow-none"
              data-bs-dismiss="modal"
              aria-label="Close"
            ></button>
          </div>
          <div class="modal-body pt-4">
            <form @submit.prevent="saveItem">
              <div class="mb-3">
                <label for="kode" class="form-label fw-semibold">Kode IKU <span class="text-danger">*</span></label>
                <div class="input-group has-validation">
                  <span class="input-group-text bg-light"><i class="fas fa-barcode text-muted"></i></span>
                  <input
                    type="text"
                    class="form-control shadow-none"
                    :class="{ 'is-invalid': errors.kode }"
                    id="kode"
                    v-model="form.kode"
                    placeholder="Contoh: IKU1"
                    required
                  />
                  <div class="invalid-feedback" v-if="errors.kode">
                    {{ errors.kode[0] }}
                  </div>
                </div>
              </div>

              <div class="mb-3">
                <label for="nama" class="form-label fw-semibold">Nama IKU <span class="text-danger">*</span></label>
                <div class="input-group has-validation">
                  <span class="input-group-text bg-light"><i class="fas fa-font text-muted"></i></span>
                  <input
                    type="text"
                    class="form-control shadow-none"
                    :class="{ 'is-invalid': errors.nama }"
                    id="nama"
                    v-model="form.nama"
                    placeholder="Masukkan nama indikator"
                    required
                  />
                  <div class="invalid-feedback" v-if="errors.nama">
                    {{ errors.nama[0] }}
                  </div>
                </div>
              </div>

              <div class="mb-3">
                <label for="deskripsi" class="form-label fw-semibold">Deskripsi</label>
                <textarea
                  class="form-control shadow-none"
                  :class="{ 'is-invalid': errors.deskripsi }"
                  id="deskripsi"
                  v-model="form.deskripsi"
                  rows="3"
                  placeholder="Masukkan deskripsi IKU"
                ></textarea>
                <div class="invalid-feedback" v-if="errors.deskripsi">
                  {{ errors.deskripsi[0] }}
                </div>
              </div>

              <div class="mb-2">
                <label class="form-label fw-semibold d-block">Status</label>
                <div class="form-check form-switch form-switch-md d-flex align-items-center p-0">
                  <input
                    class="form-check-input ms-0 me-2 shadow-none"
                    type="checkbox"
                    role="switch"
                    id="status"
                    v-model="form.status"
                  />
                  <label class="form-check-label mb-0" for="status">
                    <span
                      class="badge"
                      :class="form.status ? 'bg-success' : 'bg-secondary'"
                    >
                      {{ form.status ? 'Aktif' : 'Nonaktif' }}
                    </span>
                  </label>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer bg-light border-top-0 rounded-bottom">
            <button
              type="button"
              class="btn btn-outline-secondary px-4 shadow-none"
              data-bs-dismiss="modal"
              :disabled="saving"
            >
              Batal
            </button>
            <button
              type="button"
              class="btn btn-primary px-4 shadow-none d-flex align-items-center"
              @click="saveItem"
              :disabled="saving"
            >
              <span v-if="saving" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
              <i v-else class="fas fa-save me-2"></i>
              Simpan
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow">
          <div class="modal-body text-center p-4">
            <div class="mb-3">
              <div class="icon-shape bg-danger-subtle text-danger rounded-circle mx-auto d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                <i class="fas fa-exclamation-triangle fs-2"></i>
              </div>
            </div>
            <h5 class="fw-bold mb-2">Hapus IKU?</h5>
            <p class="text-muted mb-0">Anda yakin ingin menghapus data ini?</p>
            <div v-if="deleteTarget" class="mt-3 p-2 bg-light rounded text-start">
              <div class="fw-bold text-dark">{{ deleteTarget.kode }}</div>
              <div class="text-sm text-muted text-truncate">{{ deleteTarget.nama }}</div>
            </div>
            <div class="d-flex justify-content-center gap-2 mt-4">
              <button type="button" class="btn btn-light border px-3" data-bs-dismiss="modal" :disabled="deleting">
                Batal
              </button>
              <button type="button" class="btn btn-danger px-3 d-flex align-items-center" @click="deleteItem" :disabled="deleting">
                <span v-if="deleting" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                <i v-else class="fas fa-trash-alt me-2"></i>
                Hapus
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script>
import AdminLayout from '@/components/AdminLayout.vue';
import axios from '@/main';
import { Modal } from 'bootstrap';

export default {
  name: 'IkuIndex',
  components: {
    AdminLayout
  },
  data() {
    return {
      ikus: [],
      loading: false,
      saving: false,
      deleting: false,
      searchQuery: '',
      filterStatus: '',
      currentPage: 1,
      totalPages: 1,
      totalItems: 0,
      stats: {
        total: 0,
        aktif: 0,
        nonaktif: 0
      },
      toast: {
        show: false,
        message: '',
        type: 'success'
      },
      errors: {},
      ikuModal: null,
      deleteModal: null,
      isEditing: false,
      deleteTarget: null,
      form: {
        id: null,
        kode: '',
        nama: '',
        deskripsi: '',
        status: true
      }
    };
  },
  mounted() {
    this.ikuModal = new Modal(document.getElementById('ikuModal'));
    this.deleteModal = new Modal(document.getElementById('deleteModal'));
    this.fetchData();
    this.fetchStats();
  },
  methods: {
    showToast(message, type = 'success') {
      this.toast.message = message;
      this.toast.type = type;
      this.toast.show = true;
      setTimeout(() => {
        this.toast.show = false;
      }, 3000);
    },
    async fetchStats() {
      try {
        const response = await axios.get('/ikus', {
            params: { per_page: 9999, _t: Date.now() }
        });
        
        if (response.data.success) {
            const allData = response.data.data.data;
            this.stats.total = allData.length;
            this.stats.aktif = allData.filter(item => item.status).length;
            this.stats.nonaktif = allData.filter(item => !item.status).length;
        }
      } catch (error) {
        console.error("Error fetching stats:", error);
      }
    },
    async fetchData(page = this.currentPage) {
      this.loading = true;
      try {
        const response = await axios.get('/ikus', {
          params: {
            search: this.searchQuery,
            status: this.filterStatus,
            page: page,
            _t: Date.now()
          }
        });
        
        if (response.data.success) {
          const data = response.data.data;
          this.ikus = data.data || [];
          this.currentPage = data.current_page || 1;
          this.totalPages = data.last_page || 1;
          this.totalItems = data.total || 0;
        }
      } catch (error) {
        console.error('Error fetching data:', error);
        this.showToast('Gagal memuat data', 'danger');
      } finally {
        this.loading = false;
      }
    },
    openModal(item = null) {
      this.errors = {};
      if (item) {
        this.isEditing = true;
        this.form = { ...item };
        this.form.status = Boolean(item.status);
      } else {
        this.isEditing = false;
        this.form = {
          id: null,
          kode: '',
          nama: '',
          deskripsi: '',
          status: true
        };
      }
      this.ikuModal.show();
    },
    async saveItem() {
      this.saving = true;
      this.errors = {};
      try {
        const payload = { ...this.form, status: this.form.status ? 1 : 0 };
        let response;
        
        if (this.isEditing) {
          response = await axios.put(`/ikus/${this.form.id}`, payload);
        } else {
          response = await axios.post('/ikus', payload);
        }
        
        if (response.data.success) {
          this.ikuModal.hide();
          this.showToast(this.isEditing ? 'Data berhasil diperbarui' : 'Data berhasil ditambahkan');
          await this.fetchData();
          await this.fetchStats();
        }
      } catch (error) {
        if (error.response && error.response.status === 422) {
          this.errors = error.response.data.errors || {};
        } else {
          this.showToast('Terjadi kesalahan. Silakan coba lagi.', 'danger');
        }
      } finally {
        this.saving = false;
      }
    },
    confirmDelete(item) {
      this.deleteTarget = item;
      this.deleteModal.show();
    },
    async deleteItem() {
      if (!this.deleteTarget) return;
      this.deleting = true;
      try {
        const response = await axios.delete(`/ikus/${this.deleteTarget.id}`);
        if (response.data.success) {
          this.deleteModal.hide();
          this.showToast('Data berhasil dihapus');
          await this.fetchData();
          await this.fetchStats();
        }
      } catch (error) {
        console.error('Error deleting data:', error);
        this.showToast('Gagal menghapus data', 'danger');
      } finally {
        this.deleting = false;
        this.deleteTarget = null;
      }
    },
    goToPage(page) {
      if (page >= 1 && page <= this.totalPages) {
        this.fetchData(page);
      }
    },
    resetFilters() {
      this.searchQuery = '';
      this.filterStatus = '';
      this.fetchData(1);
    },
    truncateText(text, length) {
      if (!text) return '-';
      if (text.length <= length) return text;
      return text.substring(0, length) + '...';
    }
  }
};
</script>

<style scoped>
/* Stats cards styling */
.stat-card {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
  border-radius: 12px;
}
.stat-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
}

.stat-card.total {
  background-color: #ffffff;
}
.stat-card.active {
  background-color: #ffffff;
}
.stat-card.inactive {
  background-color: #ffffff;
}
.stat-card.info {
  background-color: #ffffff;
}

.icon-shape {
  width: 48px;
  height: 48px;
}

/* Brand Colors */
.text-primary {
  color: #996600 !important;
}
.btn-primary {
  background-color: #996600;
  border-color: #996600;
}
.btn-primary:hover, .btn-primary:focus, .btn-primary:active {
  background-color: #7a5200;
  border-color: #7a5200;
}

.bg-primary-subtle {
  background-color: rgba(153, 102, 0, 0.1) !important;
}

.kode-badge {
  background-color: #996600;
  color: white;
  border-radius: 6px;
  padding: 0.4em 0.8em;
}

/* Table styling */
.table th {
  font-weight: 600;
  text-transform: uppercase;
  font-size: 0.75rem;
  letter-spacing: 0.5px;
  color: #6c757d;
  padding: 1rem;
}
.table td {
  padding: 1rem;
  vertical-align: middle;
}

/* Form switch scaling */
.form-switch-md .form-check-input {
  width: 2.5em;
  height: 1.25em;
  cursor: pointer;
}

/* Pagination active state */
.page-item.active .page-link {
  background-color: #996600;
  border-color: #996600;
  color: white;
}
.page-link {
  color: #996600;
}
</style>
