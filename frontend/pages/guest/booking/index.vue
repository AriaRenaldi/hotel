<template>
  <div class="min-h-screen bg-gradient-to-br from-[#f3f7fb] via-[#eef4fb] to-[#dbe6f3] py-10">
    <nav class="sticky top-0 z-50 mb-8 border-b border-white/70 bg-white/85 backdrop-blur-xl shadow-sm">
      <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-6">
        <NuxtLink to="/guest/dashboard" class="flex items-center gap-3">
          <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-r from-[#07111f] via-[#0f2742] to-[#1b456f] text-amber-300 shadow-lg">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
          </div>
          <div>
            <p class="text-lg font-bold text-slate-900">HotelKu</p>
            <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Guest Experience</p>
          </div>
        </NuxtLink>

        <div class="flex flex-wrap items-center gap-4 text-sm font-medium text-slate-600">
          <NuxtLink to="/guest/dashboard" class="transition hover:text-[#1b456f]">Dashboard</NuxtLink>
          <NuxtLink to="/guest/rooms" class="transition hover:text-[#1b456f]">Rooms</NuxtLink>
          <NuxtLink to="/guest/booking" class="text-[#1b456f] font-semibold">Booking</NuxtLink>
          <NuxtLink to="/guest/check-booking" class="transition hover:text-[#1b456f]">Check Booking</NuxtLink>
          <NuxtLink to="/profile" class="transition hover:text-[#1b456f]">Profil</NuxtLink>
          <button @click="handleLogout" class="rounded-xl bg-rose-50 px-4 py-2 text-sm font-semibold text-rose-600 transition hover:bg-rose-100">
            Logout
          </button>
        </div>
      </div>
    </nav>

    <div class="mx-auto max-w-7xl px-4 sm:px-6">
      <section class="relative overflow-hidden rounded-[2rem] bg-gradient-to-br from-[#07111f] via-[#0f2742] to-[#163b63] px-8 py-10 text-white shadow-2xl shadow-slate-300/40">
        <div class="absolute inset-0 opacity-20">
          <div class="absolute -top-8 right-0 h-56 w-56 rounded-full bg-blue-300/20 blur-3xl"></div>
          <div class="absolute bottom-0 left-10 h-40 w-40 rounded-full bg-amber-300/10 blur-3xl"></div>
        </div>
        <div class="relative flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
          <div>
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-blue-100">My Reservations</p>
            <h1 class="mt-3 text-4xl font-bold">Pemesanan Saya</h1>
            <p class="mt-3 max-w-2xl text-slate-200">Kelola seluruh reservasi Anda dengan tampilan yang lebih bersih, elegan, dan nyaman dibaca.</p>
          </div>
          <NuxtLink to="/guest/pesan" class="inline-flex rounded-2xl bg-white/10 px-5 py-3 text-sm font-semibold text-white backdrop-blur transition hover:bg-white/20">
            Pesan Kamar Baru
          </NuxtLink>
        </div>
      </section>

      <section class="mt-8 overflow-hidden rounded-[2rem] border border-white/70 bg-white/90 shadow-2xl shadow-slate-200/70 backdrop-blur">
        <div class="flex flex-col gap-4 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-blue-50 px-8 py-6 sm:flex-row sm:items-center sm:justify-between">
          <div>
            <p class="text-xs font-semibold uppercase tracking-[0.25em] text-[#1b456f]">Reservation Overview</p>
            <h2 class="mt-2 text-2xl font-bold text-slate-900">Daftar Pemesanan Anda</h2>
          </div>
          <div class="rounded-2xl bg-slate-100 px-4 py-2 text-sm font-medium text-slate-600">
            Total: {{ bookings.length }} booking
          </div>
        </div>

        <div v-if="loading" class="px-6 py-8">
          <PageSkeletonLoader message="Memuat daftar booking Anda..." :card-count="2" :row-count="6" />
        </div>

        <div v-else-if="bookings.length === 0" class="px-6 py-16 text-center">
          <p class="text-xl font-semibold text-slate-800">Belum ada pemesanan</p>
          <p class="mt-2 text-slate-500">Mulai pengalaman menginap Anda dengan kamar hotel bernuansa elegan.</p>
          <NuxtLink to="/guest/pesan" class="mt-5 inline-flex rounded-2xl bg-gradient-to-r from-[#07111f] via-[#0f2742] to-[#1b456f] px-6 py-3 text-sm font-semibold text-white shadow-lg transition hover:-translate-y-0.5">
            Pesan Sekarang
          </NuxtLink>
        </div>

        <div v-else class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-slate-50/80">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.25em] text-slate-500">No. Booking</th>
                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.25em] text-slate-500">Kamar</th>
                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.25em] text-slate-500">Check-in</th>
                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.25em] text-slate-500">Check-out</th>
                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.25em] text-slate-500">Total</th>
                <th class="min-w-[190px] px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.25em] text-slate-500">Status</th>
                <th class="min-w-[210px] px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.25em] text-slate-500">Pembayaran</th>
                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-[0.25em] text-slate-500">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr v-for="booking in paginatedBookings" :key="booking.id" class="transition hover:bg-slate-50/70">
                <td class="px-6 py-5 text-sm font-semibold text-slate-900">{{ booking.booking_number }}</td>
                <td class="px-6 py-5 text-sm text-slate-600">{{ booking.booking_details?.[0]?.room?.room_type?.type_name || '-' }}</td>
                <td class="px-6 py-5 text-sm text-slate-600">{{ formatDate(booking.check_in_date) }}</td>
                <td class="px-6 py-5 text-sm text-slate-600">{{ getDisplayCheckOutDate(booking) }}</td>
                <td class="px-6 py-5 text-sm font-semibold text-[#1b456f]">Rp {{ formatPrice(booking.total_amount) }}</td>
                <td class="px-6 py-5">
                  <span :class="getBookingStatusClass(booking)" class="inline-flex whitespace-nowrap rounded-full px-3.5 py-2 text-xs font-semibold leading-4">{{ getBookingStatusText(booking) }}</span>
                </td>
                <td class="px-6 py-5">
                  <span :class="getPaymentStatusClass(booking.payment_status)" class="inline-flex whitespace-nowrap rounded-full px-4 py-2 text-xs font-semibold leading-5">{{ getPaymentStatusText(booking.payment_status) }}</span>
                </td>
                <td class="px-6 py-5">
                  <div class="flex flex-wrap items-center gap-2">
                    <button type="button" @click.prevent="goToDetail(booking)" class="inline-flex h-9 items-center rounded-xl border border-blue-100 bg-blue-50 px-4 text-sm font-semibold text-[#1b456f] shadow-sm transition hover:-translate-y-0.5 hover:bg-blue-100">
                      Detail
                    </button>
                    <button type="button" v-if="booking.booking_status === 'confirmed' && ['pending','rejected'].includes(String(booking.payment_status))" @click.prevent="goToPayment(booking.id)" class="inline-flex h-9 items-center rounded-xl bg-gradient-to-r from-amber-400 to-amber-500 px-4 text-sm font-semibold text-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                      Bayar
                    </button>
                    <button type="button" v-if="booking.booking_status === 'confirmed' && booking.payment_status === 'pending'" @click.prevent="cancelBooking(booking.id)" class="inline-flex h-9 items-center rounded-xl border border-rose-200 bg-rose-50 px-4 text-sm font-semibold text-rose-600 shadow-sm transition hover:-translate-y-0.5 hover:bg-rose-100">
                      Batal
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <AppPagination
          v-if="!loading && totalPages > 1"
          :model-value="currentPage"
          :total-pages="totalPages"
          :from="displayFrom"
          :to="displayTo"
          :total="bookings.length"
          @update:model-value="setCurrentPage"
        />
      </section>
    </div>
  </div>
