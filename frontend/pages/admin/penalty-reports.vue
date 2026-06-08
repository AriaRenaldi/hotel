<template>
  <div class="min-h-screen bg-gradient-to-br from-[#fff8e7] via-[#f8f1dc] to-[#efe2b8] text-slate-900">
    <nav class="sticky top-0 z-50 border-b border-white/70 bg-white/85 backdrop-blur-xl shadow-sm">
      <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-6">
        <NuxtLink to="/admin/dashboard" class="flex items-center gap-3">
          <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-r from-[#6b4f1d] via-[#9c7a2b] to-[#d4af37] text-white shadow-lg">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
          </div>
          <div>
            <p class="text-lg font-bold text-slate-900">HotelKu</p>
            <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Admin Panel</p>
          </div>
        </NuxtLink>
        <div class="flex flex-wrap items-center gap-2 rounded-full border border-amber-100/80 bg-white/80 p-2 text-sm font-medium text-slate-600 shadow-lg shadow-amber-100/40 backdrop-blur">
          <NuxtLink to="/admin/dashboard" class="rounded-full px-4 py-2 transition hover:bg-amber-50 hover:text-slate-900">Dashboard</NuxtLink>
          <NuxtLink to="/admin/rooms" class="rounded-full px-4 py-2 transition hover:bg-amber-50 hover:text-slate-900">Rooms</NuxtLink>
          <NuxtLink to="/admin/room-types" class="rounded-full px-4 py-2 transition hover:bg-amber-50 hover:text-slate-900">Room Types</NuxtLink>
          <NuxtLink to="/admin/facilities" class="rounded-full px-4 py-2 transition hover:bg-amber-50 hover:text-slate-900">Facilities</NuxtLink>
          <NuxtLink to="/admin/users" class="rounded-full px-4 py-2 transition hover:bg-amber-50 hover:text-slate-900">Users</NuxtLink>
          <NuxtLink to="/admin/reports" class="rounded-full px-4 py-2 transition hover:bg-amber-50 hover:text-slate-900">Reports</NuxtLink>
          <NuxtLink to="/admin/penalty-reports" class="rounded-full bg-slate-900 px-4 py-2 font-semibold text-white shadow-md">Penalty Reports</NuxtLink>
          <NuxtLink to="/profile" class="rounded-full px-4 py-2 transition hover:bg-amber-50 hover:text-slate-900">Profil</NuxtLink>
          <button @click="handleLogout" class="rounded-full bg-rose-50 px-4 py-2 font-semibold text-rose-600 transition hover:bg-rose-100">Logout</button>
        </div>
      </div>
    </nav>

    <section class="relative overflow-hidden bg-gradient-to-br from-[#6b4f1d] via-[#9c7a2b] to-[#d4af37] text-white">
      <div class="absolute inset-0 opacity-20">
        <div class="absolute left-8 top-8 h-48 w-48 rounded-full bg-white/15 blur-3xl"></div>
        <div class="absolute right-12 bottom-0 h-64 w-64 rounded-full bg-amber-100/20 blur-3xl"></div>
      </div>
      <div class="relative mx-auto max-w-7xl px-4 py-16 sm:px-6">
        <div class="grid items-center gap-8 lg:grid-cols-[1.2fr_0.8fr]">
          <div>
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-100">Penalty Reports</p>
            <h1 class="mt-4 text-4xl font-bold">Laporan Denda Checkout</h1>
            <p class="mt-4 max-w-2xl text-lg text-amber-50">Pantau pembayaran denda checkout, lihat performa verifikasi resepsionis, dan unduh laporannya.</p>
          </div>
          <div class="grid gap-4 sm:grid-cols-2">
            <div class="rounded-3xl border border-white/15 bg-white/10 px-5 py-4 backdrop-blur">
              <p class="text-xs uppercase tracking-[0.2em] text-amber-100">Periode</p>
              <p class="mt-2 text-lg font-semibold">{{ filters.start_date }}</p>
            </div>
            <div class="rounded-3xl border border-white/15 bg-white/10 px-5 py-4 backdrop-blur">
              <p class="text-xs uppercase tracking-[0.2em] text-amber-100">To</p>
              <p class="mt-2 text-lg font-semibold">{{ filters.end_date }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="relative z-10 mx-auto -mt-8 max-w-7xl px-4 pb-12 sm:px-6">
      <div class="mb-6 rounded-[2rem] border-2 border-amber-200 bg-white/92 p-6 shadow-xl shadow-amber-100/50 ring-2 ring-amber-100/70 backdrop-blur">
        <div class="grid items-end gap-4 md:grid-cols-5">
          <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">Tanggal Mulai</label>
            <input v-model="filters.start_date" type="date" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
          </div>
          <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">Tanggal Akhir</label>
            <input v-model="filters.end_date" type="date" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
          </div>
          <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">Transaksi Oleh Resepsionis</label>
            <select v-model="filters.receptionist_id" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500">
              <option value="">Semua Resepsionis</option>
              <option v-for="r in receptionists" :key="r.id" :value="String(r.id)">{{ r.full_name }}</option>
            </select>
          </div>
          <button @click="loadReport(1)" class="rounded-2xl bg-slate-900 py-3 text-sm font-semibold text-white">Tampilkan Laporan Denda</button>
          <button @click="downloadExcel" class="rounded-2xl bg-emerald-600 py-3 text-sm font-semibold text-white">Download Excel</button>
        </div>
      </div>

      <div class="mb-6 grid gap-6 md:grid-cols-4">
        <div class="rounded-2xl border-2 border-amber-200 bg-white/90 p-5 shadow-lg ring-1 ring-amber-100/60">
          <p class="text-sm text-slate-500">Total Transaksi</p>
          <p class="mt-2 text-2xl font-bold text-slate-900">{{ summary.total_transactions || 0 }}</p>
        </div>
        <div class="rounded-2xl border-2 border-amber-200 bg-white/90 p-5 shadow-lg ring-1 ring-amber-100/60">
          <p class="text-sm text-slate-500">Pending</p>
          <p class="mt-2 text-2xl font-bold text-amber-700">{{ summary.pending_transactions || 0 }}</p>
        </div>
        <div class="rounded-2xl border-2 border-amber-200 bg-white/90 p-5 shadow-lg ring-1 ring-amber-100/60">
          <p class="text-sm text-slate-500">Total Denda</p>
          <p class="mt-2 text-2xl font-bold text-rose-700">Rp {{ formatPrice(summary.total_penalty_amount || 0) }}</p>
        </div>
        <div class="rounded-2xl border-2 border-amber-200 bg-white/90 p-5 shadow-lg ring-1 ring-amber-100/60">
          <p class="text-sm text-slate-500">Denda Terverifikasi</p>
          <p class="mt-2 text-2xl font-bold text-emerald-700">Rp {{ formatPrice(summary.verified_penalty_amount || 0) }}</p>
        </div>
      </div>

      <div class="mb-6 rounded-[2rem] border-2 border-amber-200 bg-white/92 p-6 shadow-xl ring-2 ring-amber-100/70">
        <p class="text-xs font-semibold uppercase tracking-[0.25em] text-amber-700">Penalty Chart</p>
        <h2 class="mt-2 text-2xl font-bold text-slate-900">Grafik Denda per Hari</h2>
        <div class="mt-6 flex items-end gap-3 overflow-x-auto pb-3">
          <div v-for="point in chart" :key="point.date" class="min-w-[70px] text-center">
            <div class="mx-auto flex h-40 w-10 items-end rounded-t-lg bg-amber-100">
              <div class="w-full rounded-t-lg bg-amber-500 transition-all" :style="{ height: `${calculateHeight(point.verified_amount)}%` }"></div>
            </div>
            <p class="mt-2 text-[11px] text-slate-500">{{ formatShortDate(point.date) }}</p>
            <p class="text-[11px] font-semibold text-slate-700">Rp {{ formatCompact(point.verified_amount) }}</p>
          </div>
          <p v-if="chart.length === 0" class="text-sm text-slate-500">Belum ada data chart pada periode ini.</p>
        </div>
      </div>

      <div class="overflow-hidden rounded-[2rem] border-2 border-amber-200 bg-white/92 shadow-2xl shadow-amber-100/60 ring-2 ring-amber-100/70 backdrop-blur">
        <div class="border-b-2 border-amber-200 bg-gradient-to-r from-amber-50 to-white px-8 py-6">
          <h2 class="text-2xl font-bold text-slate-900">Detail Pembayaran Denda</h2>
        </div>
        <div v-if="loading" class="py-12 text-center">
          <div class="inline-block h-8 w-8 animate-spin rounded-full border-b-2 border-amber-600"></div>
        </div>
        <div v-else class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-amber-50/85">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Booking</th>
                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Tamu</th>
                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Tanggal</th>
                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Nominal</th>
                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Metode</th>
                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Transaksi Oleh</th>
                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-amber-100/70">
              <tr v-for="payment in payments" :key="payment.id" class="transition hover:bg-amber-50/40">
                <td class="px-6 py-4 text-sm font-semibold text-slate-900">{{ payment.booking?.booking_number }}</td>
                <td class="px-6 py-4 text-sm text-slate-600">{{ payment.booking?.guest?.full_name || '-' }}</td>
                <td class="px-6 py-4 text-sm text-slate-600">{{ formatDate(payment.created_at) }}</td>
                <td class="px-6 py-4 text-sm font-semibold text-amber-700">Rp {{ formatPrice(payment.amount) }}</td>
                <td class="px-6 py-4 text-sm uppercase text-slate-700">{{ payment.payment_method }}</td>
                <td class="px-6 py-4 text-sm text-slate-600">{{ getTransactionReceptionistName(payment) }}</td>
                <td class="px-6 py-4">
                  <span :class="statusClass(payment.status)" class="rounded-full px-3 py-1 text-xs font-semibold">{{ payment.status }}</span>
                </td>
              </tr>
              <tr v-if="!loading && payments.length === 0">
                <td colspan="7" class="px-6 py-8 text-center text-slate-500">Tidak ada transaksi denda pada periode ini.</td>
              </tr>
            </tbody>
          </table>
        </div>
        <AppPagination
          v-if="!loading && pagination.total > 0"
          theme="amber"
          :model-value="pagination.current_page"
          :total-pages="pagination.last_page || 1"
          :from="pagination.from || 0"
          :to="pagination.to || 0"
          :total="pagination.total || 0"
          @update:model-value="changePage"
        />
      </div>
    </section>
  </div>
</template>

<script setup>
import { useAuthStore } from '~/stores/auth'
import { useAdminStore } from '~/stores/admin'

definePageMeta({ middleware: 'admin' })
const API_BASE = 'http://127.0.0.1:8000/api'

const authStore = useAuthStore()
const adminStore = useAdminStore()

const loading = ref(false)
const summary = ref({})
const payments = ref([])
const chart = ref([])
const receptionists = ref([])
const perPage = 6
const pagination = ref({
  current_page: 1,
  last_page: 1,
  per_page: perPage,
  total: 0,
  from: 0,
  to: 0,
})

const toDateInput = (value = new Date()) => {
  const d = new Date(value)
  if (Number.isNaN(d.getTime())) return ''
  const year = d.getFullYear()
  const month = String(d.getMonth() + 1).padStart(2, '0')
  const day = String(d.getDate()).padStart(2, '0')
  return `${year}-${month}-${day}`
}

const todayDate = new Date()
const fourteenDaysAgo = new Date(todayDate)
fourteenDaysAgo.setDate(todayDate.getDate() - 13)
const filters = reactive({ start_date: toDateInput(fourteenDaysAgo), end_date: toDateInput(todayDate), receptionist_id: '' })

const handleLogout = async () => {
  await authStore.logout()
  await navigateTo('/login')
}

const formatPrice = (value) => new Intl.NumberFormat('id-ID').format(Number(value || 0))
const formatCompact = (value) => {
  const numeric = Number(value || 0)
  if (numeric >= 1000000) return `${(numeric / 1000000).toFixed(1)}jt`
  if (numeric >= 1000) return `${(numeric / 1000).toFixed(0)}rb`
  return `${numeric}`
}
const formatDate = (value) => value ? new Date(value).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : '-'
const formatShortDate = (value) => value ? new Date(value).toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit' }) : '-'
const statusClass = (status) => ({
  pending: 'bg-yellow-100 text-yellow-800',
  verified: 'bg-emerald-100 text-emerald-800',
  rejected: 'bg-rose-100 text-rose-800',
}[status] || 'bg-gray-100 text-gray-800')
const getTransactionReceptionistName = (payment) => {
  if (payment?.verifier?.full_name) return payment.verifier.full_name
  if (String(payment?.payment_method || '').toLowerCase() === 'cash') return 'Tunai (tanpa verifier)'
  return '-'
}

const calculateHeight = (amount) => {
  const maxAmount = Math.max(...chart.value.map((item) => Number(item.verified_amount || 0)), 1)
  return Math.max(6, Math.round((Number(amount || 0) / maxAmount) * 100))
}

const normalizeChartToFourteenDays = (rawChart = [], endDateValue) => {
  const endDate = new Date(endDateValue)
  const safeEnd = Number.isNaN(endDate.getTime()) ? new Date() : endDate

  const map = new Map(
    rawChart.map((item) => [String(item?.date || ''), item])
  )

  const points = []
  for (let offset = 13; offset >= 0; offset -= 1) {
    const d = new Date(safeEnd)
    d.setDate(safeEnd.getDate() - offset)
    const key = toDateInput(d)
    const row = map.get(key)

    points.push({
      date: key,
      total_transactions: Number(row?.total_transactions || 0),
      total_amount: Number(row?.total_amount || 0),
      verified_amount: Number(row?.verified_amount || 0),
    })
  }

  return points
}

const loadReport = async (page = 1) => {
  loading.value = true
  const result = await adminStore.getLateCheckoutPenaltyReport(
    filters.start_date,
    filters.end_date,
    authStore.token,
    page,
    perPage,
    filters.receptionist_id
  )
  if (result.success) {
    summary.value = result.data?.summary || {}
    payments.value = result.data?.payments || []
    chart.value = normalizeChartToFourteenDays(result.data?.chart || [], filters.end_date)
    pagination.value = result.data?.pagination || {
      current_page: 1,
      last_page: 1,
      per_page: perPage,
      total: 0,
      from: 0,
      to: 0,
    }
  } else {
    summary.value = {}
    payments.value = []
    chart.value = normalizeChartToFourteenDays([], filters.end_date)
    pagination.value = {
      current_page: 1,
      last_page: 1,
      per_page: perPage,
      total: 0,
      from: 0,
      to: 0,
    }
  }
  loading.value = false
}

const changePage = async (page) => {
  if (page < 1 || page > pagination.value.last_page) return
  await loadReport(page)
}

const toCsvCell = (value) => {
  const text = String(value ?? '')
  const escaped = text.replace(/"/g, '""')
  return `"${escaped}"`
}

const downloadExcel = async () => {
  const result = await adminStore.downloadLateCheckoutPenaltyReportExcel(
    filters.start_date,
    filters.end_date,
    authStore.token,
    filters.receptionist_id
  )
  if (!result.success) {
    alert(result.message || 'Gagal mengunduh laporan denda')
    return
  }

  const records = Array.isArray(result.data?.records) ? result.data.records : []
  if (records.length === 0) {
    alert('Tidak ada data laporan denda pada periode ini.')
    return
  }

  const headers = Object.keys(records[0])
  const rows = records.map((row) => headers.map((header) => toCsvCell(row[header])).join(','))
  const csvContent = [headers.join(','), ...rows].join('\n')

  const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' })
  const url = URL.createObjectURL(blob)
  const link = document.createElement('a')
  link.href = url
  link.download = `admin-penalty-report-${filters.start_date}-to-${filters.end_date}.csv`
  link.click()
  URL.revokeObjectURL(url)
}

const loadReceptionists = async () => {
  try {
    const response = await $fetch(`${API_BASE}/admin/users/receptionists`, {
      headers: { Authorization: `Bearer ${authStore.token}` },
    })
    if (response?.success) {
      receptionists.value = Array.isArray(response.data) ? response.data : []
    } else {
      receptionists.value = []
    }
  } catch {
    receptionists.value = []
  }
}

onMounted(async () => {
  await Promise.all([loadReceptionists(), loadReport()])
})
</script>
