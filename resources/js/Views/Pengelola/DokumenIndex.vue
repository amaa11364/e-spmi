<template>
  <AdminLayout pageTitle="Kelola Dokumen" pageIcon="fa-folder-open">
    <div class="dokumen-admin">
      <!-- Header -->
      <div class="page-header">
        <div class="header-info">
          <h2>Manajemen Dokumen</h2>
          <p>Kelola folder dan file dokumen. Atur visibilitas publik untuk ditampilkan di halaman publik.</p>
        </div>
        <div class="header-actions">
          <button class="btn-outline-gold" @click="printData" title="Cetak / Export ke PDF">
            <i class="fas fa-print"></i> Cetak / PDF
          </button>
          <button class="btn-outline-gold" @click="exportCSV" title="Export data ke CSV">
            <i class="fas fa-file-csv"></i> Ekspor CSV
          </button>
          <button class="btn-primary-gold" @click="openFolderModal()">
            <i class="fas fa-folder-plus"></i> Tambah Folder
          </button>
        </div>
      </div>

      <!-- Stats Bar -->
      <div class="stats-row">
        <div class="stat-card">
          <div class="stat-icon folders"><i class="fas fa-folder"></i></div>
          <div><span class="stat-num">{{ folders.length }}</span><span class="stat-lbl">Folder</span></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon files"><i class="fas fa-file-alt"></i></div>
          <div><span class="stat-num">{{ totalFiles }}</span><span class="stat-lbl">File</span></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon public"><i class="fas fa-globe"></i></div>
          <div><span class="stat-num">{{ publicCount }}</span><span class="stat-lbl">Publik</span></div>
        </div>
      </div>

      <!-- Search -->
      <div class="search-bar">
        <i class="fas fa-search"></i>
        <input 
          type="text" 
          v-model="searchQuery" 
          placeholder="Cari folder atau dokumen..."
          @input="fetchFolders"
        >
        <button v-if="searchQuery" class="btn-clear" @click="searchQuery = ''; fetchFolders()">
          <i class="fas fa-times"></i>
        </button>
      </div>

      <!-- Breadcrumb -->
      <div v-if="currentFolder" class="breadcrumb-bar">
        <button class="breadcrumb-item" @click="backToFolders">
          <i class="fas fa-folder"></i> Semua Folder
        </button>
        <i class="fas fa-chevron-right breadcrumb-sep"></i>
        <span class="breadcrumb-current">
          <i class="fas fa-folder-open"></i> {{ currentFolder.nama }}
        </span>
        <button class="btn-upload-file" @click="openFileModal()">
          <i class="fas fa-cloud-upload-alt"></i> Tambah File
        </button>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="loading-state">
        <div class="spinner"></div>
        <p>Memuat data...</p>
      </div>

      <!-- Folder List (when no folder is selected) -->
      <template v-else-if="!currentFolder">
        <div v-if="folders.length === 0" class="empty-state">
          <div class="empty-icon"><i class="fas fa-folder-plus"></i></div>
          <h3>Belum Ada Folder</h3>
          <p>Mulai dengan membuat folder pertama untuk menyimpan dokumen.</p>
          <button class="btn-primary-gold" @click="openFolderModal()">
            <i class="fas fa-plus"></i> Buat Folder Baru
          </button>
        </div>

        <div v-else class="folder-grid">
          <div 
            v-for="folder in folders" 
            :key="folder.id"
            class="folder-card"
          >
            <div class="folder-card-header" @click="openFolder(folder)">
              <div class="folder-icon-box">
                <i class="fas fa-folder"></i>
              </div>
              <div class="folder-card-info">
                <h4>{{ folder.nama }}</h4>
                <p v-if="folder.deskripsi">{{ folder.deskripsi }}</p>
                <div class="folder-meta">
                  <span class="meta-badge">
                    <i class="fas fa-file"></i> {{ folder.files_count || 0 }} File
                  </span>
                  <span class="status-badge" :class="folder.is_public ? 'public' : 'private'">
                    <i :class="folder.is_public ? 'fas fa-globe' : 'fas fa-lock'"></i>
                    {{ folder.is_public ? 'Publik' : 'Privat' }}
                  </span>
                </div>
              </div>
            </div>
            <div class="folder-card-actions">
              <button 
                class="btn-action toggle"
                :class="{ active: folder.is_public }"
                @click.stop="toggleFolderPublic(folder)"
                :title="folder.is_public ? 'Jadikan Privat' : 'Jadikan Publik'"
              >
                <i :class="folder.is_public ? 'fas fa-eye' : 'fas fa-eye-slash'"></i>
              </button>
              <button class="btn-action edit" @click.stop="openFolderModal(folder)" title="Edit Folder">
                <i class="fas fa-pen"></i>
              </button>
              <button class="btn-action delete" @click.stop="confirmDeleteFolder(folder)" title="Hapus Folder">
                <i class="fas fa-trash-alt"></i>
              </button>
            </div>
          </div>
        </div>
      </template>

      <!-- File List (when folder is selected) -->
      <template v-else>
        <div v-if="currentFolder.files && currentFolder.files.length === 0" class="empty-state">
          <div class="empty-icon"><i class="fas fa-cloud-upload-alt"></i></div>
          <h3>Folder Kosong</h3>
          <p>Belum ada file di folder ini. Upload file pertama.</p>
          <button class="btn-primary-gold" @click="openFileModal()">
            <i class="fas fa-upload"></i> Upload File
          </button>
        </div>

        <div v-else class="files-table-wrapper">
          <table class="files-table">
            <thead>
              <tr>
                <th>File</th>
                <th>Ukuran</th>
                <th>Tanggal</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="file in currentFolder.files" :key="file.id">
                <td>
                  <div class="file-cell">
                    <div class="file-type-icon" :class="getFileClass(file.file_type)">
                      <i :class="getFileIcon(file.file_type)"></i>
                    </div>
                    <div>
                      <span class="file-name-text">{{ file.nama }}</span>
                      <span class="file-ext">{{ getFileExt(file.file_path) }}</span>
                    </div>
                  </div>
                </td>
                <td><span class="text-muted">{{ formatSize(file.file_size) }}</span></td>
                <td><span class="text-muted">{{ formatDate(file.created_at) }}</span></td>
                <td>
                  <span class="status-badge" :class="file.is_public ? 'public' : 'private'">
                    <i :class="file.is_public ? 'fas fa-globe' : 'fas fa-lock'"></i>
                    {{ file.is_public ? 'Publik' : 'Privat' }}
                  </span>
                </td>
                <td>
                  <div class="action-btns">
                    <button 
                      class="btn-action toggle" 
                      :class="{ active: file.is_public }"
                      @click="toggleFilePublic(file)"
                      :title="file.is_public ? 'Jadikan Privat' : 'Jadikan Publik'"
                    >
                      <i :class="file.is_public ? 'fas fa-eye' : 'fas fa-eye-slash'"></i>
                    </button>
                    <a 
                      :href="'/api/dokumen/files/' + file.id + '/download'" 
                      class="btn-action download" 
                      target="_blank"
                      title="Download"
                    >
                      <i class="fas fa-download"></i>
                    </a>
                    <button class="btn-action edit" @click="openFileModal(file)" title="Edit File">
                      <i class="fas fa-pen"></i>
                    </button>
                    <button class="btn-action delete" @click="confirmDeleteFile(file)" title="Hapus File">
                      <i class="fas fa-trash-alt"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </template>

      <!-- Toast -->
      <transition name="toast">
        <div v-if="toast.show" class="toast-msg" :class="toast.type">
          <i :class="toast.type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle'"></i>
          {{ toast.message }}
        </div>
      </transition>

      <!-- Folder Modal -->
      <div class="modal-overlay" v-if="showFolderModal" @click.self="showFolderModal = false">
        <div class="modal-box">
          <div class="modal-header">
            <h3>{{ editingFolder ? 'Edit Folder' : 'Tambah Folder Baru' }}</h3>
            <button class="modal-close" @click="showFolderModal = false"><i class="fas fa-times"></i></button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label>Nama Folder <span class="required">*</span></label>
              <input 
                type="text" 
                v-model="folderForm.nama" 
                placeholder="Masukkan nama folder"
                class="form-input"
                :class="{ error: folderErrors.nama }"
              >
              <span v-if="folderErrors.nama" class="error-text">{{ folderErrors.nama[0] }}</span>
            </div>
            <div class="form-group">
              <label>Deskripsi</label>
              <textarea 
                v-model="folderForm.deskripsi" 
                placeholder="Deskripsi folder (opsional)"
                class="form-input"
                rows="3"
              ></textarea>
            </div>
            <div class="form-group">
              <label class="toggle-label">
                <span>Tampilkan di Halaman Publik</span>
                <div class="toggle-switch" :class="{ active: folderForm.is_public }" @click="folderForm.is_public = !folderForm.is_public">
                  <div class="toggle-knob"></div>
                </div>
              </label>
              <p class="help-text">
                <i class="fas fa-info-circle"></i>
                Jika aktif, folder dan semua file di dalamnya akan terlihat di halaman dokumen publik.
              </p>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn-cancel" @click="showFolderModal = false">Batal</button>
            <button class="btn-primary-gold" @click="saveFolder" :disabled="savingFolder">
              <i v-if="savingFolder" class="fas fa-spinner fa-spin"></i>
              <i v-else :class="editingFolder ? 'fas fa-save' : 'fas fa-plus'"></i>
              {{ editingFolder ? 'Simpan Perubahan' : 'Buat Folder' }}
            </button>
          </div>
        </div>
      </div>

      <!-- File Modal -->
      <div class="modal-overlay" v-if="showFileModal" @click.self="showFileModal = false">
        <div class="modal-box">
          <div class="modal-header">
            <h3>{{ editingFile ? 'Edit File' : 'Upload File Baru' }}</h3>
            <button class="modal-close" @click="showFileModal = false"><i class="fas fa-times"></i></button>
          </div>
          <div class="modal-body">
            <div class="form-group" v-if="!editingFile">
              <label>File <span class="required">*</span></label>
              <div 
                class="file-dropzone"
                :class="{ dragover: isDragover, 'has-file': selectedFiles.length > 0, error: fileErrors.file }"
                @dragover.prevent="isDragover = true"
                @dragleave="isDragover = false"
                @drop.prevent="handleDrop"
                @click="$refs.fileInput.click()"
              >
                <input 
                  type="file" 
                  ref="fileInput" 
                  @change="handleFileSelect" 
                  style="display: none;"
                  multiple
                >
                <div v-if="selectedFiles.length === 0" class="dropzone-content">
                  <i class="fas fa-cloud-upload-alt"></i>
                  <p>Klik atau seret beberapa file ke sini</p>
                  <span>Mendukung semua tipe file</span>
                </div>
                <div v-else class="dropzone-files-list">
                  <div class="dropzone-file" v-for="(file, idx) in selectedFiles" :key="idx" style="margin-bottom: 8px;">
                    <i :class="getFileIcon(file.type)" class="preview-icon"></i>
                    <div>
                      <span class="preview-name">{{ file.name }}</span>
                      <span class="preview-size">{{ formatSize(file.size) }}</span>
                    </div>
                    <button class="btn-remove-file" @click.stop="removeFile(idx)">
                      <i class="fas fa-times"></i>
                    </button>
                  </div>
                </div>
              </div>
              <span v-if="fileErrors.file" class="error-text">{{ fileErrors.file[0] }}</span>
            </div>
            <div class="form-group" v-if="selectedFiles.length <= 1">
              <label>Nama File <span class="required">*</span></label>
              <input 
                type="text" 
                v-model="fileForm.nama" 
                placeholder="Masukkan nama file"
                class="form-input"
                :class="{ error: fileErrors.nama }"
              >
              <span v-if="fileErrors.nama" class="error-text">{{ fileErrors.nama[0] }}</span>
            </div>
            <div class="form-group" v-else>
              <p class="text-muted"><i class="fas fa-info-circle"></i> Nama file akan secara otomatis mengikuti nama asli dari setiap file yang diunggah.</p>
            </div>
            <div class="form-group">
              <label class="toggle-label">
                <span>Tampilkan di Halaman Publik</span>
                <div class="toggle-switch" :class="{ active: fileForm.is_public }" @click="fileForm.is_public = !fileForm.is_public">
                  <div class="toggle-knob"></div>
                </div>
              </label>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn-cancel" @click="showFileModal = false">Batal</button>
            <button class="btn-primary-gold" @click="saveFile" :disabled="savingFile">
              <i v-if="savingFile" class="fas fa-spinner fa-spin"></i>
              <i v-else :class="editingFile ? 'fas fa-save' : 'fas fa-upload'"></i>
              {{ editingFile ? 'Simpan Perubahan' : 'Upload File' }}
            </button>
          </div>
        </div>
      </div>

      <!-- Delete Confirm Modal -->
      <div class="modal-overlay" v-if="showDeleteModal" @click.self="showDeleteModal = false">
        <div class="modal-box modal-sm">
          <div class="modal-header danger">
            <h3><i class="fas fa-exclamation-triangle"></i> Konfirmasi Hapus</h3>
            <button class="modal-close" @click="showDeleteModal = false"><i class="fas fa-times"></i></button>
          </div>
          <div class="modal-body">
            <p>{{ deleteMessage }}</p>
            <p class="text-muted" style="font-size: 0.85rem; margin-top: 8px;">Tindakan ini tidak dapat dibatalkan.</p>
          </div>
          <div class="modal-footer">
            <button class="btn-cancel" @click="showDeleteModal = false">Batal</button>
            <button class="btn-danger" @click="executeDelete" :disabled="deleting">
              <i v-if="deleting" class="fas fa-spinner fa-spin"></i>
              <i v-else class="fas fa-trash-alt"></i> Hapus
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