</template>

<script setup>
import { useAuthStore } from '~/stores/auth'
import { useBookingStore } from '~/stores/booking'

definePageMeta({ middleware: 'guest' })

const authStore = useAuthStore()
const bookingStore = useBookingStore()
const router = useRouter()
const bookings = ref([])
const loading = ref(true)
const currentPage = ref(1)
const itemsPerPage = 6

const formatPrice = (price) => new Intl.NumberFormat('id-ID').format(price)
const formatDate = (date) => new Date(date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
const getDisplayCheckOutDate = (booking) => {
  if (booking?.booking_status === 'checked_out' && booking?.actual_check_out_at) {
    return formatDate(booking.actual_check_out_at)
  }
  return formatDate(booking?.check_out_date)
}
const resolveDisplayBookingStatus = (booking) => {
  if (booking?.booking_status === 'cancelled') return 'cancelled'
  if (booking?.booking_status === 'confirmed' && booking?.payment_method === 'qris' && booking?.payment_status === 'rejected') {
    return 'payment_rejected'
  }
  if (booking?.booking_status === 'confirmed' && booking?.payment_method === 'qris' && booking?.payment_status === 'paid') {
    return 'pending_payment_approval'
  }
  if (booking?.booking_status === 'confirmed' && booking?.payment_method === 'qris' && booking?.payment_status === 'verified') {
    return 'awaiting_receptionist_checkin'
  }
  if (booking?.booking_status === 'confirmed' && booking?.payment_method === 'cash' && booking?.payment_status === 'verified') {
    return 'awaiting_receptionist_checkin'
  }
  if (booking?.payment_status === 'pending' || booking?.payment_status === 'paid') return 'pending_payment'
  return booking?.booking_status
}

const getBookingStatusClass = (booking) => ({
  payment_rejected: 'bg-rose-100 text-rose-700',
  pending_payment_approval: 'bg-amber-100 text-amber-700',
  pending_payment: 'bg-amber-100 text-amber-700',
  awaiting_receptionist_checkin: 'bg-blue-100 text-blue-700',
  confirmed: 'bg-blue-100 text-[#1b456f]',
  checked_in: 'bg-emerald-100 text-emerald-700',
  checked_out: 'bg-slate-100 text-slate-700',
  cancelled: 'bg-rose-100 text-rose-700'
}[resolveDisplayBookingStatus(booking)] || 'bg-gray-100 text-gray-800')

const getBookingStatusText = (booking) => ({
  payment_rejected: 'Ditolak',
  pending_payment_approval: 'Menunggu Persetujuan Pembayaran',
  pending_payment: 'Menunggu Pembayaran',
  awaiting_receptionist_checkin: 'Menunggu Persetujuan Check-in',
  confirmed: 'Dikonfirmasi',
  checked_in: 'Check-in',
  checked_out: 'Selesai',
  cancelled: 'Dibatalkan'
}[resolveDisplayBookingStatus(booking)] || resolveDisplayBookingStatus(booking))
const getPaymentStatusClass = (status) => ({ pending: 'bg-amber-100 text-amber-700', paid: 'bg-orange-100 text-orange-700', verified: 'bg-emerald-100 text-emerald-700', rejected: 'bg-rose-100 text-rose-700', cancelled: 'bg-rose-100 text-rose-700' }[status] || 'bg-gray-100 text-gray-800')
const getPaymentStatusText = (status) => ({ pending: 'Menunggu Pembayaran', paid: 'Menunggu Persetujuan', verified: 'Lunas', rejected: 'Ditolak', cancelled: 'Dibatalkan' }[status] || status)
const totalPages = computed(() => Math.max(1, Math.ceil(bookings.value.length / itemsPerPage)))
const paginatedBookings = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  return bookings.value.slice(start, start + itemsPerPage)
})
const displayFrom = computed(() => (bookings.value.length ? ((currentPage.value - 1) * itemsPerPage) + 1 : 0))
const displayTo = computed(() => Math.min(currentPage.value * itemsPerPage, bookings.value.length))
const setCurrentPage = (page) => { currentPage.value = page }
watch(totalPages, (value) => {
  if (currentPage.value > value) currentPage.value = value
})

