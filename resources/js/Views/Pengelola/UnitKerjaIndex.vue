<template>
    <AdminLayout pageTitle="Kelola Unit Kerja" pageIcon="fa-building">
        <!-- Toast Notification -->
        <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1055;">
            <div id="unitKerjaToast" class="toast align-items-center text-white border-0" 
                 :class="`bg-${toast.type}`" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fas" :class="toast.type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'"></i>
                        {{ toast.message }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        </div>

        <div class="container-fluid py-4">
            <!-- Stats Cards Row -->
            <div class="row g-4 mb-4">
                <!-- Total Unit Kerja -->
                <div class="col-xl-3 col-sm-6">
                    <div class="card h-100 border-0 shadow-sm bg-white">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-8">
                                    <div class="text-muted small fw-bold mb-1">TOTAL UNIT KERJA</div>
                                    <h3 class="fw-bold mb-0 text-dark">{{ stats.total }}</h3>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="icon-shape rounded-circle shadow-sm d-inline-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: rgba(153, 102, 0, 0.1);">
                                        <i class="fas fa-building fs-4" style="color: #996600;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Aktif -->
                <div class="col-xl-3 col-sm-6">
                    <div class="card h-100 border-0 shadow-sm bg-white">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-8">
                                    <div class="text-muted small fw-bold mb-1">AKTIF</div>
                                    <h3 class="fw-bold mb-0 text-dark">{{ stats.aktif }}</h3>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="icon-shape rounded-circle shadow-sm d-inline-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: rgba(40, 167, 69, 0.1);">
                                        <i class="fas fa-check-circle fs-4 text-success"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Nonaktif -->
                <div class="col-xl-3 col-sm-6">
                    <div class="card h-100 border-0 shadow-sm bg-white">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-8">
                                    <div class="text-muted small fw-bold mb-1">NONAKTIF</div>
                                    <h3 class="fw-bold mb-0 text-dark">{{ stats.nonaktif }}</h3>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="icon-shape rounded-circle shadow-sm d-inline-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: rgba(220, 53, 69, 0.1);">
                                        <i class="fas fa-times-circle fs-4 text-danger"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Terbaru Ditambahkan -->
                <div class="col-xl-3 col-sm-6">
                    <div class="card h-100 border-0 shadow-sm bg-white">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-8">
                                    <div class="text-muted small fw-bold mb-1">TERBARU</div>
                                    <h5 class="fw-bold mb-0 text-truncate mt-2 text-dark">{{ stats.terbaru || '-' }}</h5>
                                </div>
                                <div class="col-4 text-end">
                                    <div class="icon-shape rounded-circle shadow-sm d-inline-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: rgba(153, 102, 0, 0.1);">
                                        <i class="fas fa-clock fs-4" style="color: #996600;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <!-- Card Header -->
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-building text-primary me-2"></i>Daftar Unit Kerja
                        </h5>
                        <button class="btn btn-primary" @click="openModal()">
                            <i class="fas fa-plus me-2"></i>Tambah Unit Kerja
                        </button>
                    </div>
                </div>

                <!-- Search & Filters -->
                <div class="card-body border-bottom bg-light py-3">
                    <div class="row g-3">
                        <div class="col-md-6 col-lg-4">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                                <input type="text" class="form-control border-start-0" 
                                       v-model="searchQuery" 
                                       @keyup.enter="fetchData(1)"
                                       placeholder="Cari kode atau nama...">
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-3">
                            <select class="form-select" v-model="filterStatus" @change="fetchData(1)">
                                <option value="">Semua Status</option>
                                <option value="1">Aktif</option>
                                <option value="0">Nonaktif</option>
                            </select>
                        </div>
                        <div class="col-md-2 col-lg-2">
                            <button class="btn btn-outline-secondary w-100" @click="resetFilters">
                                <i class="fas fa-sync-alt me-1"></i>Reset
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
                                <th width="15%">Kode</th>
                                <th width="25%">Nama Unit Kerja</th>
                                <th width="30%">Deskripsi</th>
                                <th width="10%" class="text-center">Status</th>
                                <th width="15%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="loading">
                                <td colspan="6" class="text-center py-5">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="text-muted mt-2 mb-0">Memuat data...</p>
                                </td>
                            </tr>
                            <tr v-else-if="unitKerjas.length === 0">
                                <td colspan="6" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="fas fa-building fa-3x text-muted mb-3 opacity-50"></i>
                                        <h5 class="text-muted">Tidak ada data unit kerja</h5>
                                        <p class="text-muted small">Coba sesuaikan pencarian atau filter Anda.</p>
                                    </div>
                                </td>
                            </tr>
                            <tr v-else v-for="(item, index) in unitKerjas" :key="item.id">
                                <td class="text-center">{{ (currentPage - 1) * 10 + index + 1 }}</td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ item.kode }}</span>
                                </td>
                                <td>
                                    <div class="fw-bold">{{ item.nama }}</div>
                                </td>
                                <td>
                                    <span class="text-muted small" :title="item.deskripsi">
                                        {{ truncateText(item.deskripsi, 60) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge rounded-pill" :class="item.status ? 'bg-success' : 'bg-danger'">
                                        {{ item.status ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-primary me-1" @click="openModal(item)" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" @click="deleteItem(item)" title="Hapus">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="card-footer bg-white border-top py-3" v-if="totalPages > 1">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            Menampilkan {{ (currentPage - 1) * 10 + 1 }} sampai {{ Math.min(currentPage * 10, totalItems) }} dari {{ totalItems }} data
                        </div>
                        <nav aria-label="Page navigation">
                            <ul class="pagination pagination-sm mb-0">
                                <li class="page-item" :class="{ disabled: currentPage === 1 }">
                                    <a class="page-link" href="#" @click.prevent="goToPage(currentPage - 1)">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                </li>
                                <li class="page-item" v-for="page in totalPages" :key="page" :class="{ active: currentPage === page }">
                                    <a class="page-link" href="#" @click.prevent="goToPage(page)">{{ page }}</a>
                                </li>
                                <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                                    <a class="page-link" href="#" @click.prevent="goToPage(currentPage + 1)">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create/Edit Modal -->
        <div class="modal fade" id="unitKerjaModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <!-- Form membungkus modal-body dan modal-footer -->
                    <form @submit.prevent="saveItem">
                        <div class="modal-header border-bottom-0 pb-0">
                            <h5 class="modal-title fw-bold">
                                <i class="fas text-primary me-2" :class="isEditing ? 'fa-building' : 'fa-plus-circle'"></i>
                                {{ isEditing ? 'Edit Unit Kerja' : 'Tambah Unit Kerja Baru' }}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Kode Unit Kerja <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" v-model="form.kode" :class="{ 'is-invalid': errors.kode }" placeholder="Contoh: UK001" required>
                                <div class="invalid-feedback" v-if="errors.kode">{{ errors.kode[0] }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Nama Unit Kerja <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" v-model="form.nama" :class="{ 'is-invalid': errors.nama }" placeholder="Masukkan nama unit kerja" required>
                                <div class="invalid-feedback" v-if="errors.nama">{{ errors.nama[0] }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Deskripsi</label>
                                <textarea class="form-control" v-model="form.deskripsi" :class="{ 'is-invalid': errors.deskripsi }" rows="3" placeholder="Masukkan deskripsi unit kerja"></textarea>
                                <div class="invalid-feedback" v-if="errors.deskripsi">{{ errors.deskripsi[0] }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold d-block">Status</label>
                                <div class="form-check form-switch fs-5 mb-0">
                                    <input class="form-check-input" type="checkbox" role="switch" id="statusSwitch" v-model="form.status">
                                    <label class="form-check-label ms-2 fs-6 mt-1" for="statusSwitch">
                                        <span class="badge" :class="form.status ? 'bg-success' : 'bg-danger'">
                                            {{ form.status ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-top-0 pt-0">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                            <!-- Mengubah button menjadi type submit -->
                            <button type="submit" class="btn btn-primary" :disabled="saving">
                                <i class="fas fa-spinner fa-spin me-1" v-if="saving"></i>
                                <i class="fas fa-save me-1" v-else></i> Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content border-0 shadow">
                    <div class="modal-body text-center p-4">
                        <div class="text-danger mb-3">
                            <i class="fas fa-exclamation-triangle fa-3x"></i>
                        </div>
                        <h5 class="mb-2 fw-bold">Hapus Unit Kerja?</h5>
                        <p class="text-muted mb-4">
                            Anda yakin ingin menghapus unit kerja <br>
                            <strong class="text-dark">{{ deleteTarget?.nama }}</strong>? <br>
                            Tindakan ini tidak dapat dibatalkan.
                        </p>
                        <div class="d-flex justify-content-center gap-2">
                            <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                            <button type="button" class="btn btn-danger px-4" @click="confirmDelete" :disabled="deleting">
                                <i class="fas fa-spinner fa-spin me-1" v-if="deleting"></i>
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
import * as bootstrap from 'bootstrap';

export default {
    name: 'UnitKerjaIndex',
    components: {
        AdminLayout
    },
    data() {
        return {
            unitKerjas: [],
            loading: false,
            saving: false,
            deleting: false,
            searchQuery: '',
            filterStatus: '',
            currentPage: 1,
            totalPages: 1,
            totalItems: 0,
            stats: { total: 0, aktif: 0, nonaktif: 0, terbaru: '' },
            toast: { show: false, message: '', type: 'success' },
            errors: {},
            unitKerjaModal: null,
            deleteModal: null,
            isEditing: false,
            deleteTarget: null,
            form: { id: null, kode: '', nama: '', deskripsi: '', status: true }
        }
    },
    mounted() {
        this.unitKerjaModal = new bootstrap.Modal(document.getElementById('unitKerjaModal'));
        this.deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        this.fetchData(1);
        this.fetchStats();
    },
    methods: {
        showToast(message, type = 'success') {
            this.toast = { show: true, message, type };
            const toastEl = document.getElementById('unitKerjaToast');
            if (toastEl) {
                const bsToast = new bootstrap.Toast(toastEl);
                bsToast.show();
            }
        },
        async fetchData(page = this.currentPage) {
            this.loading = true;
            try {
                const response = await axios.get('/unit-kerjas', {
                    params: {
                        page: page,
                        search: this.searchQuery,
                        status: this.filterStatus,
                        _t: Date.now()
                    }
                });
                
                if (response.data.success) {
                    this.unitKerjas = response.data.data.data;
                    this.currentPage = response.data.data.current_page;
                    this.totalPages = response.data.data.last_page;
                    this.totalItems = response.data.data.total;
                }
            } catch (error) {
                console.error("Error fetching data:", error);
                this.showToast('Gagal mengambil data unit kerja', 'danger');
            } finally {
                this.loading = false;
            }
        },
        async fetchStats() {
            try {
                const response = await axios.get('/unit-kerjas', {
                    params: { per_page: 9999, _t: Date.now() }
                });
                
                if (response.data.success) {
                    const allData = response.data.data.data;
                    this.stats.total = allData.length;
                    this.stats.aktif = allData.filter(item => item.status).length;
                    this.stats.nonaktif = allData.filter(item => !item.status).length;
                    
                    if (allData.length > 0) {
                        const sortedData = [...allData].sort((a, b) => b.id - a.id);
                        this.stats.terbaru = sortedData[0].nama;
                    } else {
                        this.stats.terbaru = '-';
                    }
                }
            } catch (error) {
                console.error("Error fetching stats:", error);
            }
        },
        openModal(item = null) {
            this.errors = {};
            if (item) {
                this.isEditing = true;
                this.form = { 
                    id: item.id, 
                    kode: item.kode, 
                    nama: item.nama, 
                    deskripsi: item.deskripsi || '', 
                    status: Boolean(item.status)
                };
            } else {
                this.isEditing = false;
                this.form = { id: null, kode: '', nama: '', deskripsi: '', status: true };
            }
            this.unitKerjaModal.show();
        },
        async saveItem() {
            this.saving = true;
            this.errors = {};
            
            try {
                const payload = { ...this.form, status: this.form.status ? 1 : 0 };
                
                let response;
                if (this.isEditing) {
                    response = await axios.put(`/unit-kerjas/${this.form.id}`, payload);
                } else {
                    response = await axios.post('/unit-kerjas', payload);
                }
                
                if (response.data.success) {
                    this.unitKerjaModal.hide();
                    this.showToast(`Unit kerja berhasil di${this.isEditing ? 'perbarui' : 'tambahkan'}!`);
                    
                    // Arahkan ke halaman 1 jika tambah baru agar data teratas langsung ter-refresh
                    const targetPage = this.isEditing ? this.currentPage : 1;
                    await this.fetchData(targetPage);
                    await this.fetchStats();
                }
            } catch (error) {
                if (error.response && error.response.status === 422) {
                    this.errors = error.response.data.errors;
                } else {
                    this.showToast('Terjadi kesalahan saat menyimpan data', 'danger');
                }
            } finally {
                this.saving = false;
            }
        },
        deleteItem(item) {
            this.deleteTarget = item;
            this.deleteModal.show();
        },
        async confirmDelete() {
            if (!this.deleteTarget) return;
            
            this.deleting = true;
            try {
                const response = await axios.delete(`/unit-kerjas/${this.deleteTarget.id}`);
                
                if (response.data.success) {
                    this.deleteModal.hide();
                    this.showToast('Unit kerja berhasil dihapus!');
                    
                    if (this.unitKerjas.length === 1 && this.currentPage > 1) {
                        this.currentPage--;
                    }
                    
                    await this.fetchData(this.currentPage);
                    await this.fetchStats();
                }
            } catch (error) {
                this.deleteModal.hide();
                this.showToast('Gagal menghapus unit kerja', 'danger');
            } finally {
                this.deleting = false;
                this.deleteTarget = null;
            }
        },
        goToPage(page) {
            if (page >= 1 && page <= this.totalPages && page !== this.currentPage) {
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
            return text.length > length ? text.substring(0, length) + '...' : text;
        }
    }
}
</script>

<style scoped>
.text-primary {
    color: #996600 !important;
}

.bg-primary {
    background-color: #996600 !important;
}

.btn-primary {
    background-color: #996600;
    border-color: #996600;
}

.btn-primary:hover, .btn-primary:focus, .btn-primary:active {
    background-color: #805500;
    border-color: #805500;
}

.btn-outline-primary {
    color: #996600;
    border-color: #996600;
}

.btn-outline-primary:hover {
    background-color: #996600;
    color: white;
}

.page-link {
    color: #996600;
}

.page-item.active .page-link {
    background-color: #996600;
    border-color: #996600;
    color: white;
}
</style>