export default {
  name: 'DokumenIndex',
  components: { AdminLayout },
  data() {
    return {
      folders: [],
      currentFolder: null,
      loading: true,
      searchQuery: '',

      // Folder modal
      showFolderModal: false,
      editingFolder: null,
      folderForm: { nama: '', deskripsi: '', is_public: false },
      folderErrors: {},
      savingFolder: false,

      // File modal
      showFileModal: false,
      editingFile: null,
      fileForm: { nama: '', is_public: false },
      fileErrors: {},
      savingFile: false,
      selectedFiles: [],
      isDragover: false,

      // Delete modal
      showDeleteModal: false,
      deleteMessage: '',
      deleteCallback: null,
      deleting: false,

      // Toast
      toast: { show: false, message: '', type: 'success' }
    }
  },
  computed: {
    totalFiles() {
      return this.folders.reduce((sum, f) => sum + (f.files_count || 0), 0)
    },
    publicCount() {
      return this.folders.filter(f => f.is_public).length
    }
  },
  mounted() {
    this.fetchFolders()
  },
  methods: {
    // ===== Data Fetching =====
    async fetchFolders() {
      this.loading = true
      try {
        const params = {}
        if (this.searchQuery) params.search = this.searchQuery
        const res = await axios.get('/dokumen/folders', { params })
        if (res.data.success) {
          this.folders = res.data.data.data || res.data.data
        }
      } catch (err) {
        this.showToast('Gagal memuat data folder', 'error')
      } finally {
        this.loading = false
      }
    },

    // ===== Folder Operations =====
    openFolder(folder) {
      this.currentFolder = folder
    },
    backToFolders() {
      this.currentFolder = null
      this.fetchFolders()
    },
    openFolderModal(folder = null) {
      this.editingFolder = folder
      this.folderErrors = {}
      if (folder) {
        this.folderForm = { nama: folder.nama, deskripsi: folder.deskripsi || '', is_public: folder.is_public }
      } else {
        this.folderForm = { nama: '', deskripsi: '', is_public: false }
      }
      this.showFolderModal = true
    },
    async saveFolder() {
      this.savingFolder = true
      this.folderErrors = {}
      try {
        if (this.editingFolder) {
          const res = await axios.put(`/dokumen/folders/${this.editingFolder.id}`, this.folderForm)
          if (res.data.success) {
            this.showToast('Folder berhasil diperbarui!')
            this.showFolderModal = false
            this.fetchFolders()
          }
        } else {
          const res = await axios.post('/dokumen/folders', this.folderForm)
          if (res.data.success) {
            this.showToast('Folder berhasil dibuat!')
            this.showFolderModal = false
            this.fetchFolders()
          }
        }
      } catch (err) {
        if (err.response?.status === 422) {
          this.folderErrors = err.response.data.errors || {}
        } else {
          this.showToast('Terjadi kesalahan', 'error')
        }
      } finally {
        this.savingFolder = false
      }
    },
    confirmDeleteFolder(folder) {
      this.deleteMessage = `Apakah Anda yakin ingin menghapus folder "${folder.nama}" beserta semua file di dalamnya?`
      this.deleteCallback = async () => {
        try {
          const res = await axios.delete(`/dokumen/folders/${folder.id}`)
          if (res.data.success) {
            this.showToast('Folder berhasil dihapus!')
            this.fetchFolders()
          }
        } catch (err) {
          this.showToast('Gagal menghapus folder', 'error')
        }
      }
      this.showDeleteModal = true
    },
    async toggleFolderPublic(folder) {
      try {
        const res = await axios.patch(`/dokumen/folders/${folder.id}/toggle-public`)
        if (res.data.success) {
          folder.is_public = !folder.is_public
          this.showToast(folder.is_public ? 'Folder dijadikan publik' : 'Folder dijadikan privat')
        }
      } catch (err) {
        this.showToast('Gagal mengubah status', 'error')
      }
    },

    // ===== File Operations =====
    openFileModal(file = null) {
      this.editingFile = file
      this.fileErrors = {}
      this.selectedFiles = []
      if (file) {
        this.fileForm = { nama: file.nama, is_public: file.is_public }
      } else {
        this.fileForm = { nama: '', is_public: false }
      }
      this.showFileModal = true
    },
    handleFileSelect(e) {
      const files = Array.from(e.target.files)
      if (files.length > 0) {
        this.selectedFiles = [...this.selectedFiles, ...files]
        if (this.selectedFiles.length === 1 && !this.fileForm.nama) {
          this.fileForm.nama = this.selectedFiles[0].name.replace(/\.[^/.]+$/, '')
        }
      }
    },
    handleDrop(e) {
      this.isDragover = false
      const files = Array.from(e.dataTransfer.files)
      if (files.length > 0) {
        this.selectedFiles = [...this.selectedFiles, ...files]
        if (this.selectedFiles.length === 1 && !this.fileForm.nama) {
          this.fileForm.nama = this.selectedFiles[0].name.replace(/\.[^/.]+$/, '')
        }
      }
    },
    removeFile(idx) {
      this.selectedFiles.splice(idx, 1)
      if (this.selectedFiles.length === 1 && !this.fileForm.nama) {
        this.fileForm.nama = this.selectedFiles[0].name.replace(/\.[^/.]+$/, '')
      }
    },
    async saveFile() {
  this.savingFile = true
  this.fileErrors = {}
  try {
    if (this.editingFile) {
      const data = { nama: this.fileForm.nama, is_public: this.fileForm.is_public ? 1 : 0 }
      const res = await axios.put(`/dokumen/files/${this.editingFile.id}`, data)
      if (res.data.success) {
        this.showToast('File berhasil diperbarui!')
        this.showFileModal = false
        this.refreshCurrentFolder()
      }
    } else {
      if (this.selectedFiles.length === 0) {
        this.fileErrors = { file: ['Pilih setidaknya 1 file untuk diunggah'] }
        this.savingFile = false
        return
      }

      const formData = new FormData()
      formData.append('is_public', this.fileForm.is_public ? 1 : 0)

      if (this.selectedFiles.length === 1) {
        // Mode Single Upload
        formData.append('file', this.selectedFiles[0])
        formData.append('nama', this.fileForm.nama || this.selectedFiles[0].name)
      } else {
        // Mode Batch / Input Massal Upload
        this.selectedFiles.forEach((file) => {
          formData.append('files[]', file)
        })
      }

      const res = await axios.post(`/dokumen/folders/${this.currentFolder.id}/files`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })

      if (res.data.success) {
        this.showToast(res.data.message || 'File berhasil diunggah!')
        this.showFileModal = false
        this.refreshCurrentFolder()
      }
    }
  } catch (err) {
    if (err.response?.status === 422) {
      this.fileErrors = err.response.data.errors || {}
    } else {
      this.showToast('Terjadi kesalahan saat mengunggah file', 'error')
    }
  } finally {
    this.savingFile = false
  }
},
    confirmDeleteFile(file) {
      this.deleteMessage = `Apakah Anda yakin ingin menghapus file "${file.nama}"?`
      this.deleteCallback = async () => {
        try {
          const res = await axios.delete(`/dokumen/files/${file.id}`)
          if (res.data.success) {
            this.showToast('File berhasil dihapus!')
            this.refreshCurrentFolder()
          }
        } catch (err) {
          this.showToast('Gagal menghapus file', 'error')
        }
      }
      this.showDeleteModal = true
    },
    async toggleFilePublic(file) {
      try {
        const res = await axios.patch(`/dokumen/files/${file.id}/toggle-public`)
        if (res.data.success) {
          file.is_public = !file.is_public
          this.showToast(file.is_public ? 'File dijadikan publik' : 'File dijadikan privat')
        }
      } catch (err) {
        this.showToast('Gagal mengubah status', 'error')
      }
    },
    async refreshCurrentFolder() {
      try {
        const res = await axios.get('/dokumen/folders', { params: { search: '' } })
        if (res.data.success) {
          const allFolders = res.data.data.data || res.data.data
          this.folders = allFolders
          if (this.currentFolder) {
            const updated = allFolders.find(f => f.id === this.currentFolder.id)
            if (updated) this.currentFolder = updated
          }
        }
      } catch (err) {
        console.error(err)
      }
    },

    // ===== Delete Execute =====
    async executeDelete() {
      this.deleting = true
      if (this.deleteCallback) await this.deleteCallback()
      this.deleting = false
      this.showDeleteModal = false
    },

    // ===== Export & Print =====
    exportCSV() {
      let csvContent = "data:text/csv;charset=utf-8,";
      
      if (this.currentFolder) {
        // Export files
        csvContent += "Tipe,Nama File,Ukuran,Status,Tanggal Dibuat\n";
        if (this.currentFolder.files) {
          this.currentFolder.files.forEach(f => {
            const nama = `"${f.nama.replace(/"/g, '""')}"`;
            const ukuran = `"${this.formatSize(f.file_size)}"`;
            const status = f.is_public ? "Publik" : "Privat";
            const tanggal = `"${this.formatDate(f.created_at)}"`;
            csvContent += `File,${nama},${ukuran},${status},${tanggal}\n`;
          });
        }
      } else {
        // Export folders
        csvContent += "Tipe,Nama Folder,Deskripsi,Jumlah File,Status,Tanggal Dibuat\n";
        if (this.folders) {
          this.folders.forEach(f => {
            const nama = `"${f.nama.replace(/"/g, '""')}"`;
            const deskripsi = `"${(f.deskripsi || '').replace(/"/g, '""')}"`;
            const jumlahFile = f.files_count || 0;
            const status = f.is_public ? "Publik" : "Privat";
            const tanggal = `"${this.formatDate(f.created_at)}"`;
            csvContent += `Folder,${nama},${deskripsi},${jumlahFile},${status},${tanggal}\n`;
          });
        }
      }

      const encodedUri = encodeURI(csvContent);
      const link = document.createElement("a");
      link.setAttribute("href", encodedUri);
      link.setAttribute("download", this.currentFolder ? `export_files_${this.currentFolder.nama}.csv` : "export_folders.csv");
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
      this.showToast('Data berhasil diekspor ke CSV!');
    },
    printData() {
      window.print();
    },

    // ===== Helpers =====
    getFileIcon(type) {
      if (!type) return 'fas fa-file'
      if (type.includes('pdf')) return 'fas fa-file-pdf'
      if (type.includes('word') || type.includes('document') || type.includes('msword')) return 'fas fa-file-word'
      if (type.includes('excel') || type.includes('spreadsheet') || type.includes('ms-excel')) return 'fas fa-file-excel'
      if (type.includes('powerpoint') || type.includes('presentation') || type.includes('ms-powerpoint')) return 'fas fa-file-powerpoint'
      if (type.includes('image')) return 'fas fa-file-image'
      if (type.includes('zip') || type.includes('rar') || type.includes('archive') || type.includes('compressed')) return 'fas fa-file-archive'
      if (type.includes('video')) return 'fas fa-file-video'
      if (type.includes('audio')) return 'fas fa-file-audio'
      if (type.includes('text') || type.includes('csv')) return 'fas fa-file-alt'
      return 'fas fa-file'
    },
    getFileClass(type) {
      if (!type) return 'ft-default'
      if (type.includes('pdf')) return 'ft-pdf'
      if (type.includes('word') || type.includes('document') || type.includes('msword')) return 'ft-word'
      if (type.includes('excel') || type.includes('spreadsheet') || type.includes('ms-excel')) return 'ft-excel'
      if (type.includes('powerpoint') || type.includes('presentation') || type.includes('ms-powerpoint')) return 'ft-ppt'
      if (type.includes('image')) return 'ft-image'
      if (type.includes('zip') || type.includes('rar') || type.includes('archive') || type.includes('compressed')) return 'ft-archive'
      return 'ft-default'
    },
    getFileExt(path) {
      if (!path) return ''
      const ext = path.split('.').pop()
      return ext ? '.' + ext.toUpperCase() : ''
    },
    formatSize(bytes) {
      if (!bytes) return '0 B'
      const k = 1024
      const sizes = ['B', 'KB', 'MB', 'GB']
      const i = Math.floor(Math.log(bytes) / Math.log(k))
      return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i]
    },
    formatDate(d) {
      if (!d) return '-'
      return new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
    },
    showToast(message, type = 'success') {
      this.toast = { show: true, message, type }
      setTimeout(() => { this.toast.show = false }, 3500)
    }
  }
}
</script>

