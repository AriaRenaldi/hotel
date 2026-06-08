<template>
  <div class="min-h-screen bg-gradient-to-br from-[#f3f7fb] via-[#eef4fb] to-[#dbe6f3] text-slate-800">
    <nav class="sticky top-0 z-50 border-b border-white/70 bg-white/85 backdrop-blur-xl shadow-sm">
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
          <NuxtLink to="/guest/dashboard" class="text-[#1b456f] font-semibold">Dashboard</NuxtLink>
          <NuxtLink to="/guest/rooms" class="transition hover:text-[#1b456f]">Rooms</NuxtLink>
          <NuxtLink to="/guest/booking" class="transition hover:text-[#1b456f]">Booking</NuxtLink>
          <NuxtLink to="/guest/check-booking" class="transition hover:text-[#1b456f]">Check Booking</NuxtLink>
          <NuxtLink to="/profile" class="transition hover:text-[#1b456f]">Profil</NuxtLink>
          <button @click="handleLogout" class="rounded-xl bg-rose-50 px-4 py-2 text-sm font-semibold text-rose-600 transition hover:bg-rose-100">
            Logout
          </button>
        </div>
      </div>
    </nav>

    <section class="relative overflow-hidden bg-gradient-to-br from-[#07111f] via-[#0f2742] to-[#163b63]">
      <div class="absolute inset-0 opacity-20">
        <div class="absolute -top-10 left-10 h-56 w-56 rounded-full bg-white/10 blur-3xl"></div>
        <div class="absolute right-0 top-24 h-72 w-72 rounded-full bg-blue-300/20 blur-3xl"></div>
        <div class="absolute bottom-0 left-1/3 h-44 w-44 rounded-full bg-amber-300/10 blur-3xl"></div>
      </div>

      <div class="relative mx-auto max-w-7xl px-6 py-20 lg:py-24">
        <div class="grid items-center gap-10 lg:grid-cols-[1.3fr_0.9fr]">
          <div class="text-white">
            <div class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-[0.28em] text-blue-100 backdrop-blur">
              <span class="h-2 w-2 animate-pulse rounded-full bg-emerald-400"></span>
              Luxury Guest Lounge
            </div>
            <h1 class="mt-6 text-4xl font-bold leading-tight sm:text-5xl lg:text-6xl">
              Selamat Datang, {{ user?.full_name }}!
            </h1>
            <p class="mt-5 max-w-2xl text-lg leading-relaxed text-slate-200">
              Nikmati suasana hotel yang berkelas dengan sistem reservasi modern, aman, dan dirancang untuk pengalaman tamu yang lebih elegan.
            </p>

            <div class="mt-8 grid gap-4 sm:grid-cols-3">
              <div class="rounded-3xl border border-white/10 bg-white/10 px-5 py-4 backdrop-blur">
                <p class="text-xs uppercase tracking-[0.2em] text-slate-300">Experience</p>
                <div class="mt-2 flex items-center gap-3">
                  <div class="h-10 w-10 overflow-hidden rounded-xl border border-white/20 bg-white/20">
                    <img v-if="authStore.user?.photo_url" :src="authStore.user.photo_url" alt="Foto profil guest" class="h-full w-full object-cover">
                    <div v-else class="flex h-full w-full items-center justify-center text-sm font-bold text-white">
                      {{ (authStore.user?.full_name || 'G').charAt(0).toUpperCase() }}
                    </div>
                  </div>
                  <p class="text-lg font-semibold">Stay premium</p>
                </div>
              </div>
              <div class="rounded-3xl border border-white/10 bg-white/10 px-5 py-4 backdrop-blur">
                <p class="text-xs uppercase tracking-[0.2em] text-slate-300">Privacy</p>
                <p class="mt-2 text-lg font-semibold">Data terlindungi</p>
              </div>
              <div class="rounded-3xl border border-white/10 bg-white/10 px-5 py-4 backdrop-blur">
                <p class="text-xs uppercase tracking-[0.2em] text-slate-300">Service</p>
                <p class="mt-2 text-lg font-semibold">Reservasi cepat</p>
              </div>
            </div>
          </div>

          <div class="rounded-[2rem] border border-white/10 bg-white/95 p-8 shadow-2xl shadow-slate-900/20 backdrop-blur">
            <div class="mb-6 flex items-center justify-between">
              <div>
                <p class="text-xs font-semibold uppercase tracking-[0.25em] text-[#1b456f]">Quick Reservation</p>
                <h2 class="mt-2 text-2xl font-bold text-slate-900">Pesan kamar sekarang</h2>
              </div>
              <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-[#0f2742] to-[#1b456f] text-white shadow-lg shadow-blue-200/40">
                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
              </div>
            </div>

            <form @submit.prevent="searchRooms" class="grid gap-4">
              <div class="grid gap-4 sm:grid-cols-2">
                <div>
                  <label class="mb-2 block text-sm font-semibold text-slate-700">Tanggal Check-in</label>
                  <input v-model="search.checkIn" type="date" :min="today" required class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 transition focus:border-transparent focus:outline-none focus:ring-2 focus:ring-[#1b456f]">
                </div>
                <div>
                  <label class="mb-2 block text-sm font-semibold text-slate-700">Tanggal Check-out</label>
                  <input v-model="search.checkOut" type="date" :min="search.checkIn || today" required class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 transition focus:border-transparent focus:outline-none focus:ring-2 focus:ring-[#1b456f]">
                </div>
              </div>
              <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">Jumlah Kamar</label>
                <input v-model.number="search.totalRooms" type="number" min="1" max="10" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 transition focus:border-transparent focus:outline-none focus:ring-2 focus:ring-[#1b456f]">
              </div>
              <button type="submit" class="mt-2 rounded-2xl bg-gradient-to-r from-[#07111f] via-[#0f2742] to-[#1b456f] px-6 py-4 text-sm font-semibold text-white shadow-lg shadow-slate-300 transition duration-300 hover:-translate-y-0.5 hover:shadow-xl">
                Cek Ketersediaan
              </button>
            </form>
          </div>
        </div>
      </div>
    </section>

    <section class="relative z-10 mx-auto -mt-10 max-w-7xl px-6">
      <div class="grid gap-6 md:grid-cols-3">
        <div class="rounded-[1.75rem] border border-white/70 bg-white/90 p-6 shadow-xl shadow-slate-200/60 backdrop-blur transition duration-300 hover:-translate-y-1 hover:shadow-2xl">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-slate-500">Total Pemesanan</p>
              <p class="mt-2 text-3xl font-bold text-slate-900">{{ stats.totalBookings }}</p>
            </div>
          </div>
        </div>
        <div class="rounded-[1.75rem] border border-white/70 bg-white/90 p-6 shadow-xl shadow-slate-200/60 backdrop-blur transition duration-300 hover:-translate-y-1 hover:shadow-2xl">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-slate-500">Pemesanan Mendatang</p>
              <p class="mt-2 text-3xl font-bold text-[#1b456f]">{{ stats.upcomingBookings }}</p>
            </div>
          </div>
        </div>
        <div class="rounded-[1.75rem] border border-white/70 bg-white/90 p-6 shadow-xl shadow-slate-200/60 backdrop-blur transition duration-300 hover:-translate-y-1 hover:shadow-2xl">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-slate-500">Pemesanan Selesai</p>
              <p class="mt-2 text-3xl font-bold text-emerald-600">{{ stats.completedBookings }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="mx-auto max-w-7xl px-6 py-14">
      <div class="overflow-hidden rounded-[2rem] border border-white/70 bg-white/90 shadow-2xl shadow-slate-200/70 backdrop-blur">
        <div class="flex flex-col gap-4 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-blue-50 px-8 py-6 sm:flex-row sm:items-center sm:justify-between">
          <div>
            <p class="text-xs font-semibold uppercase tracking-[0.25em] text-[#1b456f]">Recent Stay</p>
            <h2 class="mt-2 text-2xl font-bold text-slate-900">Pemesanan Terbaru Anda</h2>
          </div>
          <NuxtLink to="/guest/booking" class="inline-flex items-center justify-center rounded-2xl bg-[#0f2742] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#163b63]">
            Lihat Semua
          </NuxtLink>
        </div>

        <div v-if="loading" class="px-8 py-8">
          <PageSkeletonLoader message="Memuat ringkasan pemesanan terbaru Anda..." :card-count="2" :row-count="4" />
        </div>

        <div v-else-if="recentBookings.length === 0" class="px-6 py-16 text-center">
          <p class="text-lg font-semibold text-slate-700">Belum ada pemesanan</p>
          <p class="mt-2 text-slate-500">Temukan kamar terbaik dengan nuansa elegan khas HotelKu.</p>
          <NuxtLink to="/guest/rooms" class="mt-5 inline-flex rounded-2xl bg-gradient-to-r from-[#07111f] via-[#0f2742] to-[#1b456f] px-6 py-3 text-sm font-semibold text-white shadow-lg transition hover:-translate-y-0.5">
            Pesan Sekarang
          </NuxtLink>
        </div>

        <div v-else class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-slate-50/80 text-slate-500">
              <tr>
                <th class="px-8 py-4 text-left text-xs font-bold uppercase tracking-[0.25em]">No. Booking</th>
                <th class="px-8 py-4 text-left text-xs font-bold uppercase tracking-[0.25em]">Check-in</th>
                <th class="px-8 py-4 text-left text-xs font-bold uppercase tracking-[0.25em]">Check-out</th>
                <th class="px-8 py-4 text-left text-xs font-bold uppercase tracking-[0.25em]">Total</th>
                <th class="px-8 py-4 text-left text-xs font-bold uppercase tracking-[0.25em]">Status</th>
                <th class="px-8 py-4 text-left text-xs font-bold uppercase tracking-[0.25em]">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr v-for="booking in recentBookings" :key="booking.id" class="transition hover:bg-slate-50/70">
                <td class="px-8 py-5 text-sm font-semibold text-slate-900">{{ booking.booking_number }}</td>
                <td class="px-8 py-5 text-sm text-slate-600">{{ formatDate(booking.check_in_date) }}</td>
                <td class="px-8 py-5 text-sm text-slate-600">{{ getDisplayCheckOutDate(booking) }}</td>
                <td class="px-8 py-5 text-sm font-semibold text-[#1b456f]">Rp {{ formatPrice(booking.total_amount) }}</td>
                <td class="px-8 py-5">
                  <span :class="getStatusClass(booking.booking_status)" class="rounded-full px-3 py-1.5 text-xs font-semibold">
                    {{ getStatusText(booking.booking_status) }}
                  </span>
                </td>
                <td class="px-8 py-5">
                  <button @click="viewBooking(booking.id)" class="rounded-xl bg-slate-100 px-4 py-2 text-sm font-semibold text-[#1b456f] transition hover:bg-blue-50">
                    Detail
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </section>

    <section class="bg-gradient-to-b from-transparent to-slate-100/70 py-8">
      <div class="mx-auto max-w-7xl px-6">
        <div class="mb-12 text-center">
          <p class="text-xs font-semibold uppercase tracking-[0.3em] text-[#1b456f]">Signature Amenities</p>
          <h2 class="mt-3 text-4xl font-bold text-slate-900">Fasilitas Hotel</h2>
          <p class="mt-3 text-slate-500">Detail yang dirancang untuk membuat setiap pengalaman menginap terasa lebih berkelas.</p>
        </div>

        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
          <div v-for="facility in facilities" :key="facility.id" class="rounded-[1.75rem] border border-white/70 bg-white/90 p-8 text-center shadow-xl shadow-slate-200/60 transition duration-300 hover:-translate-y-1 hover:shadow-2xl">
            <div class="mx-auto mb-4 h-24 w-24 overflow-hidden rounded-2xl border border-slate-200 bg-slate-100">
              <img :src="facility.image_url || 'https://via.placeholder.com/200x200?text=No+Photo'" :alt="facility.facility_name" class="h-full w-full object-cover">
            </div>
            <h3 class="text-lg font-bold text-slate-900">{{ facility.facility_name }}</h3>
            <p class="mt-3 text-sm leading-relaxed text-slate-500">{{ facility.description }}</p>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { useAuthStore } from '~/stores/auth'
import { useBookingStore } from '~/stores/booking'

definePageMeta({ middleware: 'guest' })

const authStore = useAuthStore()
const bookingStore = useBookingStore()
const user = computed(() => authStore.user)
const loading = ref(false)
const recentBookings = ref([])
const facilities = ref([])
const stats = ref({ totalBookings: 0, upcomingBookings: 0, completedBookings: 0 })
const search = reactive({ checkIn: '', checkOut: '', totalRooms: 1 })
const today = ref(new Date().toISOString().split('T')[0])

const formatPrice = (price) => new Intl.NumberFormat('id-ID').format(price)
const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
}
const getDisplayCheckOutDate = (booking) => {
  if (booking?.booking_status === 'checked_out' && booking?.actual_check_out_at) {
    return formatDate(booking.actual_check_out_at)
  }
  return formatDate(booking?.check_out_date)
}
const getStatusClass = (status) => ({
  confirmed: 'bg-blue-100 text-[#1b456f]',
  checked_in: 'bg-emerald-100 text-emerald-700',
  checked_out: 'bg-slate-100 text-slate-700',
  cancelled: 'bg-rose-100 text-rose-700'
}[status] || 'bg-gray-100 text-gray-800')
const getStatusText = (status) => ({
  confirmed: 'Dikonfirmasi',
  checked_in: 'Check-in',
  checked_out: 'Selesai',
  cancelled: 'Dibatalkan'
}[status] || status)

const searchRooms = () => {
  if (!search.checkIn || !search.checkOut) {
    alert('Silakan pilih tanggal check-in dan check-out')
    return
  }

  navigateTo(`/guest/rooms?check_in=${search.checkIn}&check_out=${search.checkOut}&total_rooms=${search.totalRooms}`)
}

const viewBooking = (id) => navigateTo(`/guest/booking/${id}`)

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

  loading.value = true
  try {
    const bookingsResult = await bookingStore.getMyBookings(authStore.token)
    if (bookingsResult.success) {
      recentBookings.value = bookingsResult.data.slice(0, 5)
      stats.value.totalBookings = bookingsResult.data.length
      stats.value.upcomingBookings = bookingsResult.data.filter((booking) => booking.booking_status === 'confirmed' && new Date(booking.check_in_date) > new Date()).length
      stats.value.completedBookings = bookingsResult.data.filter((booking) => booking.booking_status === 'checked_out').length
    } else if ((bookingsResult.message || '').toLowerCase().includes('unauthorized')) {
      await authStore.logout(false)
      await navigateTo('/login')
      return
    }

    const facilitiesResult = await bookingStore.getFacilities()
    if (facilitiesResult.success) {
      facilities.value = facilitiesResult.data
    }
  } catch (_error) {
    // Suppress noisy console error and keep UI fallback behavior.
  } finally {
    loading.value = false
  }
})
</script>
