<template>
  <AdminLayout page-title="Dashboard" page-icon="fa-tachometer-alt">
    <div class="dashboard-content">
      <!-- Loading State -->
      <div v-if="loading" class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Memuat data...</span>
        </div>
        <p class="mt-3 text-muted fw-medium">Memuat data dashboard SPMI...</p>
      </div>

      <!-- Content -->
      <div v-else>
        <!-- Welcome Card -->
        <div class="welcome-card mb-4">
          <div>
            <p class="welcome-eyebrow">Selamat datang, {{ currentUser.nama }}</p>
            <h3>Kelola Sistem SPMI dengan Lebih Efisien</h3>
            <p>Pantau ketercapaian IKU, jadwal kegiatan, dokumen, dan berita dari satu tempat.</p>
          </div>
          <div class="welcome-pill">{{ currentUser.roleText }}</div>
        </div>

        <!-- Statistik Ringkasan -->
        <div class="row g-4 mb-4">
          <div class="col-xl-3 col-md-6" v-for="(stat, index) in stats" :key="index">
            <div class="stat-card">
              <div class="stat-content">
                <div>
                  <h6 class="stat-title">{{ stat.title }}</h6>
                  <h2 class="stat-value">{{ stat.value }}</h2>
                  <small :class="stat.trendClass">
                    <i :class="stat.trendIcon"></i>{{ stat.trendText }}
                  </small>
                </div>
                <div :class="['stat-icon', stat.iconBg]">
                  <i :class="stat.icon"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="row g-4 mb-4">
          <div class="col-12">
            <div class="quick-actions-card">
              <h5 class="card-title mb-4">
                <i class="fas fa-bolt me-2 text-warning"></i>Quick Actions
              </h5>
              <div class="row g-3">
                <div class="col-lg-3 col-md-6" v-for="action in quickActions" :key="action.title">
                  <router-link :to="action.route" class="action-btn" :class="action.btnClass">
                    <i :class="[action.icon, 'action-icon']"></i>
                    <div class="action-text">
                      <strong>{{ action.title }}</strong>
                      <small>{{ action.subtitle }}</small>
                    </div>
                  </router-link>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Activities & Quick Menu -->
        <div class="row g-4">
          <div class="col-lg-8">
            <div class="activities-card">
              <h5 class="card-title mb-4">
                <i class="fas fa-history me-2 text-primary"></i>Aktivitas Sistem Terbaru
              </h5>
              <div class="table-responsive">
                <table class="table activities-table align-middle">
                  <thead>
                    <tr>
                      <th>Waktu</th>
                      <th>User</th>
                      <th>Aktivitas</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-if="activities.length === 0">
                      <td colspan="4" class="text-center text-muted py-4">
                        <i class="fas fa-inbox fa-2x d-block mb-2"></i>
                        Belum ada aktivitas tercatat
                      </td>
                    </tr>
                    <tr v-for="activity in activities" :key="activity.id">
                      <td>
                        <span class="activity-time">{{ activity.time }}</span>
                      </td>
                      <td>
                        <div class="activity-user">
                          <i class="fas fa-user-circle me-1 text-secondary"></i>
                          {{ activity.user }}
                        </div>
                      </td>
                      <td>{{ activity.activity }}</td>
                      <td>
                        <span :class="['badge', activity.statusClass]">
                          {{ activity.status }}
                        </span>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          
          <div class="col-lg-4">
            <div class="menu-card">
              <h5 class="card-title mb-4">
                <i class="fas fa-link me-2 text-success"></i>Menu Admin Cepat
              </h5>
              <div class="menu-list">
                <router-link 
                  v-for="menu in adminMenus" 
                  :key="menu.title"
                  :to="menu.route" 
                  class="menu-item"
                >
                  <div class="menu-icon" :class="menu.iconBg">
                    <i :class="menu.icon"></i>
                  </div>
                  <div class="menu-content">
                    <strong>{{ menu.title }}</strong>
                    <small>{{ menu.subtitle }}</small>
                  </div>
                  <i class="fas fa-chevron-right menu-arrow"></i>
                </router-link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import AdminLayout from '@/components/AdminLayout.vue'

