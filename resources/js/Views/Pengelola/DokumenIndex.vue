<template>
  <AdminLayout pageTitle="Kelola Dokumen" pageIcon="fa-folder-open">
    <div class="dokumen-admin">
      <!-- Header -->
      <div class="page-header">
        <div class="header-info">
          <!-- Breadcrumb Global Dinamis -->
          <div class="global-breadcrumb">
            <span @click="navigateToRoot" class="breadcrumb-link">
              <i class="fas fa-home"></i> Manajemen
            </span>
            <i class="fas fa-chevron-right separator"></i>
            <span
              v-if="!currentFolder"
              class="active"
            >Kelola Dokumen</span>
            <span
              v-else
              @click="navigateToRoot"
              class="breadcrumb-link"
            >Kelola Dokumen</span>

            <!-- Ancestor trail -->
            <template v-if="currentFolder">
              <template v-for="(ancestor, idx) in folderBreadcrumbs" :key="'bc-' + idx">
                <i class="fas fa-chevron-right separator"></i>
                <span
                  v-if="idx === folderBreadcrumbs.length - 1"
                  class="active"
                >
                  <i class="fas fa-folder-open"></i> {{ ancestor.nama }}
                </span>
                <span
                  v-else
                  @click="navigateToAncestor(idx)"
                  class="breadcrumb-link"
                >
                  <i class="fas fa-folder"></i> {{ ancestor.nama }}
                </span>
              </template>
            </template>
          </div>
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
          <button v-if="currentFolder" class="btn-primary-gold" @click="openFileModal()">
            <i class="fas fa-cloud-upload-alt"></i> Tambah File
          </button>
        </div>
      </div>

      <!-- Stats Bar (Dinamis & Real-time) -->
      <div class="stats-row">
        <div class="stat-card">
          <div class="stat-icon folders"><i class="fas fa-folder"></i></div>
          <div class="stat-info"><span class="stat-num">{{ totalFoldersCount }}</span><span class="stat-lbl">Folder</span></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon files"><i class="fas fa-file-alt"></i></div>
          <div class="stat-info"><span class="stat-num">{{ totalFiles }}</span><span class="stat-lbl">File</span></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon public"><i class="fas fa-globe"></i></div>
          <div class="stat-info"><span class="stat-num">{{ publicCount }}</span><span class="stat-lbl">Publik</span></div>
        </div>
      </div>

      <!-- Toolbar -->
      <div class="toolbar-section">
        <!-- Search -->
        <div class="search-bar-wrapper">
          <div class="search-bar inline-search">
            <i class="fas fa-search"></i>
            <input 
              type="text" 
              v-model="searchQuery" 
              placeholder="Cari folder atau dokumen..."
              @input="handleSearchInput"
              @focus="showSearchDropdown = true"
            >
            <button v-if="searchQuery" class="btn-clear" @click="clearSearch">
              <i class="fas fa-times"></i>
            </button>

            <!-- Autocomplete Dropdown -->
            <div v-if="showSearchDropdown && searchQuery && (isSearching || searchResults.folders.length > 0 || searchResults.files.length > 0)" class="search-dropdown-overlay" @click.stop>
              <div v-if="isSearching" class="search-loading">
                <i class="fas fa-spinner fa-spin"></i> Mencari...
              </div>
              <template v-else>
                <div v-if="searchResults.folders.length > 0" class="search-group">
                  <div class="search-group-title">Folder</div>
                  <div class="search-item" v-for="folder in searchResults.folders" :key="'sf-'+folder.id" @click="openFolderFromSearch(folder)">
                    <div class="search-item-icon"><i class="fas fa-folder"></i></div>
                    <div class="search-item-info">
                      <span class="search-item-name">{{ folder.nama }}</span>
                    </div>
                  </div>
                </div>
                
                <div v-if="searchResults.files.length > 0" class="search-group">
                  <div class="search-group-title">File Dokumen</div>
                  <div class="search-item" v-for="file in searchResults.files" :key="'sfi-'+file.id" @click="downloadFileFromSearch(file)">
                    <div class="search-item-icon" :class="getFileClass(file.file_type)"><i :class="getFileIcon(file.file_type)"></i></div>
                    <div class="search-item-info">
                      <span class="search-item-name">{{ file.nama }}</span>
                      <span class="search-item-meta" v-if="file.folder">di dalam {{ file.folder.nama }}</span>
                    </div>
                  </div>
                </div>

                <div v-if="searchResults.folders.length === 0 && searchResults.files.length === 0" class="search-empty">
                  Tidak ada hasil yang ditemukan untuk "{{ searchQuery }}"
                </div>
              </template>
            </div>
          </div>
        </div>

        <div class="toolbar-actions">
          <!-- Filter IKU -->
          <div class="filter-box integrated-filter" v-if="availableIkus.length > 0">
            <i class="fas fa-tags filter-icon-left"></i>
            <select v-model="filterIku" class="filter-select-clean">
              <option value="all">Semua Kategori</option>
              <option v-for="iku in availableIkus" :key="iku" :value="iku">{{ iku }}</option>
            </select>
          </div>

          <!-- Filter Status -->
          <div class="filter-box integrated-filter">
            <i class="fas fa-filter filter-icon-left"></i>
            <select v-model="filterStatus" class="filter-select-clean">
              <option value="all">Semua Status</option>
              <option value="public">Publik</option>
              <option value="private">Privat</option>
            </select>
          </div>

          <!-- View Controls -->
          <div class="view-controls">
            <div class="view-toggle">
              <button :class="{ active: viewMode === 'table' }" @click="viewMode = 'table'" title="Tampilan Tabel">
                <i class="fas fa-list"></i>
              </button>
              <button :class="{ active: viewMode === 'grid' }" @click="viewMode = 'grid'" title="Tampilan Grid">
                <i class="fas fa-th-large"></i>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="loading-state">
        <div class="spinner"></div>
        <p>Memuat data...</p>
      </div>

      <!-- Folder List Root -->
      <template v-else-if="!currentFolder">
        <div v-if="folders.length === 0" class="empty-state">
          <div class="empty-icon"><i class="fas fa-folder-plus"></i></div>
          <h3>Belum Ada Folder</h3>
          <p>Mulai dengan membuat folder pertama untuk menyimpan dokumen.</p>
          <button class="btn-primary-gold" @click="openFolderModal()">
            <i class="fas fa-plus"></i> Buat Folder Baru
          </button>
        </div>

        <div v-else>
          <!-- Table View -->
          <div v-if="viewMode === 'table'" class="folders-table-wrapper">
            <table class="folders-table">
              <thead>
                <tr>
                  <th @click="toggleSort('nama')" class="sortable">Nama Folder <i :class="getSortIcon('nama')"></i></th>
                  <th @click="toggleSort('kategori_iku')" class="sortable">IKU <i :class="getSortIcon('kategori_iku')"></i></th>
                  <th @click="toggleSort('deskripsi')" class="sortable">Desk File <i :class="getSortIcon('deskripsi')"></i></th>
                  <th @click="toggleSort('files_count')" class="sortable">Folder <i :class="getSortIcon('files_count')"></i></th>
                  <th @click="toggleSort('updated_at')" class="sortable">User Update &amp; Tanggal <i :class="getSortIcon('updated_at')"></i></th>
                  <th @click="toggleSort('is_public')" class="sortable">Status Akses <i :class="getSortIcon('is_public')"></i></th>
                  <th style="text-align:center;">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="folder in filteredAndSortedFolders" :key="folder.id" @click="openFolder(folder)" class="clickable-row">
                  <td>
                    <div class="folder-cell">
                      <div class="folder-icon-sm"><i class="fas fa-folder"></i></div>
                      <div class="folder-info-sm">
                        <template v-if="inlineEditId === folder.id && inlineEditType === 'folder'">
                          <input type="text" v-model="inlineEditValue" class="inline-input" @click.stop @keyup.enter="saveInlineEdit(folder, 'folder')" @keyup.esc="cancelInlineEdit" @blur="saveInlineEdit(folder, 'folder')">
                        </template>
                        <template v-else>
                          <div class="inline-edit-wrap">
                            <span class="folder-name-sm">{{ folder.nama }}</span>
                            <button v-if="userCanEdit" class="btn-inline-edit" @click.stop="startInlineEdit(folder, 'folder')" title="Edit nama"><i class="fas fa-pen"></i></button>
                          </div>
                        </template>
                      </div>
                    </div>
                  </td>
                  <td>
                    <span class="badge kode-badge" v-if="getIkuFromFolderName(folder.nama)">{{ getIkuFromFolderName(folder.nama) }}</span>
                    <span class="text-muted" v-else>-</span>
                  </td>
                  <td>
                    <template v-if="inlineEditId === folder.id && inlineEditType === 'folder_desc'">
                      <input type="text" v-model="inlineEditValue" class="inline-input" @click.stop @keyup.enter="saveInlineEditDesc(folder, 'folder')" @keyup.esc="cancelInlineEdit" @blur="saveInlineEditDesc(folder, 'folder')">
                    </template>
                    <template v-else>
                      <div class="inline-edit-wrap">
                        <span class="text-muted desk-text">{{ folder.deskripsi || '-' }}</span>
                        <button v-if="userCanEdit" class="btn-inline-edit" @click.stop="startInlineEdit(folder, 'folder_desc')" title="Edit deskripsi"><i class="fas fa-pen"></i></button>
                      </div>
                    </template>
                  </td>
                  <td>
                    <span class="text-muted">{{ getFolderContentText(folder) }}</span>
                  </td>
                  <td>
                    <div class="user-update-info">
                      <span class="text-muted user-update-name" v-if="getLastUpdater(folder)">
                        <i class="fas fa-user-circle"></i> {{ getLastUpdater(folder) }}
                      </span>
                      <span class="text-muted">{{ formatDate(folder.updated_at || folder.created_at) }}</span>
                    </div>
                  </td>
                  <td>
                    <span class="status-badge status-badge-clickable" :class="getFolderAccessClass(folder)" @click.stop="toggleFolderPublic(folder)" :title="folder.is_public ? 'Klik untuk ubah ke Privat' : 'Klik untuk ubah ke Publik'">
                      <i :class="getFolderAccessIcon(folder)"></i>
                      {{ getFolderAccessLabel(folder) }}
                    </span>
                  </td>
                  <td @click.stop style="text-align:center;">
                    <div class="table-actions">
                      <button class="btn-action view" @click.stop="openFolder(folder)" title="Lihat Detail Folder">
                        <i class="fas fa-eye"></i>
                      </button>
                      <button v-if="userCanDelete" class="btn-action delete" @click.stop="confirmDeleteFolder(folder)" title="Hapus Folder">
                        <i class="fas fa-trash-alt"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Grid View -->
          <div v-else class="folder-grid">
            <div 
              v-for="folder in filteredAndSortedFolders" 
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
                    <span class="meta-badge">{{ getFolderContentText(folder) }}</span>
                    <span class="status-badge status-badge-clickable" :class="getFolderAccessClass(folder)" @click.stop="toggleFolderPublic(folder)" :title="folder.is_public ? 'Klik untuk ubah ke Privat' : 'Klik untuk ubah ke Publik'">
                      <i :class="getFolderAccessIcon(folder)"></i>
                      {{ getFolderAccessLabel(folder) }}
                    </span>
                  </div>
                </div>
              </div>
              <div class="folder-card-actions">
                <button class="btn-action view" @click.stop="openFolder(folder)" title="Lihat Detail Folder">
                  <i class="fas fa-eye"></i>
                </button>
                <button v-if="userCanDelete" class="btn-action delete" @click.stop="confirmDeleteFolder(folder)" title="Hapus Folder">
                  <i class="fas fa-trash-alt"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
      </template>

      <!-- Subfolders & File List (In Subfolder View) -->
      <template v-else>
        <!-- Sub-folders Section -->
        <div v-if="currentFolder.children && currentFolder.children.length > 0" class="subfolders-section" style="margin-bottom: 24px;">
          <h4 style="margin-bottom: 12px; color: #1e293b; font-size: 1.05rem;"><i class="fas fa-folder-open" style="color: #92400e; margin-right: 6px;"></i> Sub-Folder</h4>
          
          <div v-if="viewMode === 'grid'" class="folder-grid">
            <div 
              v-for="subFolder in filteredAndSortedChildren" 
              :key="subFolder.id"
              class="folder-card"
            >
              <div class="folder-card-header" @click="openFolder(subFolder)">
                <div class="folder-icon-box">
                  <i class="fas fa-folder"></i>
                </div>
                <div class="folder-card-info">
                  <h4>{{ subFolder.nama }}</h4>
                  <p v-if="subFolder.deskripsi">{{ subFolder.deskripsi }}</p>
                  <div class="folder-meta">
                    <span class="meta-badge">{{ getFolderContentText(subFolder) }}</span>
                    <span class="status-badge status-badge-clickable" :class="subFolder.is_public ? 'public' : 'private'" @click.stop="toggleFolderPublic(subFolder)" :title="subFolder.is_public ? 'Klik untuk ubah ke Privat' : 'Klik untuk ubah ke Publik'">
                      <i :class="subFolder.is_public ? 'fas fa-globe' : 'fas fa-lock'"></i>
                      {{ subFolder.is_public ? 'Publik' : 'Privat' }}
                    </span>
                  </div>
                </div>
              </div>
              <div class="folder-card-actions">
                <button class="btn-action view" @click.stop="openFolder(subFolder)" title="Lihat Detail Folder">
                  <i class="fas fa-eye"></i>
                </button>
                <button v-if="userCanDelete" class="btn-action delete" @click.stop="confirmDeleteFolder(subFolder)" title="Hapus Folder">
                  <i class="fas fa-trash-alt"></i>
                </button>
              </div>
            </div>
          </div>

          <div v-if="viewMode === 'table'" class="folders-table-wrapper">
            <table class="folders-table">
              <thead>
                <tr>
                  <th @click="toggleSort('nama')" class="sortable">Nama Folder <i :class="getSortIcon('nama')"></i></th>
                  <th @click="toggleSort('kategori_iku')" class="sortable">IKU <i :class="getSortIcon('kategori_iku')"></i></th>
                  <th @click="toggleSort('deskripsi')" class="sortable">Desk File <i :class="getSortIcon('deskripsi')"></i></th>
                  <th @click="toggleSort('files_count')" class="sortable">Folder <i :class="getSortIcon('files_count')"></i></th>
                  <th @click="toggleSort('updated_at')" class="sortable">User Update &amp; Tanggal <i :class="getSortIcon('updated_at')"></i></th>
                  <th @click="toggleSort('is_public')" class="sortable">Status Akses <i :class="getSortIcon('is_public')"></i></th>
                  <th style="text-align:center;">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="folder in filteredAndSortedChildren" :key="folder.id" @click="openFolder(folder)" class="clickable-row">
                  <td>
                    <div class="folder-cell">
                      <div class="folder-icon-sm"><i class="fas fa-folder"></i></div>
                      <div class="folder-info-sm">
                        <template v-if="inlineEditId === folder.id && inlineEditType === 'folder'">
                          <input type="text" v-model="inlineEditValue" class="inline-input" @click.stop @keyup.enter="saveInlineEdit(folder, 'folder')" @keyup.esc="cancelInlineEdit" @blur="saveInlineEdit(folder, 'folder')">
                        </template>
                        <template v-else>
                          <div class="inline-edit-wrap">
                            <span class="folder-name-sm">{{ folder.nama }}</span>
                            <button v-if="userCanEdit" class="btn-inline-edit" @click.stop="startInlineEdit(folder, 'folder')" title="Edit nama"><i class="fas fa-pen"></i></button>
                          </div>
                        </template>
                      </div>
                    </div>
                  </td>
                  <td>
                    <span class="badge kode-badge" v-if="getIkuFromFolderName(folder.nama)">{{ getIkuFromFolderName(folder.nama) }}</span>
                    <span class="text-muted" v-else>-</span>
                  </td>
                  <td>
                    <template v-if="inlineEditId === folder.id && inlineEditType === 'folder_desc'">
                      <input type="text" v-model="inlineEditValue" class="inline-input" @click.stop @keyup.enter="saveInlineEditDesc(folder, 'folder')" @keyup.esc="cancelInlineEdit" @blur="saveInlineEditDesc(folder, 'folder')">
                    </template>
                    <template v-else>
                      <div class="inline-edit-wrap">
                        <span class="text-muted desk-text">{{ folder.deskripsi || '-' }}</span>
                        <button v-if="userCanEdit" class="btn-inline-edit" @click.stop="startInlineEdit(folder, 'folder_desc')" title="Edit deskripsi"><i class="fas fa-pen"></i></button>
                      </div>
                    </template>
                  </td>
                  <td><span class="text-muted">{{ getFolderContentText(folder) }}</span></td>
                  <td>
                    <div class="user-update-info">
                      <span class="text-muted user-update-name" v-if="getLastUpdater(folder)">
                        <i class="fas fa-user-circle"></i> {{ getLastUpdater(folder) }}
                      </span>
                      <span class="text-muted">{{ formatDate(folder.updated_at || folder.created_at) }}</span>
                    </div>
                  </td>
                  <td>
                    <span class="status-badge status-badge-clickable" :class="getFolderAccessClass(folder)" @click.stop="toggleFolderPublic(folder)" :title="folder.is_public ? 'Klik untuk ubah ke Privat' : 'Klik untuk ubah ke Publik'">
                      <i :class="getFolderAccessIcon(folder)"></i>
                      {{ getFolderAccessLabel(folder) }}
                    </span>
                  </td>
                  <td @click.stop style="text-align:center;">
                    <div class="table-actions">
                      <button class="btn-action view" @click.stop="openFolder(folder)" title="Lihat Detail Folder">
                        <i class="fas fa-eye"></i>
                      </button>
                      <button v-if="userCanDelete" class="btn-action delete" @click.stop="confirmDeleteFolder(folder)" title="Hapus Folder">
                        <i class="fas fa-trash-alt"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div v-if="(!currentFolder.files || currentFolder.files.length === 0) && (!currentFolder.children || currentFolder.children.length === 0)" class="empty-state">
          <div class="empty-icon"><i class="fas fa-cloud-upload-alt"></i></div>
          <h3>Folder Kosong</h3>
          <p>Belum ada sub-folder atau file di folder ini.</p>
          <div style="display: flex; justify-content: center; gap: 10px;">
            <button class="btn-primary-gold" @click="openFileModal()">
              <i class="fas fa-upload"></i> Upload File
            </button>
            <button class="btn-outline-gold" @click="openFolderModal(null, currentFolder.id)">
              <i class="fas fa-folder-plus"></i> Tambah Sub-Folder
            </button>
          </div>
        </div>

        <div v-if="currentFolder.files && currentFolder.files.length > 0" class="files-section">
          <h4 style="padding: 16px 24px 0; margin-bottom: 8px; color: #1e293b; font-size: 1.05rem;"><i class="fas fa-file-alt" style="color: #1d4ed8; margin-right: 6px;"></i> File</h4>
          
          <div v-if="viewMode === 'table'" class="files-table-wrapper">
            <table class="files-table">
              <thead>
                <tr>
                  <th @click="toggleSort('nama')" class="sortable">File <i :class="getSortIcon('nama')"></i></th>
                  <th @click="toggleSort('file_size')" class="sortable">Ukuran <i :class="getSortIcon('file_size')"></i></th>
                  <th @click="toggleSort('created_at')" class="sortable">User Update &amp; Tanggal <i :class="getSortIcon('created_at')"></i></th>
                  <th @click="toggleSort('is_public')" class="sortable">Status Akses <i :class="getSortIcon('is_public')"></i></th>
                  <th style="text-align:center;">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="file in filteredAndSortedFiles" :key="file.id">
                  <td>
                    <div class="file-cell">
                      <div class="file-type-icon" :class="getFileClass(file.file_type)">
                        <i :class="getFileIcon(file.file_type)"></i>
                      </div>
                      <div>
                        <a :href="file.file_url || ('/dokumen/files/' + file.id + '/download')" target="_blank" class="file-name-link">{{ file.nama }}</a>
                        <span class="file-ext">{{ getFileExt(file.file_path) }}</span>
                      </div>
                    </div>
                  </td>
                  <td><span class="text-muted">{{ formatSize(file.file_size) }}</span></td>
                  <td>
                    <div class="user-update-info">
                      <span class="text-muted user-update-name" v-if="file.uploader">
                        <i class="fas fa-user-circle"></i> {{ file.uploader.name }}
                      </span>
                      <span class="text-muted">{{ formatDate(file.created_at) }}</span>
                    </div>
                  </td>
                  <td>
                    <span class="status-badge status-badge-clickable" :class="file.is_public ? 'public' : 'private'" @click.stop="toggleFilePublic(file)" :title="file.is_public ? 'Klik untuk ubah ke Privat' : 'Klik untuk ubah ke Publik'">
                      <i :class="file.is_public ? 'fas fa-globe' : 'fas fa-lock'"></i>
                      {{ file.is_public ? 'Publik' : 'Privat' }}
                    </span>
                  </td>
                  <td @click.stop style="text-align:center;">
                    <div class="table-actions">
                      <a :href="file.file_url || ('/dokumen/files/' + file.id + '/download')" target="_blank" class="btn-action view" title="Lihat File">
                        <i class="fas fa-eye"></i>
                      </a>
                      <a :href="'/dokumen/files/' + file.id + '/download'" target="_blank" class="btn-action download" title="Download File">
                        <i class="fas fa-download"></i>
                      </a>
                      <button v-if="userCanEdit" class="btn-action edit" @click.stop="openFileModal(file)" title="Edit File">
                        <i class="fas fa-pen"></i>
                      </button>
                      <button v-if="userCanDelete" class="btn-action delete" @click.stop="confirmDeleteFile(file)" title="Hapus File">
                        <i class="fas fa-trash-alt"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div v-if="viewMode === 'grid'" class="folder-grid">
            <div 
              v-for="file in filteredAndSortedFiles" 
              :key="file.id"
              class="folder-card file-card"
            >
              <div class="folder-card-header">
                <div class="folder-icon-box" :class="getFileClass(file.file_type)">
                  <i :class="getFileIcon(file.file_type)"></i>
                </div>
                <div class="folder-card-info">
                  <h4><a :href="file.file_url || ('/dokumen/files/' + file.id + '/download')" target="_blank" style="color: inherit; text-decoration: none;">{{ file.nama }}</a></h4>
                  <div class="folder-meta">
                    <span class="meta-badge">{{ formatSize(file.file_size) }}</span>
                    <span class="meta-badge">{{ formatDate(file.created_at) }}</span>
                    <span class="status-badge status-badge-clickable" :class="file.is_public ? 'public' : 'private'" @click.stop="toggleFilePublic(file)" :title="file.is_public ? 'Klik untuk ubah ke Privat' : 'Klik untuk ubah ke Publik'">
                      <i :class="file.is_public ? 'fas fa-globe' : 'fas fa-lock'"></i>
                      {{ file.is_public ? 'Publik' : 'Privat' }}
                    </span>
                  </div>
                </div>
              </div>
              <div class="folder-card-actions">
                <a :href="file.file_url || ('/dokumen/files/' + file.id + '/download')" target="_blank" class="btn-action view" title="Lihat File">
                  <i class="fas fa-eye"></i>
                </a>
                <a :href="'/dokumen/files/' + file.id + '/download'" target="_blank" class="btn-action download" title="Download File">
                  <i class="fas fa-download"></i>
                </a>
                <button v-if="userCanEdit" class="btn-action edit" @click.stop="openFileModal(file)" title="Edit File">
                  <i class="fas fa-pen"></i>
                </button>
                <button v-if="userCanDelete" class="btn-action delete" @click.stop="confirmDeleteFile(file)" title="Hapus File">
                  <i class="fas fa-trash-alt"></i>
                </button>
              </div>
            </div>
          </div>

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
      folderHistory: [],
      loading: true,
      searchQuery: '',
      searchTimeout: null,
      isSearching: false,
      showSearchDropdown: false,
      filterStatus: 'all',
      filterIku: 'all',
      searchResults: {
        folders: [],
        files: []
      },
      
      // Filter & Sorting
      sortKey: 'nama',
      sortOrder: 'asc',
      
      // Layout properties
      viewMode: 'table',
      activeDropdown: null,

      // User / permission
      currentUser: null,

      // Inline edit
      inlineEditId: null,
      inlineEditType: null,
      inlineEditValue: '',

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
      toast: { show: false, message: '', type: 'success' },
      
      clickListener: null
    }
  },
  computed: {
    availableIkus() {
      const ikuSet = new Set();
      const processItem = (item) => {
        if (item.file_type) {
           if (item.iku && item.iku.kode) ikuSet.add(item.iku.kode);
        } else {
           const folderIku = this.getIkuFromFolderName(item.nama);
           if (folderIku) ikuSet.add(folderIku);
        }
      };
      
      this.folders.forEach(processItem);
      
      if (this.currentFolder) {
         if (this.currentFolder.children) this.currentFolder.children.forEach(processItem);
         if (this.currentFolder.files) this.currentFolder.files.forEach(processItem);
      }
      
      return Array.from(ikuSet).sort();
    },
    filteredAndSortedFolders() {
      return this.applyFilterAndSort(this.folders, 'folder');
    },
    filteredAndSortedChildren() {
      if (!this.currentFolder || !this.currentFolder.children) return [];
      return this.applyFilterAndSort(this.currentFolder.children, 'folder');
    },
    filteredAndSortedFiles() {
      if (!this.currentFolder || !this.currentFolder.files) return [];
      return this.applyFilterAndSort(this.currentFolder.files, 'file');
    },
    
    // Perbaikan Statistik Real-time & Komprehensif
    totalFoldersCount() {
      const countFolders = (folderList) => {
        if (!Array.isArray(folderList)) return 0;
        return folderList.reduce((acc, folder) => {
          const children = folder.children || [];
          return acc + 1 + countFolders(children);
        }, 0);
      };
      return countFolders(this.folders);
    },
    totalFiles() {
      const countFiles = (folderList) => {
        if (!Array.isArray(folderList)) return 0;
        return folderList.reduce((acc, folder) => {
          const directFiles = (folder.files && Array.isArray(folder.files)) 
            ? folder.files.length 
            : (folder.files_count || 0);
          const childFiles = countFiles(folder.children || []);
          return acc + directFiles + childFiles;
        }, 0);
      };
      return countFiles(this.folders);
    },
    publicCount() {
      const countPublic = (folderList) => {
        if (!Array.isArray(folderList)) return 0;
        return folderList.reduce((acc, folder) => {
          let currentCount = 0;
          if (Boolean(folder.is_public)) {
            currentCount++;
          }
          
          if (folder.files && Array.isArray(folder.files)) {
            currentCount += folder.files.filter(file => Boolean(file.is_public)).length;
          } else if (folder.public_files_count) {
            currentCount += folder.public_files_count;
          }
          
          return acc + currentCount + countPublic(folder.children || []);
        }, 0);
      };
      return countPublic(this.folders);
    },

    folderBreadcrumbs() {
      const crumbs = []
      for (const f of this.folderHistory) {
        crumbs.push({ id: f.id, nama: f.nama, slug: f.slug, full_slug_path: f.full_slug_path })
      }
      if (this.currentFolder) {
        crumbs.push({ 
          id: this.currentFolder.id, 
          nama: this.currentFolder.nama, 
          slug: this.currentFolder.slug,
          full_slug_path: this.currentFolder.full_slug_path 
        })
      }
      return crumbs
    },
    userCanEdit() {
      if (!this.currentUser) return false
      return this.currentUser.is_admin || this.currentUser.role === 'verifikator'
    },
    userCanDelete() {
      if (!this.currentUser) return false
      return this.currentUser.is_admin === true
    }
  },
  mounted() {
    this.loadCurrentUser()
    this.initializeFromRoute()
    this.clickListener = (e) => {
      if (!e.target.closest('.action-dropdown')) {
        this.closeDropdown()
      }
      if (!e.target.closest('.search-bar')) {
        this.showSearchDropdown = false
      }
    }
    document.addEventListener('click', this.clickListener)
  },
  watch: {
    '$route'(to, from) {
      if (to.name === 'pengelola.dokumen') {
        this.initializeFromRoute()
      }
    }
  },
  unmounted() {
    if (this.clickListener) {
      document.removeEventListener('click', this.clickListener)
    }
  },
  methods: {
    async initializeFromRoute() {
      let slug = this.$route.params.slugPath;
      if (Array.isArray(slug)) slug = slug.join('/');
      
      if (slug) {
        await this.resolvePath(slug);
      } else {
        this.currentFolder = null;
        this.folderHistory = [];
        await this.fetchFolders();
      }
    },
    async resolvePath(slug) {
      this.loading = true;
      try {
        const res = await axios.get('/dokumen/resolve-path', { params: { path: slug } });
        if (res.data.success) {
          this.currentFolder = res.data.data;
          let currentPath = '';
          this.folderHistory = (res.data.ancestors || []).map(anc => {
            currentPath = currentPath ? currentPath + '/' + anc.slug : anc.slug;
            return { ...anc, full_slug_path: currentPath };
          });
        }
      } catch (err) {
        this.showToast('Gagal memuat folder dari URL', 'error');
        this.$router.push('/pengelola/dokumen');
      } finally {
        this.loading = false;
      }
    },

    loadCurrentUser() {
      try {
        const userData = localStorage.getItem('user')
        if (userData) {
          this.currentUser = JSON.parse(userData)
        }
      } catch (e) {
        console.warn('Gagal memuat data user dari localStorage:', e)
      }
      axios.get('/user').then(res => {
        if (res.data && res.data.data) {
          this.currentUser = res.data.data
          localStorage.setItem('user', JSON.stringify(this.currentUser))
        }
      }).catch(() => {})
    },

    toggleDropdown(id) {
      this.activeDropdown = this.activeDropdown === id ? null : id;
    },
    closeDropdown() {
      this.activeDropdown = null;
    },

    startInlineEdit(item, type) {
      this.inlineEditId = item.id
      this.inlineEditType = type
      if (type === 'folder') {
        this.inlineEditValue = item.nama
      } else if (type === 'folder_desc') {
        this.inlineEditValue = item.deskripsi || ''
      }
    },
    cancelInlineEdit() {
      this.inlineEditId = null
      this.inlineEditType = null
      this.inlineEditValue = ''
    },
    async saveInlineEdit(item, type) {
      if (!this.inlineEditValue.trim()) {
        this.cancelInlineEdit()
        return
      }
      try {
        const data = {
          nama: type === 'folder' ? this.inlineEditValue : item.nama,
          deskripsi: item.deskripsi || '',
          is_public: item.is_public
        }
        const res = await axios.put(`/dokumen/folders/${item.id}`, data)
        if (res.data.success) {
          item.nama = data.nama
          this.showToast('Nama folder berhasil diperbarui!')
        }
      } catch (err) {
        this.showToast('Gagal memperbarui nama folder', 'error')
      } finally {
        this.cancelInlineEdit()
      }
    },
    async saveInlineEditDesc(item, type) {
      try {
        const data = {
          nama: item.nama,
          deskripsi: this.inlineEditValue,
          is_public: item.is_public
        }
        const res = await axios.put(`/dokumen/folders/${item.id}`, data)
        if (res.data.success) {
          item.deskripsi = data.deskripsi
          this.showToast('Deskripsi folder berhasil diperbarui!')
        }
      } catch (err) {
        this.showToast('Gagal memperbarui deskripsi folder', 'error')
      } finally {
        this.cancelInlineEdit()
      }
    },

    applyFilterAndSort(items, type) {
      if (!items) return [];
      
      let result = items.filter(item => {
        const isPub = Boolean(item.is_public);
        if (this.filterStatus === 'public' && !isPub) return false;
        if (this.filterStatus === 'private' && isPub) return false;
        
        if (this.filterIku !== 'all') {
           const iku = item.file_type 
               ? (item.iku ? item.iku.kode : null) 
               : this.getIkuFromFolderName(item.nama);
           if (iku !== this.filterIku) return false;
        }
        
        return true;
      });
      
      result = result.sort((a, b) => {
        let valA = a[this.sortKey];
        let valB = b[this.sortKey];
        
        if (this.sortKey === 'files_count') {
           valA = a.children ? a.children.length : (a.children_count || 0);
           valB = b.children ? b.children.length : (b.children_count || 0);
        } else if (this.sortKey === 'file_size') {
           valA = a.file_size || 0;
           valB = b.file_size || 0;
        } else if (this.sortKey === 'updated_at' || this.sortKey === 'created_at') {
           valA = new Date(a.updated_at || a.created_at).getTime();
           valB = new Date(b.updated_at || b.created_at).getTime();
        } else if (this.sortKey === 'kategori_iku') {
           valA = this.getIkuFromFolderName(a.nama) || '';
           valB = this.getIkuFromFolderName(b.nama) || '';
        } else {
           if (typeof valA === 'string') valA = valA.toLowerCase();
           if (typeof valB === 'string') valB = valB.toLowerCase();
        }
        
        if (valA < valB) return this.sortOrder === 'asc' ? -1 : 1;
        if (valA > valB) return this.sortOrder === 'asc' ? 1 : -1;
        return 0;
      });
      
      return result;
    },
    toggleSort(key) {
      if (this.sortKey === key) {
        this.sortOrder = this.sortOrder === 'asc' ? 'desc' : 'asc';
      } else {
        this.sortKey = key;
        this.sortOrder = 'asc';
      }
    },
    getSortIcon(key) {
      if (this.sortKey !== key) return 'fas fa-sort text-muted-light sort-icon';
      const isNumericOrDate = ['files_count', 'file_size', 'updated_at', 'created_at'].includes(key);
      if (isNumericOrDate) {
        return this.sortOrder === 'asc' ? 'fas fa-sort-numeric-down sort-icon active' : 'fas fa-sort-numeric-up sort-icon active';
      }
      return this.sortOrder === 'asc' ? 'fas fa-sort-alpha-down sort-icon active' : 'fas fa-sort-alpha-up sort-icon active';
    },

    handleSearchInput() {
      this.showSearchDropdown = true;
      if (this.searchTimeout) clearTimeout(this.searchTimeout);
      
      if (!this.searchQuery.trim()) {
        this.searchResults = { folders: [], files: [] };
        this.fetchFolders();
        return;
      }

      this.isSearching = true;
      this.searchTimeout = setTimeout(async () => {
        try {
          const res = await axios.get('/dokumen/search', { params: { q: this.searchQuery } });
          if (res.data.success) {
            this.searchResults = res.data.data;
          }
        } catch (err) {
          console.error(err);
        } finally {
          this.isSearching = false;
        }
      }, 300);
    },
    clearSearch() {
      this.searchQuery = '';
      this.showSearchDropdown = false;
      this.searchResults = { folders: [], files: [] };
      this.fetchFolders();
    },
    async openFolderFromSearch(folder) {
      this.showSearchDropdown = false;
      this.searchQuery = '';
      try {
         await this.fetchFolders();
         const fullFolder = this.folders.find(f => f.id === folder.id);
         if (fullFolder) {
           const path = fullFolder.full_slug_path || fullFolder.slug;
           this.$router.push('/pengelola/dokumen/' + path);
         }
      } catch (err) {}
    },
    downloadFileFromSearch(file) {
      this.showSearchDropdown = false;
      window.open('/dokumen/files/' + file.id + '/download', '_blank');
    },

    navigateToRoot() {
      this.$router.push('/pengelola/dokumen');
    },
    navigateToAncestor(idx) {
      const targetFolder = this.folderBreadcrumbs[idx]
      const path = targetFolder.full_slug_path || targetFolder.slug;
      this.$router.push('/pengelola/dokumen/' + path);
    },

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

    async openFolder(folder) {
      this.closeDropdown();
      let path = folder.full_slug_path;
      if (!path) {
         path = this.currentFolder ? this.currentFolder.full_slug_path + '/' + folder.slug : folder.slug;
      }
      this.$router.push('/pengelola/dokumen/' + path);
    },
    async backToFolders() {
      if (this.folderHistory.length > 0) {
        const parent = this.folderHistory[this.folderHistory.length - 1];
        const path = parent.full_slug_path || parent.slug;
        this.$router.push('/pengelola/dokumen/' + path);
      } else {
        this.$router.push('/pengelola/dokumen');
      }
    },
    openFolderModal(folder = null, parentId = null) {
      this.editingFolder = folder
      this.folderErrors = {}
      if (folder) {
        this.folderForm = { nama: folder.nama, deskripsi: folder.deskripsi || '', is_public: Boolean(folder.is_public), parent_id: folder.parent_id }
      } else {
        this.folderForm = { nama: '', deskripsi: '', is_public: false, parent_id: parentId }
      }
      this.showFolderModal = true
    },
    async saveFolder() {
      this.savingFolder = true
      this.folderErrors = {}
      try {
        const payload = {
          ...this.folderForm,
          is_public: this.folderForm.is_public ? 1 : 0
        };
        if (this.editingFolder) {
          const res = await axios.put(`/dokumen/folders/${this.editingFolder.id}`, payload)
          if (res.data.success) {
            this.showToast('Folder berhasil diperbarui!')
            this.showFolderModal = false
            this.refreshCurrentFolder()
          }
        } else {
          const res = await axios.post('/dokumen/folders', payload)
          if (res.data.success) {
            this.showToast('Folder berhasil dibuat!')
            this.showFolderModal = false
            this.refreshCurrentFolder()
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
            this.refreshCurrentFolder()
          }
        } catch (err) {
          this.showToast('Gagal menghapus folder', 'error')
        }
      }
      this.showDeleteModal = true
    },
    
    // Toggle Status Folder Public/Private
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

    openFileModal(file = null) {
      this.editingFile = file
      this.fileErrors = {}
      this.selectedFiles = []
      if (file) {
        this.fileForm = { nama: file.nama, is_public: Boolean(file.is_public) }
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
            formData.append('file', this.selectedFiles[0])
            formData.append('nama', this.fileForm.nama || this.selectedFiles[0].name)
          } else {
            this.selectedFiles.forEach((file) => {
              formData.append('files[]', file)
            })
          }

          const res = await axios.post(`/dokumen/folders/${this.currentFolder.id}/files`, formData)

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

    // Toggle Status File Public/Private
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
      await this.initializeFromRoute()
    },

    async executeDelete() {
      this.deleting = true
      if (this.deleteCallback) await this.deleteCallback()
      this.deleting = false
      this.showDeleteModal = false
    },

    exportCSV() {
      let csvContent = "data:text/csv;charset=utf-8,";
      
      if (this.currentFolder) {
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

    getIkuFromFolderName(name) {
      if (!name) return null;
      const kriteriaMatch = name.match(/(?:Kriteria\s+)?(C[\d\.]+)/i);
      if (kriteriaMatch) {
        let val = kriteriaMatch[1].toUpperCase();
        if (val.endsWith('.')) val = val.slice(0, -1);
        return val;
      }
      const ikuMatch = name.match(/IKU[\s\-]?(\d+)/i);
      if (ikuMatch) {
        return 'IKU-' + ikuMatch[1];
      }
      return null;
    },
    getLastUpdater(folder) {
      if (folder.files && folder.files.length > 0) {
        const sorted = [...folder.files]
          .filter(f => f.uploader)
          .sort((a, b) => new Date(b.updated_at || b.created_at) - new Date(a.updated_at || a.created_at))
        if (sorted.length > 0 && sorted[0].uploader) {
          return sorted[0].uploader.name
        }
      }
      return null
    },
    getFolderAccessClass(folder) {
      return Boolean(folder.is_public) ? 'public' : 'private'
    },
    getFolderAccessIcon(folder) {
      return Boolean(folder.is_public) ? 'fas fa-globe' : 'fas fa-lock'
    },
    getFolderAccessLabel(folder) {
      return Boolean(folder.is_public) ? 'Public' : 'Private'
    },
    getFolderContentText(folder) {
      const folders = folder.children ? folder.children.length : (folder.children_count || 0);
      const files = folder.files ? folder.files.length : (folder.files_count || 0);
      
      let text = [];
      if (folders > 0) text.push(`${folders} Folder`);
      if (files > 0) text.push(`${files} File`);
      
      if (text.length === 0) return 'Kosong';
      return text.join(', ');
    },
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

/* ===== BREADCRUMB GLOBAL DINAMIS ===== */
.global-breadcrumb {
  font-size: 0.85rem;
  color: #64748b;
  margin-bottom: 8px;
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;
}
.global-breadcrumb .active {
  color: #996600;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  gap: 5px;
}
.global-breadcrumb .separator {
  font-size: 0.7rem;
  color: #cbd5e1;
}
.global-breadcrumb .breadcrumb-link {
  cursor: pointer;
  color: #64748b;
  transition: color 0.2s;
  display: inline-flex;
  align-items: center;
  gap: 5px;
}
.global-breadcrumb .breadcrumb-link:hover {
  color: #996600;
  text-decoration: underline;
}

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
  flex-wrap: wrap;
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

/* ===== STATS BAR COMPACT ===== */
.stats-row {
  display: flex;
  gap: 16px;
  margin-bottom: 24px;
  flex-wrap: wrap;
}
.stat-card {
  background: white;
  border-radius: 10px;
  padding: 10px 18px;
  display: flex;
  align-items: center;
  gap: 12px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.04);
  border: 1px solid #e2e8f0;
  flex: 1;
  min-width: 150px;
  transition: all 0.2s;
}
.stat-card:hover {
  box-shadow: 0 4px 12px rgba(0,0,0,0.05);
  transform: translateY(-1px);
}
.stat-icon {
  width: 38px; height: 38px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1rem;
}
.stat-icon.folders { background: #fef3c7; color: #92400e; }
.stat-icon.files { background: #dbeafe; color: #1d4ed8; }
.stat-icon.public { background: #d1fae5; color: #059669; }
.stat-info {
  display: flex; flex-direction: column; justify-content: center;
}
.stat-num { font-size: 1.25rem; font-weight: 700; color: #1e293b; line-height: 1.2; }
.stat-lbl { font-size: 0.78rem; color: #94a3b8; }

/* ===== TOOLBAR & SEARCH BAR BARU ===== */
.toolbar-section {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
  gap: 16px;
  flex-wrap: wrap;
}
.search-bar-wrapper {
  flex: 1;
  min-width: 250px;
  position: relative;
}
.search-bar {
  display: flex;
  align-items: center;
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 0 16px;
  height: 42px;
  transition: all 0.2s;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}
.search-bar:focus-within {
  border-color: #996600;
  box-shadow: 0 0 0 3px rgba(153, 102, 0, 0.1);
}
.search-bar i {
  color: #94a3b8;
  margin-right: 12px;
}
.search-bar input {
  border: none;
  outline: none;
  width: 100%;
  font-size: 0.95rem;
  color: #334155;
  background: transparent;
  padding: 0;
}
.search-bar .btn-clear {
  background: none;
  border: none;
  color: #94a3b8;
  cursor: pointer;
  padding: 4px;
}
.search-bar .btn-clear:hover {
  color: #ef4444;
}

/* ===== AUTOCOMPLETE DROPDOWN OVERLAY ===== */
.search-dropdown-overlay {
  position: absolute;
  top: calc(100% + 6px);
  left: 0; right: 0;
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
  z-index: 50;
  max-height: 320px;
  overflow-y: auto;
  padding: 8px 0;
}
.search-loading, .search-empty {
  padding: 12px 16px;
  font-size: 0.875rem;
  color: #64748b;
  text-align: center;
}
.search-group-title {
  padding: 6px 16px;
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: #94a3b8;
  background: #f8fafc;
}
.search-item {
  display: flex;
  align-items: center;
  padding: 10px 16px;
  cursor: pointer;
  transition: background 0.15s;
  gap: 12px;
}
.search-item:hover {
  background: #f1f5f9;
}
.search-item-icon {
  color: #996600;
  font-size: 1rem;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 20px;
}
.search-item-info {
  display: flex;
  flex-direction: column;
}
.search-item-name {
  font-size: 0.875rem;
  color: #1e293b;
  font-weight: 500;
}
.search-item-meta {
  font-size: 0.75rem;
  color: #64748b;
}

/* ===== TOOLBAR ACTIONS & FILTERS ===== */
.toolbar-actions {
  display: flex;
  gap: 12px;
  align-items: center;
}
.integrated-filter {
  display: flex;
  align-items: center;
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 0 12px;
  height: 42px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05);
  transition: border-color 0.2s;
}
.integrated-filter:focus-within {
  border-color: #996600;
  box-shadow: 0 0 0 3px rgba(153, 102, 0, 0.1);
}
.filter-icon-left {
  color: #94a3b8;
  margin-right: 8px;
  font-size: 0.9rem;
}
.filter-select-clean {
  border: none;
  background: transparent;
  outline: none;
  color: #334155;
  font-size: 0.95rem;
  padding: 8px 0;
  cursor: pointer;
  appearance: none;
  -webkit-appearance: none;
  -moz-appearance: none;
  padding-right: 20px;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right center;
  background-size: 16px;
}

/* ===== VIEW TOGGLE ===== */
.view-controls .view-toggle {
  display: flex;
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  overflow: hidden;
  height: 42px;
}
.view-toggle button {
  background: none;
  border: none;
  padding: 0 16px;
  height: 100%;
  color: #64748b;
  cursor: pointer;
  transition: all 0.2s;
}
.view-toggle button:hover {
  background: #f8fafc;
  color: #334155;
}
.view-toggle button.active {
  background: #f1f5f9;
  color: #996600;
  box-shadow: inset 0 1px 2px rgba(0,0,0,0.05);
}

/* ===== LOADING & EMPTY ===== */
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

/* ===== FOLDERS TABLE ===== */
.folders-table-wrapper {
  background: white;
  border-radius: 12px;
  border: 1px solid #e2e8f0;
  overflow: visible; 
  margin-bottom: 24px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.02);
}
.sortable {
  cursor: pointer;
  user-select: none;
}
.folders-table { width: 100%; border-collapse: separate; border-spacing: 0; }
.folders-table th {
  text-align: left;
  padding: 16px 24px;
  font-size: 0.8rem;
  font-weight: 600;
  color: #64748b;
  border-bottom: 1px solid #e2e8f0;
  background: #f8fafc;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  white-space: nowrap;
}
.folders-table th:first-child { border-top-left-radius: 12px; }
.folders-table th:last-child { border-top-right-radius: 12px; }
.folders-table td {
  padding: 18px 24px;
  border-bottom: 1px solid #f1f5f9;
  vertical-align: middle;
}
.clickable-row { cursor: pointer; transition: background 0.2s; }
.clickable-row:hover { background: #fefce8; }
.clickable-row:last-child td { border-bottom: none; }
.row-dropdown-open { position: relative; z-index: 100; }

.folder-cell { display: flex; align-items: center; gap: 14px; }
.folder-icon-sm {
  width: 42px; height: 42px;
  border-radius: 10px; background: linear-gradient(135deg, #fef3c7, #fde68a);
  color: #92400e; display: flex; align-items: center; justify-content: center;
  font-size: 1.15rem; flex-shrink: 0;
}
.folder-info-sm { display: flex; flex-direction: column; }
.folder-name-sm { font-weight: 600; color: #1e293b; font-size: 0.95rem; }
.folder-desc-sm { 
  font-size: 0.78rem; color: #94a3b8; 
  max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; 
  margin-top: 2px;
}

/* Inline Edit */
.inline-edit-wrap {
  display: flex;
  align-items: center;
  gap: 6px;
}
.btn-inline-edit {
  background: none;
  border: none;
  color: #cbd5e1;
  cursor: pointer;
  font-size: 0.75rem;
  padding: 2px 4px;
  border-radius: 4px;
  transition: all 0.2s;
  opacity: 0;
}
.inline-edit-wrap:hover .btn-inline-edit,
.clickable-row:hover .btn-inline-edit {
  opacity: 1;
}
.btn-inline-edit:hover {
  color: #996600;
  background: #fffbeb;
}
.inline-input {
  padding: 6px 10px;
  border: 1px solid #cc9900;
  border-radius: 6px;
  font-size: 0.9rem;
  outline: none;
  box-shadow: 0 0 0 3px rgba(204, 153, 0, 0.1);
  width: 100%;
  max-width: 250px;
}

/* Desk Text */
.desk-text {
  max-width: 200px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  display: inline-block;
}

/* User Update Info */
.user-update-info {
  display: flex;
  flex-direction: column;
  gap: 2px;
}
.user-update-name {
  display: flex;
  align-items: center;
  gap: 4px;
  font-weight: 500;
  color: #475569 !important;
  font-size: 0.82rem !important;
}

/* Badge IKU */
.badge.kode-badge {
  background: #eff6ff;
  color: #1d4ed8;
  padding: 3px 10px;
  border-radius: 6px;
  font-size: 0.78rem;
  font-weight: 600;
  display: inline-block;
}

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
  overflow: visible;
  transition: all 0.25s;
  box-shadow: 0 1px 4px rgba(0,0,0,0.04);
  position: relative;
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
  border-radius: 14px 14px 0 0;
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
  padding: 4px 10px; border-radius: 6px;
}
.status-badge.public { background: #d1fae5; color: #059669; }
.status-badge.private { background: #f1f5f9; color: #64748b; }
.status-badge.restricted { background: #fef3c7; color: #92400e; }

/* Status Badge Clickable */
.status-badge-clickable {
  cursor: pointer;
  user-select: none;
  transition: all 0.2s ease;
}
.status-badge-clickable:hover {
  transform: scale(1.05);
  box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.table-actions {
  display: flex;
  justify-content: center;
  gap: 6px;
}
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
.btn-action.view:hover { color: #0891b2; background: #cffafe; border-color: #a5f3fc; }
.btn-action.download:hover { color: #16a34a; background: #dcfce7; border-color: #86efac; }
.btn-action.edit:hover { color: #2563eb; background: #eff6ff; border-color: #bfdbfe; }
.btn-action.delete:hover { color: #dc2626; background: #fef2f2; border-color: #fecaca; }

/* ===== FILES TABLE ===== */
.files-table-wrapper {
  background: white;
  border-radius: 14px;
  border: 1px solid #e2e8f0;
  overflow: visible;
}
.files-table {
  width: 100%;
  border-collapse: collapse;
}
.files-table th {
  text-align: left;
  padding: 16px 24px;
  font-size: 0.78rem;
  font-weight: 600;
  color: #94a3b8;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  border-bottom: 1px solid #f1f5f9;
  white-space: nowrap;
}
.files-table td {
  padding: 18px 24px;
  border-bottom: 1px solid #f8fafc;
  vertical-align: middle;
}
.files-table tr:hover td { background: #fefce8; }
.files-table tr:last-child td { border-bottom: none; }
.file-cell {
  display: flex; align-items: center; gap: 14px;
}
.file-type-icon {
  width: 42px; height: 42px;
  border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
  font-size: 1.15rem;
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
  font-size: 0.95rem; font-weight: 500; color: #1e293b;
  display: block; max-width: 250px;
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.file-name-link {
  font-size: 0.95rem; font-weight: 500; color: #1e293b;
  display: block; max-width: 250px;
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
  text-decoration: none;
  transition: color 0.2s;
}
.file-name-link:hover {
  color: #92400e;
  text-decoration: underline;
}
.file-ext {
  font-size: 0.75rem; color: #94a3b8;
  display: block; margin-top: 2px;
}
.text-muted { color: #94a3b8; font-size: 0.85rem; }

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

/* ===== SORTING & AUXILIARY ===== */
.sort-icon {
  margin-left: 4px;
  font-size: 0.8rem;
  transition: color 0.2s;
}
.sort-icon.active {
  color: #996600;
}
.text-muted-light {
  color: #cbd5e1;
}

/* ===== RESPONSIVE ===== */
@media (max-width: 768px) {
  .page-header { flex-direction: column; }
  .folder-grid { grid-template-columns: 1fr; }
  .stats-row { flex-direction: column; }
  .files-table th:nth-child(3),
  .files-table td:nth-child(3) { display: none; }
  .folders-table th:nth-child(3),
  .folders-table td:nth-child(3),
  .folders-table th:nth-child(5),
  .folders-table td:nth-child(5) { display: none; }
}
@media (max-width: 480px) {
  .global-breadcrumb { flex-direction: column; align-items: flex-start; gap: 4px; }
  .header-actions { flex-direction: column; width: 100%; }
  .header-actions button { width: 100%; justify-content: center; }
}

@media print {
  .admin-layout > .sidebar,
  .admin-layout > .topbar,
  .toolbar-section,
  .header-actions,
  .btn-upload-file,
  .folder-card-actions,
  .action-dropdown,
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

  .files-table th, .files-table td, .folders-table th, .folders-table td {
    border: 1px solid #ddd !important;
  }
}
</style>