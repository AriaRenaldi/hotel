<template>
  <div class="min-h-screen bg-gradient-to-br from-[#fff8e7] via-[#f8f1dc] to-[#efe2b8] text-slate-900">
    <nav class="sticky top-0 z-50 border-b border-white/70 bg-white/85 backdrop-blur-xl shadow-sm">
      <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-6">
        <NuxtLink to="/admin/dashboard" class="flex items-center gap-3">
          <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-r from-[#6b4f1d] via-[#9c7a2b] to-[#d4af37] text-white shadow-lg">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
          </div>
          <div>
            <p class="text-lg font-bold text-slate-900">HotelKu</p>
            <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Admin Panel</p>
          </div>
        </NuxtLink>

        <div class="flex flex-wrap items-center gap-2 rounded-full border border-amber-100/80 bg-white/80 p-2 text-sm font-medium text-slate-600 shadow-lg shadow-amber-100/40 backdrop-blur">
          <NuxtLink to="/admin/dashboard" class="rounded-full bg-slate-900 px-4 py-2 font-semibold text-white shadow-md">Dashboard</NuxtLink>
          <NuxtLink to="/admin/rooms" class="rounded-full px-4 py-2 transition hover:bg-amber-50 hover:text-slate-900">Rooms</NuxtLink>
          <NuxtLink to="/admin/room-types" class="rounded-full px-4 py-2 transition hover:bg-amber-50 hover:text-slate-900">Room Types</NuxtLink>
          <NuxtLink to="/admin/facilities" class="rounded-full px-4 py-2 transition hover:bg-amber-50 hover:text-slate-900">Facilities</NuxtLink>
          <NuxtLink to="/admin/users" class="rounded-full px-4 py-2 transition hover:bg-amber-50 hover:text-slate-900">Users</NuxtLink>
          <NuxtLink to="/admin/reports" class="rounded-full px-4 py-2 transition hover:bg-amber-50 hover:text-slate-900">Reports</NuxtLink>
          <NuxtLink to="/admin/penalty-reports" class="rounded-full px-4 py-2 transition hover:bg-amber-50 hover:text-slate-900">Penalty Reports</NuxtLink>
          <NuxtLink to="/profile" class="rounded-full px-4 py-2 transition hover:bg-amber-50 hover:text-slate-900">Profil</NuxtLink>
          <button @click="handleLogout" class="rounded-full bg-rose-50 px-4 py-2 font-semibold text-rose-600 transition hover:bg-rose-100">Logout</button>
        </div>
      </div>
    </nav>

    <section class="relative overflow-hidden bg-gradient-to-br from-[#6b4f1d] via-[#9c7a2b] to-[#d4af37] text-white">
      <div class="absolute inset-0 opacity-20">
        <div class="absolute left-10 top-8 h-52 w-52 rounded-full bg-white/15 blur-3xl"></div>
        <div class="absolute right-10 bottom-0 h-72 w-72 rounded-full bg-amber-100/20 blur-3xl"></div>
      </div>
      <div class="relative mx-auto max-w-7xl px-4 py-16 sm:px-6">
        <div class="grid items-center gap-8 lg:grid-cols-[1.2fr_0.8fr]">
          <div>
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-100">Admin Overview</p>
            <h1 class="mt-4 text-4xl font-bold">Dashboard Admin</h1>
            <p class="mt-4 max-w-2xl text-lg text-amber-50">Kelola hotel, pantau performa bisnis, dan lihat ringkasan pendapatan dalam tampilan emas yang lebih mewah dan profesional.</p>
          </div>
          <div class="grid gap-4 sm:grid-cols-2">
            <div class="rounded-3xl border border-white/15 bg-white/10 px-5 py-4 backdrop-blur shadow-lg shadow-black/10">
              <p class="text-xs uppercase tracking-[0.2em] text-amber-100">Welcome</p>
              <div class="mt-2 flex items-center gap-3">
                <div class="h-10 w-10 overflow-hidden rounded-xl border border-white/30 bg-white/20">
                  <img v-if="authStore.user?.photo_url" :src="authStore.user.photo_url" alt="Foto profil admin" class="h-full w-full object-cover">
                  <div v-else class="flex h-full w-full items-center justify-center text-sm font-bold text-white">
                    {{ (authStore.user?.full_name || 'A').charAt(0).toUpperCase() }}
                  </div>
                </div>
                <p class="text-lg font-semibold">{{ authStore.user?.full_name }}</p>
              </div>
            </div>
            <div class="rounded-3xl border border-white/15 bg-white/10 px-5 py-4 backdrop-blur shadow-lg shadow-black/10">
              <p class="text-xs uppercase tracking-[0.2em] text-amber-100">Control</p>
              <p class="mt-2 text-lg font-semibold">Premium Management</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="relative z-10 mx-auto -mt-8 max-w-7xl px-4 sm:px-6">
      <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-[1.75rem] border border-white/70 bg-white/90 p-6 shadow-xl shadow-amber-100/60 backdrop-blur transition duration-300 hover:-translate-y-1 hover:shadow-2xl">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-slate-500">Total Kamar</p>
              <p class="mt-2 text-3xl font-bold text-slate-900">{{ stats.totalRooms }}</p>
            </div>
            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-amber-100 to-white text-amber-700 shadow-sm">
              <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            </div>
          </div>
        </div>
        <div class="rounded-[1.75rem] border border-white/70 bg-white/90 p-6 shadow-xl shadow-amber-100/60 backdrop-blur transition duration-300 hover:-translate-y-1 hover:shadow-2xl">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-slate-500">Kamar Tersedia</p>
              <p class="mt-2 text-3xl font-bold text-emerald-600">{{ stats.availableRooms }}</p>
            </div>
            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-emerald-100 to-white text-emerald-600 shadow-sm">
              <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
          </div>
        </div>
        <div class="rounded-[1.75rem] border border-white/70 bg-white/90 p-6 shadow-xl shadow-amber-100/60 backdrop-blur transition duration-300 hover:-translate-y-1 hover:shadow-2xl">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-slate-500">Total Pemesanan</p>
              <p class="mt-2 text-3xl font-bold text-slate-900">{{ stats.totalBookings }}</p>
            </div>
            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-yellow-100 to-white text-yellow-700 shadow-sm">
              <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
          </div>
        </div>
        <div class="rounded-[1.75rem] border border-white/70 bg-white/90 p-6 shadow-xl shadow-amber-100/60 backdrop-blur transition duration-300 hover:-translate-y-1 hover:shadow-2xl">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-slate-500">Total Pendapatan</p>
              <p class="mt-2 text-2xl font-bold text-amber-700">Rp {{ formatPrice(stats.totalRevenue) }}</p>
            </div>
            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-amber-100 to-white text-amber-700 shadow-sm">
              <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6">
      <div class="overflow-hidden rounded-[2rem] border border-white/70 bg-white/92 p-8 shadow-2xl shadow-amber-100/60 backdrop-blur">
        <div class="mb-6 flex items-center justify-between">
          <div>
            <p class="text-xs font-semibold uppercase tracking-[0.25em] text-amber-700">Revenue Insight</p>
            <h2 class="mt-2 text-2xl font-bold text-slate-900">Grafik Pendapatan 7 Hari Terakhir</h2>
          </div>
        </div>

        <div class="h-72 rounded-2xl border border-amber-200 bg-amber-50/20 p-4">
          <div class="mb-4 flex items-center justify-between px-1 text-sm">
            <p class="text-amber-700">
              Total 7 hari:
              <span class="font-semibold text-amber-800">Rp {{ formatPrice(totalRevenueWeek) }}</span>
            </p>
            <p class="text-amber-700">
              Hari ini:
              <span class="font-semibold text-amber-800">Rp {{ formatPrice(todayRevenue) }}</span>
            </p>
          </div>

          <div v-if="!isRevenueEmpty" class="flex h-56 items-end justify-between gap-2 px-1">
            <div
              v-for="(item, index) in revenueData"
              :key="`${item.originalDate}-${index}`"
              class="group flex flex-1 flex-col items-center justify-end h-full"
            >
              <div
                :class="[
                  'relative w-10 rounded-t-xl shadow-md transition-all duration-300',
                  item.isToday
                    ? 'bg-gradient-to-t from-amber-600 to-yellow-500 ring-2 ring-amber-300'
                    : 'bg-gradient-to-t from-amber-500 to-yellow-400 hover:brightness-110'
                ]"
                :style="{ height: `${getBarHeight(item.revenue)}%`, minHeight: '20px' }"
              >
                <div class="pointer-events-none absolute -top-10 left-1/2 -translate-x-1/2 rounded-lg bg-slate-900 px-2 py-1 text-[10px] font-semibold text-white opacity-0 transition-opacity duration-200 group-hover:opacity-100 whitespace-nowrap">
                  Rp {{ formatPrice(item.revenue) }}
                </div>
              </div>
              <p class="mt-2 text-xs font-semibold text-slate-700">{{ item.day }}</p>
              <p class="text-[11px] text-slate-500">{{ item.date }}</p>
            </div>
          </div>

          <div v-else class="flex h-56 items-center justify-center rounded-xl border border-dashed border-amber-200 bg-white/60">
            <p class="text-center text-sm text-slate-500">Belum ada transaksi terverifikasi dalam 7 hari terakhir.</p>
          </div>
        </div>
      </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 pb-12 sm:px-6">
      <div class="overflow-hidden rounded-[2rem] border border-white/70 bg-white/92 shadow-2xl shadow-amber-100/60 backdrop-blur">
        <div class="border-b border-amber-100 bg-gradient-to-r from-amber-50 to-white px-8 py-6 flex items-center justify-between">
          <div>
            <p class="text-xs font-semibold uppercase tracking-[0.25em] text-amber-700">Recent Bookings</p>
            <h2 class="mt-2 text-2xl font-bold text-slate-900">Pemesanan Terbaru</h2>
          </div>
          <NuxtLink to="/admin/reports" class="rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800">Lihat Semua</NuxtLink>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-amber-50/70">
              <tr>
                <th class="px-8 py-4 text-left text-xs font-bold uppercase tracking-[0.25em] text-slate-500">No. Booking</th>
                <th class="px-8 py-4 text-left text-xs font-bold uppercase tracking-[0.25em] text-slate-500">Tamu</th>
                <th class="px-8 py-4 text-left text-xs font-bold uppercase tracking-[0.25em] text-slate-500">Check-in</th>
                <th class="px-8 py-4 text-left text-xs font-bold uppercase tracking-[0.25em] text-slate-500">Check-out</th>
                <th class="px-8 py-4 text-left text-xs font-bold uppercase tracking-[0.25em] text-slate-500">Total</th>
                <th class="px-8 py-4 text-left text-xs font-bold uppercase tracking-[0.25em] text-slate-500">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-amber-100/70">
              <tr v-for="booking in recentBookings" :key="booking.id" class="transition hover:bg-amber-50/40">
                <td class="px-8 py-4 text-sm font-semibold text-slate-900">{{ booking.booking_number }}</td>
                <td class="px-8 py-4 text-sm text-slate-600">{{ booking.guest?.full_name }}</td>
                <td class="px-8 py-4 text-sm text-slate-600">{{ formatDate(booking.check_in_date) }}</td>
                <td class="px-8 py-4 text-sm text-slate-600">{{ formatDate(getCheckoutDisplayDate(booking)) }}</td>
                <td class="px-8 py-4 text-sm font-semibold text-amber-700">Rp {{ formatPrice(booking.total_amount) }}</td>
                <td class="px-8 py-4">
                  <span :class="getBookingStatusClass(booking)" class="rounded-full px-3 py-1.5 text-xs font-semibold">
                    {{ getBookingStatusText(booking) }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { useAuthStore } from '~/stores/auth'

definePageMeta({
  middleware: 'admin'
})

const authStore = useAuthStore()

const handleLogout = async () => {
  await authStore.logout()
  await navigateTo('/login')
}
const stats = ref({
  totalRooms: 0,
  availableRooms: 0,
  totalBookings: 0,
  totalRevenue: 0
})

const recentBookings = ref([])
const revenueData = ref([])
const isRevenueEmpty = ref(false)

const totalRevenueWeek = computed(() => {
  if (!revenueData.value.length) return 0
  return revenueData.value.reduce((total, item) => total + (Number(item.revenue) || 0), 0)
})

const todayRevenue = computed(() => {
  const today = new Date().toISOString().split('T')[0]
  const todayItem = revenueData.value.find((item) => item.originalDate === today)
  return Number(todayItem?.revenue || 0)
})

const getBarHeight = (value) => {
  const maxRevenue = Math.max(...revenueData.value.map((item) => Number(item.revenue) || 0), 0)
  if (maxRevenue <= 0) return 20

  const minHeight = 20
  const maxHeight = 90
  const percentage = (Number(value) / maxRevenue) * 100
  const height = minHeight + (percentage * (maxHeight - minHeight) / 100)

  return Math.max(minHeight, Math.min(maxHeight, height))
}

const formatPrice = (price) => {
  return new Intl.NumberFormat('id-ID').format(price)
}

const formatDate = (date) => {
  if (!date) return '-'
  const normalized = typeof date === 'string' ? date.replace(' ', 'T') : date
  const parsedDate = new Date(normalized)

  if (Number.isNaN(parsedDate.getTime())) return typeof date === 'string' ? date.slice(0, 10) : '-'

  return parsedDate.toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric'
  })
}