export default {
  name: 'DashboardView',
  components: {
    AdminLayout
  },
  setup() {
    const loading = ref(true)
    const stats = ref([])
    const activities = ref([])
    const currentUser = ref({
      nama: 'Pengelola SPMI',
      roleText: 'Administrator'
    })

    const statsData = [
      {
        title: 'Total User',
        value: '0',
        icon: 'fas fa-users',
        iconBg: 'bg-primary',
        trendClass: 'text-success',
        trendIcon: 'fas fa-arrow-up me-1',
        trendText: 'Aktif'
      },
      {
        title: 'Total Berita',
        value: '0',
        icon: 'fas fa-newspaper',
        iconBg: 'bg-success',
        trendClass: 'text-success',
        trendIcon: 'fas fa-arrow-up me-1',
        trendText: 'Terpublikasi'
      },
      {
        title: 'Jadwal Aktif',
        value: '0',
        icon: 'fas fa-calendar-alt',
        iconBg: 'bg-warning',
        trendClass: 'text-warning',
        trendIcon: 'fas fa-clock me-1',
        trendText: 'Kegiatan berjalan'
      },
      {
        title: 'Dokumen Upload',
        value: '0',
        icon: 'fas fa-file-upload',
        iconBg: 'bg-info',
        trendClass: 'text-info',
        trendIcon: 'fas fa-file-alt me-1',
        trendText: 'Tersimpan'
      }
    ]

    const quickActions = [
      {
        title: 'Tambah Berita',
        subtitle: 'Posting berita baru',
        icon: 'fas fa-plus-circle',
        btnClass: 'btn-primary',
        route: '/admin/berita'
      },
      {
        title: 'Buat Jadwal',
        subtitle: 'Jadwal kegiatan baru',
        icon: 'fas fa-calendar-plus',
        btnClass: 'btn-success',
        route: '/admin/jadwal'
      },
      {
        title: 'Atur IKU',
        subtitle: 'Kelola indikator',
        icon: 'fas fa-chart-line',
        btnClass: 'btn-warning',
        route: '/admin/iku'
      },
      {
        title: 'Kelola Unit',
        subtitle: 'Unit kerja',
        icon: 'fas fa-building',
        btnClass: 'btn-info',
        route: '/admin/unit-kerja'
      }
    ]

    const adminMenus = [
      {
        title: 'Kelola Berita',
        subtitle: 'Edit, hapus, publikasi',
        icon: 'fas fa-newspaper',
        iconBg: 'bg-primary-light',
        route: '/admin/berita'
      },
      {
        title: 'Kelola Jadwal',
        subtitle: 'Jadwal kegiatan SPMI',
        icon: 'fas fa-calendar-alt',
        iconBg: 'bg-success-light',
        route: '/admin/jadwal'
      },
      {
        title: 'Manajemen User',
        subtitle: 'Atur hak akses user',
        icon: 'fas fa-users-cog',
        iconBg: 'bg-warning-light',
        route: '/admin/users'
      },
      {
        title: 'Pengaturan IKU',
        subtitle: 'Indikator Kinerja Utama',
        icon: 'fas fa-chart-bar',
        iconBg: 'bg-info-light',
        route: '/admin/iku'
      }
    ]

    const getStatusClass = (status) => {
      const statusMap = {
        'Selesai': 'bg-success',
        'Pending': 'bg-warning',
        'Proses': 'bg-info',
        'Gagal': 'bg-danger',
        'Ditinjau': 'bg-primary'
      }
      return statusMap[status] || 'bg-secondary'
    }

    const loadUserData = () => {
      const storedUser = localStorage.getItem('user')
      if (storedUser) {
        try {
          const parsed = JSON.parse(storedUser)
          currentUser.value.nama = parsed.name || parsed.nama || 'Pengelola SPMI'
          currentUser.value.roleText = parsed.is_admin ? 'Administrator' : 
                                       parsed.is_verifikator ? 'Verifikator' : 'User'
        } catch (e) {
          console.error(e)
        }
      }
    }

    const fetchStats = async () => {
      try {
        const response = await axios.get('/dashboard/stats')
        if (response.data && response.data.success) {
          const data = response.data.data
          stats.value = statsData.map((stat, index) => ({
            ...stat,
            value: data[index]?.value || '0',
            trendText: data[index]?.trendText || stat.trendText
          }))
        } else {
          stats.value = statsData
        }
      } catch (error) {
        stats.value = statsData
      }
    }

    const fetchActivities = async () => {
      try {
        const response = await axios.get('/dashboard/activity')
        if (response.data && response.data.success && response.data.data.length > 0) {
          activities.value = response.data.data.map(activity => ({
            ...activity,
            statusClass: getStatusClass(activity.status)
          }))
        } else {
          activities.value = [
            {
              id: 1,
              time: '10:30',
              user: 'Admin Utama',
              activity: 'Menambahkan berita kegiatan SPMI',
              status: 'Selesai',
              statusClass: 'bg-success'
            },
            {
              id: 2,
              time: '09:45',
              user: 'Unit LPM',
              activity: 'Upload dokumen Evaluasi Diri',
              status: 'Pending',
              statusClass: 'bg-warning'
            }
          ]
        }
      } catch (error) {
        activities.value = [
          {
            id: 1,
            time: '10:30',
            user: 'Admin Utama',
            activity: 'Menambahkan berita kegiatan SPMI',
            status: 'Selesai',
            statusClass: 'bg-success'
          }
        ]
      }
    }

    const loadDashboard = async () => {
      loading.value = true
      loadUserData()
      await Promise.all([fetchStats(), fetchActivities()])
      loading.value = false
    }

    onMounted(() => {
      loadDashboard()
    })

    return {
      loading,
      stats,
      activities,
      quickActions,
      adminMenus,
      currentUser
    }
  }
}
</script>

<style scoped>
.dashboard-content {
  animation: fadeIn 0.4s ease-out;
  padding: 0.25rem 0 1rem;
}

