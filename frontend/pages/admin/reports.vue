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
          <NuxtLink to="/admin/reports" class="rounded-full bg-slate-900 px-4 py-2 font-semibold text-white shadow-md">Reports</NuxtLink>
          <NuxtLink to="/admin/penalty-reports" class="rounded-full px-4 py-2 transition hover:bg-amber-50 hover:text-slate-900">Penalty Reports</NuxtLink>
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
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-100">Reports Overview</p>
            <h1 class="mt-4 text-4xl font-bold">Laporan Pendapatan</h1>
            <p class="mt-4 max-w-2xl text-lg text-amber-50">Lihat ringkasan pendapatan hotel, filter periode, dan unduh laporan dalam tampilan emas yang lebih eksklusif.</p>
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
      <div class="mb-8 rounded-[2rem] border border-white/70 bg-white/92 p-6 shadow-xl shadow-amber-100/50 backdrop-blur">
        <div class="grid items-end gap-4 md:grid-cols-3">
          <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">Tanggal Mulai</label>
            <input v-model="filters.start_date" type="date" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500">
          </div>
          <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">Tanggal Akhir</label>
            <input v-model="filters.end_date" type="date" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500">
          </div>
          <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">Transaksi Oleh Resepsionis</label>
            <select v-model="filters.receptionist_id" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500">
              <option value="">Semua Resepsionis</option>
              <option v-for="r in receptionists" :key="r.id" :value="String(r.id)">{{ r.full_name }}</option>
            </select>
          </div>
          <div>
            <button @click="loadReport" class="w-full rounded-2xl bg-gradient-to-r from-[#6b4f1d] via-[#9c7a2b] to-[#d4af37] py-3 text-sm font-semibold text-white shadow-lg shadow-amber-200 transition duration-300 hover:-translate-y-0.5 hover:shadow-xl">Tampilkan</button>
          </div>
          <div>
            <button @click="downloadReport" class="w-full rounded-2xl bg-gradient-to-r from-emerald-500 to-emerald-600 py-3 text-sm font-semibold text-white shadow-lg shadow-emerald-200 transition duration-300 hover:-translate-y-0.5 hover:shadow-xl">Download Excel</button>
          </div>
        </div>
      </div>

      <div class="mb-8 grid gap-6 md:grid-cols-4">
        <div class="rounded-[1.75rem] border border-white/70 bg-white/90 p-6 shadow-xl shadow-amber-100/60 backdrop-blur transition duration-300 hover:-translate-y-1 hover:shadow-2xl">
          <p class="text-sm font-medium text-slate-500">Total Pendapatan</p>
          <p class="mt-2 text-2xl font-bold text-emerald-600">Rp {{ formatPrice(reportData.total_revenue || 0) }}</p>
        </div>
        <div class="rounded-[1.75rem] border border-white/70 bg-white/90 p-6 shadow-xl shadow-amber-100/60 backdrop-blur transition duration-300 hover:-translate-y-1 hover:shadow-2xl">
          <p class="text-sm font-medium text-slate-500">Total Pemesanan</p>
          <p class="mt-2 text-2xl font-bold text-slate-900">{{ reportData.total_bookings || 0 }}</p>
        </div>
        <div class="rounded-[1.75rem] border border-white/70 bg-white/90 p-6 shadow-xl shadow-amber-100/60 backdrop-blur transition duration-300 hover:-translate-y-1 hover:shadow-2xl">
          <p class="text-sm font-medium text-slate-500">Terverifikasi</p>
          <p class="mt-2 text-2xl font-bold text-amber-700">{{ reportData.verified_bookings || 0 }}</p>
        </div>
        <div class="rounded-[1.75rem] border border-white/70 bg-white/90 p-6 shadow-xl shadow-amber-100/60 backdrop-blur transition duration-300 hover:-translate-y-1 hover:shadow-2xl">
          <p class="text-sm font-medium text-slate-500">Periode</p>
          <p class="mt-2 text-lg font-bold text-slate-900">{{ filters.start_date }} s/d {{ filters.end_date }}</p>
        </div>
      </div>

      <div class="overflow-hidden rounded-[2rem] border border-white/70 bg-white/92 shadow-2xl shadow-amber-100/60 backdrop-blur">
        <div class="border-b border-amber-100 bg-gradient-to-r from-amber-50 to-white px-8 py-6">
          <p class="text-xs font-semibold uppercase tracking-[0.25em] text-amber-700">Revenue Table</p>
          <h2 class="mt-2 text-2xl font-bold text-slate-900">Detail Pemesanan</h2>
        </div>
        <div v-if="loading" class="py-12 text-center">
          <div class="inline-block h-8 w-8 animate-spin rounded-full border-b-2 border-amber-600"></div>
        </div>
        <div v-else class="overflow-x-auto">
          <table class="w-full min-w-[1180px]">
            <thead class="bg-amber-50/70">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">No. Booking</th>
                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Tamu</th>
                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Check-in</th>
                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Check-out</th>
                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Total</th>
                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Status Bayar</th>
                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Transaksi Oleh</th>
                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-amber-100/70">
              <tr v-for="b in paginatedBookings" :key="b.id" class="transition hover:bg-amber-50/40">
                <td class="px-6 py-4 text-sm font-semibold text-slate-900">{{ b.booking_number }}</td>
                <td class="px-6 py-4 text-sm text-slate-600">{{ b.guest?.full_name || '-' }}</td>
                <td class="px-6 py-4 text-sm text-slate-600">{{ formatDate(b.check_in_date) }}</td>
                <td class="px-6 py-4 text-sm text-slate-600">{{ formatDate(getCheckoutDisplayDate(b)) }}</td>
                <td class="px-6 py-4 text-sm font-semibold text-amber-700">Rp {{ formatPrice(b.total_amount) }}</td>
                <td class="px-6 py-4">
                  <span :class="getPaymentClass(b.payment_status)" class="rounded-full px-3 py-1 text-xs font-semibold">{{ getPaymentText(b.payment_status) }}</span>
                </td>
                <td class="px-6 py-4 text-sm text-slate-600">{{ getTransactionReceptionistName(b) }}</td>
                <td class="px-6 py-4">
                  <span :class="getStatusClass(b)" class="rounded-full px-3 py-1 text-xs font-semibold">{{ getStatusText(b) }}</span>
                </td>
              </tr>
            </tbody>
          </table>
          <AppPagination
            v-if="bookings.length > 0 && totalPages > 1"
            theme="amber"
            :model-value="currentPage"
            :total-pages="totalPages"
            :from="displayFrom"
            :to="displayTo"
            :total="bookings.length"
            @update:model-value="setCurrentPage"
          />
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { useAuthStore } from '~/stores/auth'