<style scoped>
.dokumen-admin { max-width: 100%; }

/* ===== HEADER ===== */
.page-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 24px;
  gap: 16px;
  flex-wrap: wrap;
}
.header-info h2 {
  font-size: 1.5rem;
  font-weight: 700;
  color: #1e293b;
  margin: 0 0 4px;
}
.header-info p {
  color: #64748b;
  font-size: 0.9rem;
  margin: 0;
}
.header-actions {
  display: flex;
  gap: 10px;
  align-items: center;
}
.btn-outline-gold {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 10px 18px;
  background: white;
  color: #996600;
  border: 1px solid #996600;
  border-radius: 10px;
  font-size: 0.9rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  white-space: nowrap;
}
.btn-outline-gold:hover {
  background: #fffbeb;
  color: #b37700;
}
.btn-primary-gold {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 10px 22px;
  background: linear-gradient(135deg, #996600, #b37700);
  color: white;
  border: none;
  border-radius: 10px;
  font-size: 0.9rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  white-space: nowrap;
}
.btn-primary-gold:hover:not(:disabled) {
  background: linear-gradient(135deg, #7a5200, #996600);
  transform: translateY(-1px);
  box-shadow: 0 4px 14px rgba(153, 102, 0, 0.3);
}
.btn-primary-gold:disabled { opacity: 0.6; cursor: not-allowed; }

/* ===== STATS ===== */
.stats-row {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
  gap: 16px;
  margin-bottom: 24px;
}
.stat-card {
  background: white;
  border-radius: 12px;
  padding: 16px 20px;
  display: flex;
  align-items: center;
  gap: 14px;
  box-shadow: 0 1px 4px rgba(0,0,0,0.05);
  border: 1px solid #f1f5f9;
}
.stat-icon {
  width: 44px; height: 44px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.1rem;
}
.stat-icon.folders { background: #fef3c7; color: #92400e; }
.stat-icon.files { background: #dbeafe; color: #1d4ed8; }
.stat-icon.public { background: #d1fae5; color: #059669; }
.stat-num { font-size: 1.4rem; font-weight: 700; color: #1e293b; display: block; }
.stat-lbl { font-size: 0.78rem; color: #94a3b8; }

/* ===== SEARCH ===== */
.search-bar {
  position: relative;
  margin-bottom: 20px;
}
.search-bar i.fa-search {
  position: absolute;
  left: 16px; top: 50%;
  transform: translateY(-50%);
  color: #94a3b8;
}
.search-bar input {
  width: 100%;
  padding: 12px 16px 12px 44px;
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  font-size: 0.9rem;
  transition: all 0.2s;
  background: white;
}
.search-bar input:focus {
  outline: none;
  border-color: #cc9900;
  box-shadow: 0 0 0 3px rgba(204, 153, 0, 0.1);
}
.btn-clear {
  position: absolute;
  right: 12px; top: 50%;
  transform: translateY(-50%);
  background: none; border: none;
  color: #94a3b8; cursor: pointer;
  font-size: 0.9rem;
}

/* ===== BREADCRUMB ===== */
.breadcrumb-bar {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 20px;
  padding: 12px 16px;
  background: white;
  border-radius: 10px;
  border: 1px solid #e2e8f0;
  flex-wrap: wrap;
}
.breadcrumb-item {
  background: none; border: none;
  color: #996600; cursor: pointer;
  font-size: 0.9rem; font-weight: 500;
  display: flex; align-items: center; gap: 6px;
  padding: 4px 8px; border-radius: 6px;
  transition: all 0.2s;
}
.breadcrumb-item:hover { background: #fff7ed; }
.breadcrumb-sep { color: #cbd5e1; font-size: 0.7rem; }
.breadcrumb-current {
  color: #475569;
  font-size: 0.9rem; font-weight: 600;
  display: flex; align-items: center; gap: 6px;
}
.btn-upload-file {
  margin-left: auto;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 8px 16px;
  background: linear-gradient(135deg, #996600, #b37700);
  color: white; border: none; border-radius: 8px;
  font-size: 0.82rem; font-weight: 600;
  cursor: pointer; transition: all 0.2s;
}
.btn-upload-file:hover {
  background: linear-gradient(135deg, #7a5200, #996600);
  transform: translateY(-1px);
}

/* ===== LOADING ===== */
.loading-state {
  text-align: center;
  padding: 60px 20px;
}
.spinner {
  width: 40px; height: 40px;
  border: 3px solid #e2e8f0;
  border-top: 3px solid #996600;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
  margin: 0 auto 14px;
}
@keyframes spin { to { transform: rotate(360deg); } }
.loading-state p { color: #94a3b8; }

/* ===== EMPTY ===== */
.empty-state {
  text-align: center;
  padding: 60px 20px;
  background: white;
  border-radius: 16px;
  border: 2px dashed #e2e8f0;
}
.empty-icon {
  width: 70px; height: 70px;
  border-radius: 16px;
  background: #fef3c7;
  display: flex; align-items: center; justify-content: center;
  margin: 0 auto 16px;
}
.empty-icon i { font-size: 1.6rem; color: #92400e; }
.empty-state h3 { font-size: 1.15rem; color: #334155; margin-bottom: 6px; }
.empty-state p { color: #94a3b8; font-size: 0.9rem; margin-bottom: 20px; }

/* ===== FOLDER GRID ===== */
.folder-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
  gap: 16px;
}
.folder-card {
  background: white;
  border-radius: 14px;
  border: 1px solid #e2e8f0;
  overflow: hidden;
  transition: all 0.25s;
  box-shadow: 0 1px 4px rgba(0,0,0,0.04);
}
.folder-card:hover {
  box-shadow: 0 6px 20px rgba(0,0,0,0.08);
  border-color: #cbd5e1;
}
.folder-card-header {
  display: flex;
  align-items: flex-start;
  gap: 14px;
  padding: 20px;
  cursor: pointer;
  transition: background 0.2s;
}
.folder-card-header:hover { background: #fffbeb; }
.folder-icon-box {
  width: 48px; height: 48px;
  border-radius: 12px;
  background: linear-gradient(135deg, #fef3c7, #fde68a);
  display: flex;
  align-items: center; justify-content: center;
  flex-shrink: 0;
}
.folder-icon-box i { font-size: 1.2rem; color: #92400e; }
.folder-card-info { flex: 1; min-width: 0; }
.folder-card-info h4 {
  font-size: 1rem; font-weight: 600; color: #1e293b; margin: 0 0 4px;
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.folder-card-info p {
  font-size: 0.82rem; color: #64748b; margin: 0 0 8px;
  display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;
  overflow: hidden;
}
.folder-meta {
  display: flex; align-items: center; gap: 8px; flex-wrap: wrap;
}
.meta-badge {
  font-size: 0.75rem; color: #94a3b8;
  display: inline-flex; align-items: center; gap: 4px;
  background: #f1f5f9; padding: 3px 8px; border-radius: 6px;
}
.status-badge {
  font-size: 0.72rem; font-weight: 600;
  display: inline-flex; align-items: center; gap: 4px;
  padding: 3px 10px; border-radius: 6px;
}
.status-badge.public { background: #d1fae5; color: #059669; }
.status-badge.private { background: #f1f5f9; color: #64748b; }

/* Folder Actions */
.folder-card-actions {
  display: flex;
  justify-content: flex-end;
  gap: 6px;
  padding: 0 20px 14px;
}
.btn-action {
  width: 34px; height: 34px;
  border-radius: 8px;
  border: 1px solid #e2e8f0;
  background: white;
  display: flex; align-items: center; justify-content: center;
  cursor: pointer;
  transition: all 0.2s;
  font-size: 0.82rem;
  text-decoration: none;
  color: #64748b;
}
.btn-action.toggle { color: #94a3b8; }
.btn-action.toggle.active { color: #059669; background: #ecfdf5; border-color: #a7f3d0; }
.btn-action.toggle:hover { color: #059669; background: #f0fdf4; }
.btn-action.edit:hover { color: #2563eb; background: #eff6ff; border-color: #bfdbfe; }
.btn-action.delete:hover { color: #dc2626; background: #fef2f2; border-color: #fecaca; }
.btn-action.download:hover { color: #996600; background: #fffbeb; border-color: #fde68a; }

/* ===== FILES TABLE ===== */
.files-table-wrapper {
  background: white;
  border-radius: 14px;
  border: 1px solid #e2e8f0;
  overflow-x: auto;
}
.files-table {
  width: 100%;
  border-collapse: collapse;
}
.files-table th {
  text-align: left;
  padding: 14px 20px;
  font-size: 0.78rem;
  font-weight: 600;
  color: #94a3b8;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  border-bottom: 1px solid #f1f5f9;
  white-space: nowrap;
}
.files-table td {
  padding: 14px 20px;
  border-bottom: 1px solid #f8fafc;
  vertical-align: middle;
}
.files-table tr:hover td { background: #fefce8; }
.files-table tr:last-child td { border-bottom: none; }
.file-cell {
  display: flex; align-items: center; gap: 12px;
}
.file-type-icon {
  width: 38px; height: 38px;
  border-radius: 8px;
  display: flex; align-items: center; justify-content: center;
  font-size: 1rem;
  flex-shrink: 0;
}
.ft-pdf { background: #fef2f2; color: #dc2626; }
.ft-word { background: #eff6ff; color: #2563eb; }
.ft-excel { background: #f0fdf4; color: #16a34a; }
.ft-ppt { background: #fff7ed; color: #ea580c; }
.ft-image { background: #faf5ff; color: #9333ea; }
.ft-archive { background: #fefce8; color: #ca8a04; }
.ft-default { background: #f1f5f9; color: #64748b; }
.file-name-text {
  font-size: 0.9rem; font-weight: 500; color: #1e293b;
  display: block; max-width: 250px;
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.file-ext {
  font-size: 0.72rem; color: #94a3b8;
  display: block; margin-top: 2px;
}
.text-muted { color: #94a3b8; font-size: 0.85rem; }
.action-btns { display: flex; gap: 6px; }

/* ===== TOAST ===== */
.toast-msg {
  position: fixed;
  bottom: 24px; right: 24px;
  padding: 14px 22px;
  border-radius: 12px;
  font-size: 0.9rem;
  font-weight: 500;
  display: flex; align-items: center; gap: 10px;
  box-shadow: 0 8px 30px rgba(0,0,0,0.15);
  z-index: 9999;
  animation: slideUp 0.3s ease;
}
.toast-msg.success { background: #065f46; color: white; }
.toast-msg.error { background: #991b1b; color: white; }
.toast-enter-active { animation: slideUp 0.3s ease; }
.toast-leave-active { animation: slideUp 0.3s ease reverse; }
@keyframes slideUp {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

/* ===== MODAL ===== */
.modal-overlay {
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(0,0,0,0.5);
  display: flex; align-items: center; justify-content: center;
  z-index: 9990;
  padding: 20px;
  backdrop-filter: blur(4px);
  animation: fadeIn 0.2s ease;
}
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
.modal-box {
  background: white;
  border-radius: 16px;
  width: 100%;
  max-width: 520px;
  box-shadow: 0 20px 60px rgba(0,0,0,0.2);
  animation: modalSlide 0.3s ease;
  max-height: 90vh;
  overflow-y: auto;
}
.modal-sm { max-width: 420px; }
@keyframes modalSlide {
  from { opacity: 0; transform: translateY(-20px); }
  to { opacity: 1; transform: translateY(0); }
}
.modal-header {
  padding: 20px 24px;
  border-bottom: 1px solid #f1f5f9;
  display: flex; justify-content: space-between; align-items: center;
}
.modal-header h3 {
  font-size: 1.1rem; font-weight: 600; color: #1e293b; margin: 0;
  display: flex; align-items: center; gap: 8px;
}
.modal-header.danger h3 { color: #dc2626; }
.modal-close {
  background: none; border: none;
  color: #94a3b8; font-size: 1.1rem; cursor: pointer;
  padding: 4px; border-radius: 6px; transition: all 0.2s;
}
.modal-close:hover { background: #f1f5f9; color: #1e293b; }
.modal-body { padding: 24px; }
.modal-footer {
  padding: 16px 24px;
  border-top: 1px solid #f1f5f9;
  display: flex; justify-content: flex-end; gap: 10px;
}

/* Form elements */
.form-group { margin-bottom: 20px; }
.form-group label {
  display: block; font-size: 0.85rem; font-weight: 600;
  color: #374151; margin-bottom: 6px;
}
.required { color: #dc2626; }
.form-input {
  width: 100%;
  padding: 10px 14px;
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  font-size: 0.9rem;
  transition: all 0.2s;
  background: white;
  font-family: inherit;
}
.form-input:focus {
  outline: none;
  border-color: #cc9900;
  box-shadow: 0 0 0 3px rgba(204, 153, 0, 0.1);
}
.form-input.error { border-color: #dc2626; }
.error-text { color: #dc2626; font-size: 0.78rem; margin-top: 4px; display: block; }
.help-text {
  font-size: 0.8rem; color: #94a3b8; margin-top: 6px;
  display: flex; align-items: center; gap: 6px;
}

/* Toggle */
.toggle-label {
  display: flex !important;
  justify-content: space-between;
  align-items: center;
}
.toggle-switch {
  width: 48px; height: 26px;
  background: #e2e8f0;
  border-radius: 13px;
  position: relative;
  cursor: pointer;
  transition: background 0.3s;
  flex-shrink: 0;
}
.toggle-switch.active { background: #059669; }
.toggle-knob {
  width: 20px; height: 20px;
  background: white;
  border-radius: 50%;
  position: absolute;
  top: 3px; left: 3px;
  transition: transform 0.3s;
  box-shadow: 0 1px 3px rgba(0,0,0,0.2);
}
.toggle-switch.active .toggle-knob { transform: translateX(22px); }

/* File Dropzone */
.file-dropzone {
  border: 2px dashed #d1d5db;
  border-radius: 12px;
  padding: 30px 20px;
  text-align: center;
  cursor: pointer;
  transition: all 0.3s;
  background: #fafbfc;
}
.file-dropzone:hover,
.file-dropzone.dragover {
  border-color: #cc9900;
  background: #fffbeb;
}
.file-dropzone.has-file { border-color: #059669; background: #f0fdf4; }
.file-dropzone.error { border-color: #dc2626; background: #fef2f2; }
.dropzone-content i { font-size: 2rem; color: #94a3b8; margin-bottom: 10px; }
.dropzone-content p { font-size: 0.9rem; color: #475569; margin: 4px 0; }
.dropzone-content span { font-size: 0.78rem; color: #94a3b8; }
.dropzone-file {
  display: flex; align-items: center; gap: 12px;
  text-align: left;
}
.preview-icon { font-size: 1.8rem; color: #059669; }
.preview-name { font-size: 0.9rem; font-weight: 500; color: #1e293b; display: block; }
.preview-size { font-size: 0.78rem; color: #94a3b8; }
.btn-remove-file {
  margin-left: auto;
  background: none; border: none;
  color: #dc2626; cursor: pointer;
  font-size: 1rem; padding: 4px;
}

/* Cancel / Danger buttons */
.btn-cancel {
  padding: 10px 20px;
  background: #f1f5f9; color: #475569;
  border: 1px solid #e2e8f0; border-radius: 10px;
  font-size: 0.9rem; font-weight: 500;
  cursor: pointer; transition: all 0.2s;
}
.btn-cancel:hover { background: #e2e8f0; }
.btn-danger {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 10px 20px;
  background: #dc2626; color: white;
  border: none; border-radius: 10px;
  font-size: 0.9rem; font-weight: 600;
  cursor: pointer; transition: all 0.2s;
}
.btn-danger:hover:not(:disabled) { background: #b91c1c; }
.btn-danger:disabled { opacity: 0.6; cursor: not-allowed; }

/* ===== RESPONSIVE ===== */
@media (max-width: 768px) {
  .page-header { flex-direction: column; }
  .folder-grid { grid-template-columns: 1fr; }
  .stats-row { grid-template-columns: repeat(3, 1fr); }
  .files-table th:nth-child(3),
  .files-table td:nth-child(3) { display: none; }
}
@media (max-width: 480px) {
  .stats-row { grid-template-columns: 1fr; }
  .breadcrumb-bar { flex-direction: column; align-items: flex-start; }
  .btn-upload-file { margin-left: 0; width: 100%; justify-content: center; }
}

@media print {
  .admin-layout > .sidebar,
  .admin-layout > .topbar,
  .search-bar,
  .header-actions,
  .btn-upload-file,
  .folder-card-actions,
  .action-btns,
  .stats-row {
    display: none !important;
  }
  
  .dokumen-admin,
  .main-content {
    margin: 0 !important;
    padding: 0 !important;
    width: 100% !important;
  }
  
  .folder-grid {
    grid-template-columns: repeat(2, 1fr) !important;
  }
  
  .folder-card {
    page-break-inside: avoid;
    border: 1px solid #ccc !important;
    box-shadow: none !important;
  }

  .files-table th, .files-table td {
    border: 1px solid #ddd !important;
  }
}
</style>
