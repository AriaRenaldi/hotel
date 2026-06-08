<template>
  <div class="min-h-screen bg-gradient-to-br from-[#f3f7fb] via-[#eef4fb] to-[#dbe6f3]">
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
            <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Room Collection</p>
          </div>
        </NuxtLink>

        <div class="flex flex-wrap items-center gap-4 text-sm font-medium text-slate-600">
          <NuxtLink to="/guest/dashboard" class="transition hover:text-[#1b456f]">Dashboard</NuxtLink>
          <NuxtLink to="/guest/rooms" class="text-[#1b456f] font-semibold">Rooms</NuxtLink>
          <NuxtLink to="/guest/booking" class="transition hover:text-[#1b456f]">Booking</NuxtLink>
          <NuxtLink to="/guest/check-booking" class="transition hover:text-[#1b456f]">Check Booking</NuxtLink>
          <NuxtLink to="/profile" class="transition hover:text-[#1b456f]">Profil</NuxtLink>
          <button @click="handleLogout" class="rounded-xl bg-rose-50 px-4 py-2 text-sm font-semibold text-rose-600 transition hover:bg-rose-100">Logout</button>
        </div>
      </div>
    </nav>

    <section class="relative overflow-hidden bg-gradient-to-br from-[#07111f] via-[#0f2742] to-[#163b63] text-white">
      <div class="absolute inset-0 opacity-20">
        <div class="absolute left-6 top-8 h-48 w-48 rounded-full bg-white/10 blur-3xl"></div>
        <div class="absolute right-16 bottom-0 h-64 w-64 rounded-full bg-blue-300/20 blur-3xl"></div>
      </div>
      <div class="relative mx-auto max-w-7xl px-4 py-16 sm:px-6">
        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-blue-100">Luxury Rooms</p>
        <h1 class="mt-4 text-4xl font-bold">Pilih Kamar Anda</h1>
        <p class="mt-4 max-w-2xl text-lg text-slate-200">Temukan kamar dengan nuansa eksklusif, detail elegan, dan kenyamanan premium untuk pengalaman menginap yang lebih istimewa.</p>
      </div>
    </section>

    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6">
      <div class="mb-8 rounded-[2rem] border border-white/70 bg-white/90 p-6 shadow-2xl shadow-slate-200/70 backdrop-blur">
        <div class="mb-6 flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
          <div>
            <p class="text-xs font-semibold uppercase tracking-[0.25em] text-[#1b456f]">Room Finder</p>
            <h2 class="mt-2 text-2xl font-bold text-slate-900">Sesuaikan pencarian Anda</h2>
          </div>
          <div class="rounded-2xl bg-slate-100 px-4 py-2 text-sm text-slate-600">Kurasi kamar terbaik HotelKu</div>
        </div>

        <div class="grid gap-4 md:grid-cols-4">
          <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">Check-in</label>
            <input v-model="filters.checkIn" type="date" :min="today" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 focus:outline-none focus:ring-2 focus:ring-[#1b456f]">
          </div>
          <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">Check-out</label>
            <input v-model="filters.checkOut" type="date" :min="filters.checkIn || today" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 focus:outline-none focus:ring-2 focus:ring-[#1b456f]">
          </div>
          <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">Jumlah Kamar</label>
            <input v-model.number="filters.totalRooms" type="number" min="1" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 focus:outline-none focus:ring-2 focus:ring-[#1b456f]">
          </div>
          <div class="flex items-end">
            <button @click="applyFilters" class="w-full rounded-2xl bg-gradient-to-r from-[#07111f] via-[#0f2742] to-[#1b456f] py-3.5 text-sm font-semibold text-white shadow-lg transition hover:-translate-y-0.5">
              Cari Kamar
            </button>
          </div>
        </div>
      </div>

      <div v-if="errorMessage" class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm font-medium text-rose-700">
        {{ errorMessage }}
      </div>

      <div v-if="hotelFacilities.length" class="mb-8 rounded-[2rem] border border-white/70 bg-white/90 p-6 shadow-2xl shadow-slate-200/70 backdrop-blur">
        <div class="mb-5 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
          <div>
            <p class="text-xs font-semibold uppercase tracking-[0.25em] text-[#1b456f]">Hotel Facilities</p>
            <h2 class="mt-2 text-2xl font-bold text-slate-900">Fasilitas Tersedia di Hotel</h2>
          </div>
          <p class="text-sm font-medium text-slate-500">{{ hotelFacilities.length }} fasilitas aktif</p>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
          <div
            v-for="facility in hotelFacilities"
            :key="facility.id"
            class="rounded-2xl border border-slate-100 bg-slate-50 p-4"
          >
            <p class="font-semibold text-slate-900">{{ facility.facility_name }}</p>
            <p class="mt-1 text-sm text-slate-500">{{ facility.description || 'Fasilitas siap digunakan tamu selama menginap.' }}</p>
          </div>
        </div>
      </div>

      <div v-if="loading" class="py-8">
        <PageSkeletonLoader message="Memuat tipe kamar, fasilitas, dan ketersediaan..." :card-count="3" :row-count="6" />
      </div>

      <div v-else class="grid gap-8 md:grid-cols-2 xl:grid-cols-3">
        <div v-for="room in paginatedRoomTypes" :key="room.id" class="overflow-hidden rounded-[2rem] border border-white/70 bg-white/90 shadow-2xl shadow-slate-200/60 backdrop-blur transition duration-300 hover:-translate-y-1 hover:shadow-2xl">
          <div class="relative">
            <img :src="room.image_url || 'https://via.placeholder.com/400x300'" :alt="room.type_name" class="h-56 w-full object-cover">
            <div class="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-slate-950/70 to-transparent"></div>
            <div class="absolute bottom-5 left-5 rounded-full bg-white/15 px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-white backdrop-blur">
              Max {{ room.capacity }} orang
            </div>
          </div>
          <div class="p-6">
            <h3 class="text-2xl font-bold text-slate-900">{{ room.type_name }}</h3>
            <p class="mt-3 text-sm leading-relaxed text-slate-500">{{ truncateText(room.description, 100) }}</p>
            <div class="mt-5">
              <span class="text-3xl font-bold text-[#1b456f]">Rp {{ formatPrice(room.price_per_night) }}</span>
              <span class="text-sm text-slate-400"> / malam</span>
            </div>
            <div class="mt-5 rounded-2xl bg-slate-50 p-4">
              <p class="text-sm text-slate-600"><span class="font-semibold text-slate-800">Fasilitas:</span> {{ room.facilities }}</p>
            </div>
            <div class="mt-4">
              <p class="text-sm font-semibold text-slate-700">
                Kamar tersedia: <span class="text-[#1b456f]">{{ getAvailabilityCount(room.id) }}</span>
              </p>
              <p
                v-if="isFullyBooked(room.id)"
                class="mt-1 text-xs font-semibold text-rose-600"
              >
                Kamar sudah terisi full
              </p>
            </div>
            <button
              @click="bookNow(room.id)"
              :disabled="!canBookRoomType(room.id)"
              class="mt-6 w-full rounded-2xl py-3.5 text-sm font-semibold text-white shadow-lg transition"
              :class="canBookRoomType(room.id)
                ? 'bg-gradient-to-r from-[#07111f] via-[#0f2742] to-[#1b456f] hover:-translate-y-0.5'
                : 'cursor-not-allowed bg-slate-300 text-slate-100 shadow-none'"
            >
              {{ canBookRoomType(room.id) ? 'Pesan Sekarang' : 'Kamar Penuh' }}
            </button>
          </div>
        </div>
      </div>
      <AppPagination
        v-if="!loading && totalPages > 1"
        class="mt-8"
        :model-value="currentPage"
        :total-pages="totalPages"
        :from="displayFrom"
        :to="displayTo"
        :total="roomTypes.length"
        @update:model-value="setCurrentPage"
      />
    </div>
  </div>
</template>

<script setup>
import { useAuthStore } from '~/stores/auth'
import { useBookingStore } from '~/stores/booking'
import { useRoomStore } from '~/stores/room'

definePageMeta({ middleware: 'guest' })

const authStore = useAuthStore()
const bookingStore = useBookingStore()
const roomStore = useRoomStore()
const route = useRoute()

const roomTypes = ref([])
const loading = ref(true)
const currentPage = ref(1)
const itemsPerPage = 6
const today = ref(new Date().toISOString().split('T')[0])
const tomorrow = ref(new Date(Date.now() + 24 * 60 * 60 * 1000).toISOString().split('T')[0])
const errorMessage = ref('')
const hotelFacilities = ref([])
const filters = reactive({ checkIn: '', checkOut: '', totalRooms: 1 })
const availabilityByType = ref({})
const totalPages = computed(() => Math.max(1, Math.ceil(roomTypes.value.length / itemsPerPage)))
const paginatedRoomTypes = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  return roomTypes.value.slice(start, start + itemsPerPage)
})
const displayFrom = computed(() => (roomTypes.value.length ? ((currentPage.value - 1) * itemsPerPage) + 1 : 0))
const displayTo = computed(() => Math.min(currentPage.value * itemsPerPage, roomTypes.value.length))
const setCurrentPage = (page) => { currentPage.value = page }
watch(totalPages, (value) => {
  if (currentPage.value > value) currentPage.value = value
})

const formatPrice = (price) => new Intl.NumberFormat('id-ID').format(price)
const truncateText = (text, length) => !text ? '' : text.length > length ? `${text.substring(0, length)}...` : text

const syncFiltersFromQuery = () => {
  filters.checkIn = typeof route.query.check_in === 'string' ? route.query.check_in : today.value
  filters.checkOut = typeof route.query.check_out === 'string' ? route.query.check_out : tomorrow.value
  filters.totalRooms = Math.max(1, Number(route.query.total_rooms || 1))
}

const applyFilters = async () => {
  errorMessage.value = ''
  await navigateTo({
    path: '/guest/rooms',
    query: {
      ...(filters.checkIn ? { check_in: filters.checkIn } : {}),
      ...(filters.checkOut ? { check_out: filters.checkOut } : {}),
      total_rooms: String(filters.totalRooms || 1),
    },
  })
  await loadRooms()
}

const bookNow = (roomId) => {
  if (!canBookRoomType(roomId)) {
    errorMessage.value = 'Kamar pada tipe ini sudah terisi full untuk tanggal yang dipilih.'
    return
  }

  if (filters.checkIn && filters.checkOut) {
    navigateTo(`/guest/pesan?room_type_id=${roomId}&check_in=${filters.checkIn}&check_out=${filters.checkOut}&total_rooms=${filters.totalRooms}`)
  } else {
    navigateTo(`/guest/pesan?room_type_id=${roomId}`)
  }
}

const getAvailabilityCount = (roomTypeId) => Number(availabilityByType.value[roomTypeId] || 0)
const canBookRoomType = (roomTypeId) => getAvailabilityCount(roomTypeId) >= Math.max(1, Number(filters.totalRooms || 1))
const isFullyBooked = (roomTypeId) => getAvailabilityCount(roomTypeId) === 0

const loadRooms = async () => {
  loading.value = true
  errorMessage.value = ''

  const result = await roomStore.getRoomTypes()
  if (!result.success) {
    errorMessage.value = result.message || 'Gagal memuat data kamar'
    roomTypes.value = []
    loading.value = false
    return
  }

  let data = result.data || []
  availabilityByType.value = {}

  if (filters.checkIn && filters.checkOut) {
    const availabilityResults = await Promise.all(
      data.map(async (roomType) => {
        const availability = await roomStore.checkAvailability(filters.checkIn, filters.checkOut, roomType.id, 1)
        const availableFromPayload = Number(availability.data?.available_rooms)
        const availableRooms = Number.isFinite(availableFromPayload) ? availableFromPayload : 0
        return [roomType.id, availableRooms]
      })
    )

    availabilityByType.value = Object.fromEntries(availabilityResults)
    data = data.filter((roomType) => canBookRoomType(roomType.id))

    if (data.length === 0) {
      errorMessage.value = `Semua tipe kamar sedang penuh untuk ${filters.totalRooms} kamar pada tanggal yang dipilih.`
    }
  }

  roomTypes.value = data
  currentPage.value = 1
  loading.value = false
}

const handleLogout = async () => {
  await authStore.logout()
  await navigateTo('/login')
}

onMounted(async () => {
  syncFiltersFromQuery()
  const facilitiesResult = await bookingStore.getFacilities()
  if (facilitiesResult.success && Array.isArray(facilitiesResult.data)) {
    hotelFacilities.value = facilitiesResult.data
  }
  await loadRooms()
})
</script>