.welcome-card {
  background: linear-gradient(135deg, #fff9e6 0%, #fdf3d4 100%);
  border: 1px solid #f0e0b8;
  border-radius: 16px;
  padding: 1.25rem 1.5rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 1rem;
  box-shadow: 0 8px 20px rgba(153, 102, 0, 0.06);
}

.welcome-eyebrow {
  margin: 0 0 0.25rem;
  font-size: 0.8rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: #996600;
}

.welcome-card h3 {
  margin: 0 0 0.25rem;
  font-size: 1.15rem;
  font-weight: 700;
  color: #2c3e50;
}

.welcome-card p {
  margin: 0;
  color: #6c757d;
  font-size: 0.9rem;
}

.welcome-pill {
  background: #996600;
  color: white;
  padding: 0.5rem 0.85rem;
  border-radius: 999px;
  font-size: 0.8rem;
  font-weight: 600;
  white-space: nowrap;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(15px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* STAT CARDS */
.stat-card {
  background: white;
  border-radius: 12px;
  padding: 1.25rem;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
  transition: all 0.3s ease;
  height: 100%;
}

.stat-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
}

.stat-content {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
}

.stat-title {
  color: #6c757d;
  font-size: 0.8rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 0.5rem;
}

.stat-value {
  font-size: 1.8rem;
  font-weight: 700;
  color: #2c3e50;
  margin-bottom: 0.25rem;
}

.stat-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.35rem;
}

.bg-primary {
  background: linear-gradient(135deg, #996600, #7a5200);
}

.bg-success {
  background: linear-gradient(135deg, #28a745, #20c997);
}

.bg-warning {
  background: linear-gradient(135deg, #ffc107, #fd7e14);
}

.bg-info {
  background: linear-gradient(135deg, #17a2b8, #0dcaf0);
}

/* QUICK ACTIONS */
.quick-actions-card {
  background: white;
  border-radius: 16px;
  padding: 1.5rem;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04);
}

.card-title {
  font-weight: 600;
  color: #2c3e50;
  font-size: 1.05rem;
}

.action-btn {
  display: flex;
  align-items: center;
  padding: 0.85rem 1.1rem;
  border-radius: 10px;
  text-decoration: none;
  color: white;
  transition: all 0.3s ease;
  gap: 0.85rem;
}

.action-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 18px rgba(0, 0, 0, 0.12);
  color: white;
}

.action-icon {
  font-size: 1.35rem;
}

.action-text {
  display: flex;
  flex-direction: column;
}

.action-text strong {
  font-size: 0.95rem;
}

.action-text small {
  font-size: 0.75rem;
  opacity: 0.9;
}

.btn-primary {
  background: linear-gradient(135deg, #996600, #7a5200);
}

.btn-success {
  background: linear-gradient(135deg, #28a745, #20c997);
}

.btn-warning {
  background: linear-gradient(135deg, #ffc107, #fd7e14);
}

.btn-info {
  background: linear-gradient(135deg, #17a2b8, #0dcaf0);
}

/* ACTIVITIES TABLE */
.activities-card {
  background: white;
  border-radius: 16px;
  padding: 1.5rem;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04);
  height: 100%;
}

.activities-table th {
  border-top: none;
  color: #6c757d;
  font-weight: 600;
  font-size: 0.78rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  padding: 0.75rem 0.5rem;
}

.activities-table td {
  padding: 0.75rem 0.5rem;
  font-size: 0.88rem;
}

.activity-time {
  font-weight: 500;
  color: #2c3e50;
}

.activity-user {
  display: flex;
  align-items: center;
  gap: 0.3rem;
  color: #2c3e50;
  font-weight: 500;
}

.badge {
  padding: 0.35rem 0.65rem;
  font-weight: 500;
  border-radius: 50px;
  font-size: 0.72rem;
}

/* MENU CARD */
.menu-card {
  background: white;
  border-radius: 16px;
  padding: 1.5rem;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04);
  height: 100%;
}

.menu-list {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.menu-item {
  display: flex;
  align-items: center;
  padding: 0.75rem 0.85rem;
  border-radius: 8px;
  text-decoration: none;
  color: #2c3e50;
  transition: all 0.25 ease;
  gap: 0.85rem;
}

.menu-item:hover {
  background: #f8f9fa;
  transform: translateX(4px);
}

.menu-icon {
  width: 38px;
  height: 38px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 0.95rem;
  flex-shrink: 0;
}

.bg-primary-light {
  background: #996600;
}

.bg-success-light {
  background: #28a745;
}

.bg-warning-light {
  background: #ffc107;
}

.bg-info-light {
  background: #17a2b8;
}

.menu-content {
  flex: 1;
  display: flex;
  flex-direction: column;
}

.menu-content strong {
  font-size: 0.88rem;
}

.menu-content small {
  font-size: 0.72rem;
  color: #6c757d;
}

.menu-arrow {
  color: #a0aec0;
  transition: all 0.2s ease;
  font-size: 0.8rem;
}

.menu-item:hover .menu-arrow {
  transform: translateX(3px);
  color: #996600;
}

@media (max-width: 576px) {
  .welcome-card {
    flex-direction: column;
    align-items: flex-start;
  }
}
</style>