const API_BASE = 'http://127.0.0.1:8000/api'

definePageMeta({ middleware: 'admin' })

const authStore = useAuthStore()

const handleLogout = async () => {
  await authStore.logout()
  await navigateTo('/login')
}
const loading = ref(false)
const bookings = ref([])
const reportData = ref({})
const receptionists = ref([])
const ITEMS_PER_PAGE = 6
const currentPage = ref(1)

const today = new Date().toISOString().split('T')[0]
const firstDayOfMonth = new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().split('T')[0]

const filters = reactive({
  start_date: firstDayOfMonth,
  end_date: today,
  receptionist_id: '',
})

const formatPrice = (p) => new Intl.NumberFormat('id-ID').format(p)
const formatDate = (d) => {
  if (!d) return '-'
  const normalized = typeof d === 'string' ? d.replace(' ', 'T') : d
  const date = new Date(normalized)
  if (Number.isNaN(date.getTime())) return typeof d === 'string' ? d.slice(0, 10) : '-'
  return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
}
const getCheckoutDisplayDate = (booking) => booking?.actual_check_out_at || booking?.check_out_date || null

const getPaymentClass = (s) => ({
  pending: 'bg-yellow-100 text-yellow-800',
  paid: 'bg-orange-100 text-orange-800',
  verified: 'bg-emerald-100 text-emerald-800',
  rejected: 'bg-rose-100 text-rose-800',
  cancelled: 'bg-rose-100 text-rose-800',
}[s] || 'bg-gray-100 text-gray-800')
const getPaymentText = (s) => ({
  pending: 'Menunggu',
  paid: 'Sudah Bayar',
  verified: 'Lunas',
  rejected: 'Ditolak',
  cancelled: 'Dibatalkan',
}[s] || s)

const deriveBookingStatus = (booking) => {
  const bookingStatus = String(booking?.booking_status || '').toLowerCase()
  const paymentStatus = String(booking?.payment_status || '').toLowerCase()
  const paymentMethod = String(booking?.payment_method || '').toLowerCase()

  if (bookingStatus === 'cancelled' || paymentStatus === 'cancelled') return 'cancelled'
  if (bookingStatus === 'checked_out') return 'checked_out'
  if (bookingStatus === 'checked_in') return 'checked_in'
  if (paymentStatus === 'rejected') return 'rejected'

  if (bookingStatus === 'confirmed') {
    const isOnlinePayment = ['qris', 'transfer', 'bank_transfer'].includes(paymentMethod)
    const waitingPayment = ['pending', 'paid', 'unpaid', ''].includes(paymentStatus)
    if (isOnlinePayment && waitingPayment) return 'waiting_payment'
    return 'waiting_checkin'
  }

  return bookingStatus || 'unknown'
}