const getCheckoutDisplayDate = (booking) => booking?.actual_check_out_at || booking?.check_out_date || null

const deriveDashboardStatus = (booking) => {
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

const getBookingStatusClass = (booking) => {
  const classes = {
    waiting_payment: 'bg-amber-100 text-amber-800',
    waiting_checkin: 'bg-blue-100 text-[#1b456f]',
    checked_in: 'bg-emerald-100 text-emerald-800',
    checked_out: 'bg-slate-100 text-slate-800',
    cancelled: 'bg-rose-100 text-rose-800',
    rejected: 'bg-rose-100 text-rose-800'
  }
  return classes[deriveDashboardStatus(booking)] || 'bg-slate-100 text-slate-800'
}

const getBookingStatusText = (booking) => {
  const texts = {
    waiting_payment: 'Menunggu Pembayaran',
    waiting_checkin: 'Menunggu Check-in',
    checked_in: 'Check-in',
    checked_out: 'Selesai',
    cancelled: 'Dibatalkan',
    rejected: 'Ditolak'
  }
  const status = deriveDashboardStatus(booking)
  return texts[status] || status
}

onMounted(async () => {
  const dashboardResponse = await $fetch('http://127.0.0.1:8000/api/admin/dashboard', {
    headers: {
      Authorization: `Bearer ${authStore.token}`,
    },
  })

  if (dashboardResponse.success) {
    stats.value.totalRooms = dashboardResponse.data.total_rooms || 0
    stats.value.availableRooms = dashboardResponse.data.available_rooms || 0
    stats.value.totalBookings = dashboardResponse.data.total_bookings || 0
    stats.value.totalRevenue = dashboardResponse.data.total_revenue || 0

    const chartRows = dashboardResponse.data.daily_revenue_7_days || []
    const today = new Date().toISOString().split('T')[0]

    revenueData.value = chartRows.map((row) => {
      const revenue = Number(row.total || 0)
      const rowDate = String(row.date)

      return {
        revenue,
        originalDate: rowDate,
        day: new Date(rowDate).toLocaleDateString('id-ID', { weekday: 'short' }),
        date: new Date(rowDate).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' }),
        isToday: rowDate === today
      }
    })

    isRevenueEmpty.value = revenueData.value.every((item) => Number(item.revenue) === 0)
  }

  const bookingsReport = await $fetch('http://127.0.0.1:8000/api/admin/reports/bookings', {
    headers: {
      Authorization: `Bearer ${authStore.token}`,
    },
  })

  if (bookingsReport.success) {
    const allBookings = bookingsReport.data?.bookings || []
    recentBookings.value = allBookings.slice(0, 5)
  }
})
</script>