const getBookingIdentifier = (booking) => {
  return String(booking?.booking_number || booking?.id || '')
}

const forceNavigate = async (path) => {
  try {
    await router.push(path)
  } catch {
    if (process.client) {
      window.location.href = path
    }
  }
}

const goToDetail = async (booking) => {
  const identifier = getBookingIdentifier(booking)
  if (!identifier) {
    alert('ID booking tidak valid')
    return
  }
  await forceNavigate(`/guest/booking/${encodeURIComponent(identifier)}`)
}

const goToPrint = async (booking) => {
  const identifier = getBookingIdentifier(booking)
  if (!identifier) {
    alert('ID booking tidak valid')
    return
  }
  await forceNavigate(`/guest/booking/print/${encodeURIComponent(identifier)}`)
}

const goToPayment = (id) => navigateTo(`/guest/payment/${id}`)
const cancelBooking = async (id) => {
  if (confirm('Apakah Anda yakin ingin membatalkan pemesanan ini?')) {
    const result = await bookingStore.cancelBooking(String(id), authStore.token)
    if (result.success) {
      const refreshed = await bookingStore.getMyBookings(authStore.token)
      if (refreshed.success) {
        bookings.value = refreshed.data
      }
      return
    }
    alert(result.message || 'Gagal membatalkan booking')
  }
}
const handleLogout = async () => {
  await authStore.logout()
  await navigateTo('/login')
}

onMounted(async () => {
  if (!authStore.token) {
    authStore.initAuth()
  }

  if (!authStore.token) {
    await navigateTo('/login')
    return
  }

  const result = await bookingStore.getMyBookings(authStore.token)
  if (result.success) {
    bookings.value = result.data
    currentPage.value = 1
  } else if ((result.message || '').toLowerCase().includes('unauthorized')) {
    await authStore.logout()
    await navigateTo('/login')
  }
  loading.value = false
})
</script>