const getStatusClass = (booking) => ({
  waiting_payment: 'bg-amber-100 text-amber-800',
  waiting_checkin: 'bg-blue-100 text-[#1b456f]',
  checked_in: 'bg-emerald-100 text-emerald-800',
  checked_out: 'bg-slate-100 text-slate-800',
  cancelled: 'bg-rose-100 text-rose-800',
  rejected: 'bg-rose-100 text-rose-800',
}[deriveBookingStatus(booking)] || 'bg-gray-100 text-gray-800')

const getStatusText = (booking) => ({
  waiting_payment: 'Menunggu Pembayaran',
  waiting_checkin: 'Menunggu Check-in',
  checked_in: 'Check-in',
  checked_out: 'Selesai',
  cancelled: 'Dibatalkan',
  rejected: 'Ditolak',
}[deriveBookingStatus(booking)] || deriveBookingStatus(booking))
const getBookingPayment = (booking) => {
  if (!Array.isArray(booking?.payments)) return null
  return booking.payments
    .filter((payment) => payment?.payment_type === 'booking')
    .sort((a, b) => Number(b?.id || 0) - Number(a?.id || 0))[0] || null
}
const getTransactionReceptionistName = (booking) => {
  const payment = getBookingPayment(booking)
  if (payment?.status === 'verified' && payment?.verifier?.full_name) return payment.verifier.full_name
  if (booking?.check_in_approver?.full_name) return booking.check_in_approver.full_name
  if (booking?.check_out_processor?.full_name) return booking.check_out_processor.full_name
  if (booking?.payment_method === 'cash') return 'Tunai (tanpa verifier)'
  return '-'
}
const totalPages = computed(() => {
  const pages = Math.ceil(bookings.value.length / ITEMS_PER_PAGE)
  return Math.max(1, pages)
})

const paginatedBookings = computed(() => {
  const start = (currentPage.value - 1) * ITEMS_PER_PAGE
  const end = start + ITEMS_PER_PAGE
  return bookings.value.slice(start, end)
})

const displayFrom = computed(() => {
  if (bookings.value.length === 0) return 0
  return (currentPage.value - 1) * ITEMS_PER_PAGE + 1
})

const displayTo = computed(() => {
  if (bookings.value.length === 0) return 0
  return Math.min(currentPage.value * ITEMS_PER_PAGE, bookings.value.length)
})

const setCurrentPage = (page) => { currentPage.value = page }

watch(bookings, () => {
  if (currentPage.value > totalPages.value) {
    currentPage.value = totalPages.value
  }
})

const loadReport = async () => {
  loading.value = true
  try {
    const query = new URLSearchParams({
      start_date: filters.start_date,
      end_date: filters.end_date,
      ...(filters.receptionist_id ? {
        payment_verified_by: filters.receptionist_id,
        checkin_approved_by: filters.receptionist_id,
        checkout_processed_by: filters.receptionist_id,
      } : {}),
    })

    const response = await $fetch(`${API_BASE}/admin/reports/revenue?${query.toString()}`, {
      headers: { Authorization: `Bearer ${authStore.token}` }
    })
    if (response.success) {
      reportData.value = response.data
      bookings.value = response.data.bookings || []
      currentPage.value = 1
    }
  } catch (e) {
    console.error(e)
  }
  loading.value = false
}

const downloadReport = async () => {
  try {
    const query = new URLSearchParams({
      start_date: filters.start_date,
      end_date: filters.end_date,
      type: 'revenue',
      ...(filters.receptionist_id ? {
        payment_verified_by: filters.receptionist_id,
        checkin_approved_by: filters.receptionist_id,
        checkout_processed_by: filters.receptionist_id,
      } : {}),
    })

    const response = await $fetch(`${API_BASE}/admin/reports/export/excel?${query.toString()}`, {
      headers: { Authorization: `Bearer ${authStore.token}` }
    })
    if (response.success) {
      const csv = [Object.keys(response.data.records[0] || {}).join(','), ...response.data.records.map(r => Object.values(r).join(','))].join('\n')
      const blob = new Blob([csv], { type: 'text/csv' })
      const url = URL.createObjectURL(blob)
      const a = document.createElement('a')
      a.href = url
      a.download = `laporan-${filters.start_date}-${filters.end_date}.csv`
      a.click()
    }
  } catch (e) {
    alert('Gagal mengunduh laporan')
  }
}

const loadReceptionists = async () => {
  try {
    const response = await $fetch(`${API_BASE}/admin/users/receptionists`, {
      headers: { Authorization: `Bearer ${authStore.token}` }
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
