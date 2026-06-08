<template>
  <div class="min-h-screen bg-gradient-to-br from-[#f3f7fb] via-[#eef4fb] to-[#dbe6f3] text-slate-900">
    <nav class="sticky top-0 z-50 border-b border-white/70 bg-white/85 backdrop-blur-xl shadow-sm">
      <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-6">
        <NuxtLink to="/receptionist/dashboard" class="flex items-center gap-3">
          <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-r from-[#4b5563] via-[#6b7280] to-[#9ca3af] text-white shadow-lg">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
          </div>
          <div>
            <p class="text-lg font-bold text-slate-900">HotelKu</p>
            <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Reception Desk</p>
          </div>
        </NuxtLink>

        <div class="flex flex-wrap items-center gap-2 rounded-full border border-slate-200/80 bg-white/80 p-2 text-sm font-medium text-slate-600 shadow-lg shadow-slate-200/40 backdrop-blur">
          <NuxtLink to="/receptionist/dashboard" class="rounded-full bg-slate-800 px-4 py-2 font-semibold text-white shadow-md">
            Dashboard
          </NuxtLink>
          <NuxtLink to="/receptionist/bookings" class="rounded-full px-4 py-2 transition hover:bg-slate-100 hover:text-slate-900">
            Bookings
          </NuxtLink>
          <NuxtLink to="/receptionist/reports" class="rounded-full px-4 py-2 transition hover:bg-slate-100 hover:text-slate-900">
            Reports
          </NuxtLink>
          <NuxtLink to="/receptionist/penalty-verifications" class="rounded-full px-4 py-2 transition hover:bg-slate-100 hover:text-slate-900">
            Verifikasi Denda
          </NuxtLink>
          <NuxtLink to="/receptionist/penalty-reports" class="rounded-full px-4 py-2 transition hover:bg-slate-100 hover:text-slate-900">
            Laporan Denda
          </NuxtLink>
          <NuxtLink to="/profile" class="rounded-full px-4 py-2 transition hover:bg-slate-100 hover:text-slate-900">
            Profil
          </NuxtLink>
          <button @click="handleLogout" class="rounded-xl bg-rose-50 px-4 py-2 text-sm font-semibold text-rose-600 transition hover:bg-rose-100">
            Logout
          </button>
        </div>
      </div>
    </nav>

    <section class="relative overflow-hidden bg-gradient-to-br from-[#4b5563] via-[#6b7280] to-[#9ca3af] text-white">
      <div class="absolute inset-0 opacity-20">
        <div class="absolute left-8 top-8 h-48 w-48 rounded-full bg-white/15 blur-3xl"></div>
        <div class="absolute right-12 bottom-0 h-64 w-64 rounded-full bg-slate-200/25 blur-3xl"></div>
      </div>
      <div class="relative mx-auto max-w-7xl px-4 py-16 sm:px-6">
        <div class="grid items-center gap-8 lg:grid-cols-[1.2fr_0.8fr]">
          <div>
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-200">Reception Overview</p>
            <h1 class="mt-4 text-4xl font-bold">Dashboard Receptionist</h1>
            <p class="mt-4 max-w-2xl text-lg text-slate-100">
              Pantau seluruh aktivitas tamu, pemesanan, dan operasional front desk dalam tampilan silver yang lebih elegan, rapi, dan profesional.
            </p>
          </div>

          <div class="grid gap-4 sm:grid-cols-2">
            <div class="rounded-3xl border border-white/15 bg-white/10 px-5 py-4 backdrop-blur shadow-lg shadow-slate-900/10">
              <p class="text-xs uppercase tracking-[0.2em] text-slate-200">Welcome</p>
              <div class="mt-2 flex items-center gap-3">
                <div class="h-10 w-10 overflow-hidden rounded-xl border border-white/30 bg-white/20">
                  <img v-if="authStore.user?.photo_url" :src="authStore.user.photo_url" alt="Foto profil receptionist" class="h-full w-full object-cover">
                  <div v-else class="flex h-full w-full items-center justify-center text-sm font-bold text-white">
                    {{ (authStore.user?.full_name || 'R').charAt(0).toUpperCase() }}
                  </div>
                </div>
                <p class="text-lg font-semibold">{{ authStore.user?.full_name }}</p>
              </div>
            </div>
            <div class="rounded-3xl border border-white/15 bg-white/10 px-5 py-4 backdrop-blur shadow-lg shadow-slate-900/10">
              <p class="text-xs uppercase tracking-[0.2em] text-slate-200">Service</p>
              <p class="mt-2 text-lg font-semibold">Fast &amp; precise</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="relative z-10 mx-auto -mt-8 max-w-7xl px-4 sm:px-6">
      <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-[1.75rem] border border-white/70 bg-white/90 p-6 shadow-xl shadow-slate-200/60 backdrop-blur transition duration-300 hover:-translate-y-1 hover:shadow-2xl">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-slate-500">Check-in Hari Ini</p>
              <p class="mt-2 text-3xl font-bold text-emerald-600">{{ stats.checkInsToday }}</p>
            </div>
          </div>
        </div>

        <div class="rounded-[1.75rem] border border-white/70 bg-white/90 p-6 shadow-xl shadow-slate-200/60 backdrop-blur transition duration-300 hover:-translate-y-1 hover:shadow-2xl">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-slate-500">Check-out Hari Ini</p>
              <p class="mt-2 text-3xl font-bold text-amber-600">{{ stats.checkOutsToday }}</p>
            </div>
          </div>
        </div>

        <div class="rounded-[1.75rem] border border-white/70 bg-white/90 p-6 shadow-xl shadow-slate-200/60 backdrop-blur transition duration-300 hover:-translate-y-1 hover:shadow-2xl">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-slate-500">Menunggu Pembayaran</p>
              <p class="mt-2 text-3xl font-bold text-orange-600">{{ stats.pendingPayments }}</p>
            </div>
          </div>
        </div>

        <div class="rounded-[1.75rem] border border-white/70 bg-white/90 p-6 shadow-xl shadow-slate-200/60 backdrop-blur transition duration-300 hover:-translate-y-1 hover:shadow-2xl">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-slate-500">Kamar Tersedia</p>
              <p class="mt-2 text-3xl font-bold text-slate-700">{{ stats.availableRooms }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6">
      <div class="overflow-hidden rounded-[2rem] border border-white/70 bg-white/92 shadow-2xl shadow-slate-200/60 backdrop-blur">
        <div class="border-b border-slate-100 bg-gradient-to-r from-slate-50 to-zinc-100 px-8 py-6">
          <p class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Today Activity</p>
          <h2 class="mt-2 text-2xl font-bold text-slate-900">Pemesanan Hari Ini</h2>
        </div>

        <div v-if="loading" class="py-16 text-center">
          <div class="inline-block h-10 w-10 animate-spin rounded-full border-4 border-slate-200 border-t-slate-500"></div>
          <p class="mt-4 text-slate-400">Memuat data...</p>
        </div>

        <div v-else>
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead class="bg-slate-50/80">
                <tr>
                  <th class="px-8 py-4 text-left text-xs font-bold uppercase tracking-[0.25em] text-slate-500">No. Booking</th>
                  <th class="px-8 py-4 text-left text-xs font-bold uppercase tracking-[0.25em] text-slate-500">Tamu</th>
                  <th class="px-8 py-4 text-left text-xs font-bold uppercase tracking-[0.25em] text-slate-500">Kamar</th>
                  <th class="px-8 py-4 text-left text-xs font-bold uppercase tracking-[0.25em] text-slate-500">Check-in</th>
                  <th class="px-8 py-4 text-left text-xs font-bold uppercase tracking-[0.25em] text-slate-500">Total</th>
                  <th class="px-8 py-4 text-left text-xs font-bold uppercase tracking-[0.25em] text-slate-500">Status</th>
                  <th class="px-8 py-4 text-left text-xs font-bold uppercase tracking-[0.25em] text-slate-500">Aksi</th>
                </tr>
              </thead>

              <tbody class="divide-y divide-slate-100">
                <tr v-for="b in paginatedTodayBookings" :key="b.id" class="transition hover:bg-slate-50/70">
                  <td class="px-8 py-4 text-sm font-semibold text-slate-900">{{ b.booking_number }}</td>
                  <td class="px-8 py-4">
                    <div class="text-sm font-semibold text-slate-900">{{ b.guest?.full_name }}</div>
                    <div class="text-xs text-slate-500">{{ b.guest?.email }}</div>
                  </td>
                  <td class="px-8 py-4">
                    <div class="flex items-center gap-3">
                      <div class="h-14 w-14 overflow-hidden rounded-xl border border-slate-200 bg-slate-100">
                        <img :src="b.booking_details?.[0]?.room?.image_url || b.booking_details?.[0]?.room?.room_type?.image_url || 'https://via.placeholder.com/140x140?text=No+Photo'" :alt="`Booking ${b.booking_number}`" class="h-full w-full object-cover">
                      </div>
                      <span class="text-sm text-slate-600">{{ b.booking_details?.[0]?.room?.room_number || '-' }}</span>
                    </div>
                  </td>
                  <td class="px-8 py-4 text-sm text-slate-600">{{ formatDate(b.check_in_date) }}</td>
                  <td class="px-8 py-4 text-sm font-semibold text-slate-700">Rp {{ formatPrice(b.total_amount) }}</td>
                  <td class="px-8 py-4">
                    <span :class="getStatusClass(b)" class="rounded-full px-3 py-1.5 text-xs font-semibold">
                      {{ getStatusText(b) }}
                    </span>
                  </td>
                  <td class="px-8 py-4">
                    <NuxtLink to="/receptionist/bookings" class="rounded-xl bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-200">
                      Kelola
                    </NuxtLink>
                  </td>
                </tr>

                <tr v-if="todayBookings.length === 0">
                  <td colspan="7" class="px-8 py-12 text-center text-slate-400">Tidak ada pemesanan hari ini</td>
                </tr>
              </tbody>
            </table>
          </div>

          <AppPagination
            v-if="todayBookings.length > 0 && totalTodayPages > 1"
            :model-value="todayCurrentPage"
            :total-pages="totalTodayPages"
            :from="todayDisplayFrom"
            :to="todayDisplayTo"
            :total="todayBookings.length"
            @update:model-value="setTodayCurrentPage"
          />
        </div>
      </div>
    </section>
  </div>
</template>


<script setup>
import { useAuthStore } from '~/stores/auth'
import { useReceptionistStore } from '~/stores/receptionist'

definePageMeta({ middleware: 'receptionist' })
const authStore = useAuthStore()
const receptionistStore = useReceptionistStore()
const todayBookings = ref([])
const loading = ref(true)
const stats = ref({ checkInsToday: 0, checkOutsToday: 0, pendingPayments: 0, availableRooms: 0 })
const todayCurrentPage = ref(1)
const todayItemsPerPage = 6
const formatPrice = (p) => new Intl.NumberFormat('id-ID').format(p)
const formatDate = (d) => d ? new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : '-'
const toTimestamp = (value) => {
  const date = new Date(value || '')
  const time = date.getTime()
  return Number.isNaN(time) ? 0 : time
}

const sortNewestFirst = (items, primaryDateField) => {
  return [...items].sort((a, b) => {
    const primaryDiff = toTimestamp(b?.[primaryDateField]) - toTimestamp(a?.[primaryDateField])
    if (primaryDiff !== 0) return primaryDiff

    const createdDiff = toTimestamp(b?.created_at) - toTimestamp(a?.created_at)
    if (createdDiff !== 0) return createdDiff

    return Number(b?.id || 0) - Number(a?.id || 0)
  })
}

const totalTodayPages = computed(() => Math.max(1, Math.ceil(todayBookings.value.length / todayItemsPerPage)))
const paginatedTodayBookings = computed(() => {
  const start = (todayCurrentPage.value - 1) * todayItemsPerPage
  return todayBookings.value.slice(start, start + todayItemsPerPage)
})
const todayDisplayFrom = computed(() => (todayBookings.value.length ? ((todayCurrentPage.value - 1) * todayItemsPerPage) + 1 : 0))
const todayDisplayTo = computed(() => Math.min(todayCurrentPage.value * todayItemsPerPage, todayBookings.value.length))
const setTodayCurrentPage = (page) => { todayCurrentPage.value = page }
watch(totalTodayPages, (value) => {
  if (todayCurrentPage.value > value) todayCurrentPage.value = value
})
watch(todayBookings, () => {
  todayCurrentPage.value = 1
})

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

const getStatusClass = (booking) => ({
  waiting_payment: 'bg-amber-100 text-amber-700',
  waiting_checkin: 'bg-blue-100 text-[#1b456f]',
  checked_in: 'bg-emerald-100 text-emerald-700',
  checked_out: 'bg-slate-100 text-slate-700',
  cancelled: 'bg-rose-100 text-rose-700',
  rejected: 'bg-rose-100 text-rose-700',
}[deriveDashboardStatus(booking)] || 'bg-gray-100 text-gray-800')

const getStatusText = (booking) => ({
  waiting_payment: 'Menunggu Pembayaran',
  waiting_checkin: 'Menunggu Check-in',
  checked_in: 'Check-in',
  checked_out: 'Selesai',
  cancelled: 'Dibatalkan',
  rejected: 'Ditolak',
}[deriveDashboardStatus(booking)] || deriveDashboardStatus(booking))
const handleLogout = async () => {
  await authStore.logout()
  await navigateTo('/login')
}

onMounted(async () => {
  loading.value = true

  try {
    const [statsResult, todayBookingsResult] = await Promise.all([
      receptionistStore.getStats(authStore.token),
      receptionistStore.getTodayBookings(authStore.token),
    ])

    if (statsResult.success && statsResult.data) {
      stats.value = {
        checkInsToday: statsResult.data.today?.checkins ?? 0,
        checkOutsToday: statsResult.data.today?.checkouts ?? 0,
        pendingPayments: statsResult.data.pending_payments ?? 0,
        availableRooms: statsResult.data.available_rooms ?? 0,
      }
    }

    if (todayBookingsResult.success) {
      const bookings = todayBookingsResult.data?.all ?? todayBookingsResult.data ?? []
      todayBookings.value = Array.isArray(bookings)
        ? sortNewestFirst(bookings, 'check_in_date')
        : []
    }
  } finally {
    loading.value = false
  }
})
</script>
