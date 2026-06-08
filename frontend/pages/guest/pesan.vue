<template>
  <div class="relative min-h-screen overflow-hidden bg-gradient-to-br from-[#05142b] via-[#0c2d54] to-[#d9e6f5]">
    <div class="pointer-events-none absolute inset-0">
      <div class="absolute inset-y-0 left-0 w-28 bg-gradient-to-r from-white/85 via-white/20 to-transparent"></div>
      <div class="absolute inset-y-0 right-0 w-28 bg-gradient-to-l from-white/85 via-white/20 to-transparent"></div>
      <div class="absolute -top-24 left-1/3 h-72 w-72 rounded-full bg-[#2f5f95]/35 blur-3xl"></div>
      <div class="absolute bottom-0 right-16 h-72 w-72 rounded-full bg-[#89abd1]/25 blur-3xl"></div>
    </div>

    <div class="relative mx-auto max-w-6xl px-4 py-10 sm:px-6">
      <section class="rounded-[2rem] border border-slate-200 bg-white p-6 text-slate-900 shadow-2xl shadow-slate-300/60 md:p-8">
        <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
          <div>
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-[#0f2f57]">Guest Booking Suite</p>
            <h1 class="mt-3 text-3xl font-bold sm:text-4xl">Form Pemesanan</h1>
            <p class="mt-3 max-w-2xl text-sm text-slate-600 sm:text-base">
              Pilih tipe kamar dan atur tanggal menginap dengan tampilan navy elegan untuk pengalaman booking yang lebih nyaman.
            </p>
          </div>
          <NuxtLink to="/guest/rooms" class="inline-flex items-center justify-center rounded-2xl bg-gradient-to-r from-[#061a33] via-[#0f2f57] to-[#1d4a7a] px-5 py-3 text-sm font-semibold text-white transition hover:brightness-110">
            Kembali ke Rooms
          </NuxtLink>
        </div>
      </section>

      <div v-if="loadingRoom" class="mt-8">
        <PageSkeletonLoader message="Memuat data kamar dan harga pemesanan..." :card-count="2" :row-count="5" />
      </div>

      <div v-else class="mt-8 grid gap-6 lg:grid-cols-[1.15fr_0.85fr]">
        <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-2xl shadow-slate-300/60 md:p-8">
          <div class="mb-6 flex items-center justify-between">
            <div>
              <p class="text-xs font-semibold uppercase tracking-[0.25em] text-[#0f2f57]">Reservation Form</p>
              <h2 class="mt-2 text-2xl font-bold text-slate-900">Pilih Tipe Kamar</h2>
            </div>
          </div>

          <div class="mt-4">
            <label class="mb-2 block text-sm font-semibold text-slate-700">Tipe Kamar</label>
            <select v-model.number="selectedRoomTypeId" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#0f2f57]">
              <option :value="0">Pilih tipe kamar</option>
              <option v-for="item in roomTypeOptions" :key="item.id" :value="item.id">
                {{ item.type_name }}
              </option>
            </select>
            <p class="mt-2 text-xs text-slate-500">Dalam satu booking, Anda hanya bisa memilih satu tipe kamar.</p>
          </div>

          <div v-if="roomType" class="mt-5 rounded-2xl border border-blue-100 bg-gradient-to-r from-slate-50 to-blue-50 p-5">
            <h3 class="text-xl font-bold text-slate-900">{{ roomType.type_name }}</h3>
            <p class="mt-2 text-sm text-slate-600">{{ roomType.description }}</p>
            <p class="mt-4 text-lg font-semibold text-[#0f2f57]">Rp {{ formatPrice(roomType.price_per_night) }} / malam</p>
          </div>

          <form class="mt-6 space-y-4" @submit.prevent="submitBooking">
            <div class="grid gap-4 sm:grid-cols-2">
              <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">Check-in</label>
                <input v-model="form.checkIn" type="date" :min="today" required class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#0f2f57]">
              </div>
              <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700">Check-out</label>
                <input v-model="form.checkOut" type="date" :min="form.checkIn || today" required class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#0f2f57]">
              </div>
            </div>

            <div>
              <label class="mb-2 block text-sm font-semibold text-slate-700">Jumlah Kamar</label>
              <input v-model.number="form.totalRooms" type="number" min="1" required class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#0f2f57]">
            </div>

            <div>
              <label class="mb-2 block text-sm font-semibold text-slate-700">Metode Pembayaran</label>
              <select v-model="form.paymentMethod" :disabled="requiresTransfer" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 disabled:cursor-not-allowed disabled:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-[#0f2f57]">
                <option value="qris">QRIS</option>
                <option value="cash">Tunai (Bayar di Hotel)</option>
              </select>
              <p v-if="requiresTransfer" class="mt-2 rounded-xl border border-amber-200 bg-amber-50 px-3 py-2 text-xs font-medium text-amber-700">
                Total pemesanan di atas Rp 500.000 wajib pembayaran QRIS dan upload bukti.
              </p>
              <p v-else class="mt-2 text-xs text-slate-500">
                Karena total pemesanan Rp 500.000 ke bawah, Anda boleh memilih QRIS atau bayar tunai di hotel.
              </p>
              <p class="mt-2 rounded-xl border border-blue-100 bg-blue-50 px-3 py-2 text-xs font-medium text-[#1b456f]">
                {{ checkInPolicyText }}
              </p>
            </div>

            <div v-if="selectedPaymentMethod === 'qris'">
              <label class="mb-2 block text-sm font-semibold text-slate-700">Jam Check-in Pilihan (QRIS)</label>
              <input
                v-model="form.requestedCheckinTime"
                type="time"
                required
                class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#0f2f57]"
              >
              <p class="mt-2 text-xs text-slate-500">
                Resepsionis hanya dapat menyetujui check-in pada jam ini atau setelahnya.
              </p>
            </div>

            <p v-if="errorMessage" class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700">
              {{ errorMessage }}
            </p>

            <button :disabled="creating" type="submit" class="w-full rounded-2xl bg-gradient-to-r from-[#061a33] via-[#0f2f57] to-[#1d4a7a] py-3.5 font-semibold text-white shadow-lg shadow-slate-300 transition hover:-translate-y-0.5 disabled:cursor-not-allowed disabled:opacity-60">
              {{ creating ? 'Memproses...' : submitButtonText }}
            </button>
          </form>
        </section>

        <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-2xl shadow-slate-300/60 md:p-8">
          <p class="text-xs font-semibold uppercase tracking-[0.25em] text-[#0f2f57]">Summary</p>
          <h3 class="mt-2 text-2xl font-bold text-slate-900">Ringkasan</h3>

          <div class="mt-6 space-y-4">
            <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3">
              <span class="text-sm text-slate-500">Lama menginap</span>
              <span class="text-sm font-semibold text-slate-900">{{ nights }} malam</span>
            </div>
            <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3">
              <span class="text-sm text-slate-500">Subtotal</span>
              <span class="text-sm font-semibold text-slate-900">Rp {{ formatPrice(subtotal) }}</span>
            </div>
            <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3">
              <span class="text-sm text-slate-500">Pajak 10%</span>
              <span class="text-sm font-semibold text-slate-900">Rp {{ formatPrice(tax) }}</span>
            </div>
            <div class="rounded-2xl border border-blue-100 bg-blue-50 px-4 py-4">
              <div class="flex items-center justify-between">
                <span class="text-sm font-semibold text-slate-700">Total</span>
                <span class="text-xl font-bold text-[#0f2f57]">Rp {{ formatPrice(totalAmount) }}</span>
              </div>
            </div>
          </div>
        </section>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useAuthStore } from '~/stores/auth'
import { useBookingStore } from '~/stores/booking'
import { useRoomStore } from '~/stores/room'

definePageMeta({ middleware: 'guest' })

const route = useRoute()
const authStore = useAuthStore()
const bookingStore = useBookingStore()
const roomStore = useRoomStore()

const roomType = ref(null)
const roomTypeOptions = ref([])
const loadingRoom = ref(true)
const creating = ref(false)
const errorMessage = ref('')
const today = new Date().toISOString().split('T')[0]

const selectedRoomTypeId = ref(Number(route.query.room_type_id || 0))

const form = reactive({
  checkIn: typeof route.query.check_in === 'string' ? route.query.check_in : '',
  checkOut: typeof route.query.check_out === 'string' ? route.query.check_out : '',
  totalRooms: Number(route.query.total_rooms || 1),
  paymentMethod: 'qris',
  requestedCheckinTime: '14:00',
})

const normalizeAmount = (value) => {
  const parsed = Number(value)
  if (!Number.isFinite(parsed)) return 0
  return Math.max(0, Math.round(parsed * 100) / 100)
}

const nights = computed(() => {
  if (!form.checkIn || !form.checkOut) return 0
  const start = new Date(form.checkIn)
  const end = new Date(form.checkOut)
  const diff = Math.ceil((end.getTime() - start.getTime()) / (1000 * 60 * 60 * 24))
  return diff > 0 ? diff : 0
})

const subtotal = computed(() => normalizeAmount((roomType.value?.price_per_night || 0) * nights.value * (form.totalRooms || 1)))
const tax = computed(() => normalizeAmount(subtotal.value * 0.1))
const totalAmount = computed(() => normalizeAmount(subtotal.value + tax.value))
const requiresTransfer = computed(() => totalAmount.value > 500000)
const selectedPaymentMethod = computed(() => (requiresTransfer.value ? 'qris' : form.paymentMethod))
const submitButtonText = computed(() => selectedPaymentMethod.value === 'cash' ? 'Buat Booking Tunai' : 'Lanjut ke Pembayaran QRIS')
const checkInPolicyText = computed(() => selectedPaymentMethod.value === 'cash'
  ? 'Booking tunai tidak perlu upload bukti pembayaran. Saat check-in, Anda wajib upload struk booking dan menunggu persetujuan resepsionis.'
  : 'Booking QRIS harus upload bukti pembayaran. Check-in disetujui resepsionis mengikuti jam check-in yang Anda pilih (boleh lebih lambat, tidak boleh lebih cepat).')

watch(
  () => requiresTransfer.value,
  (value) => {
    if (value) {
      form.paymentMethod = 'qris'
    }
  },
  { immediate: true }
)

const formatPrice = (price) => new Intl.NumberFormat('id-ID', {
  minimumFractionDigits: 0,
  maximumFractionDigits: 2,
}).format(normalizeAmount(price))

const submitBooking = async () => {
  errorMessage.value = ''

  if (!authStore.token) {
    authStore.initAuth()
  }

  if (!authStore.token) {
    errorMessage.value = 'Sesi login Anda tidak ditemukan. Silakan login kembali.'
    await navigateTo('/login')
    return
  }

  if (!form.checkIn || !form.checkOut) {
    errorMessage.value = 'Tanggal check-in dan check-out wajib diisi.'
    return
  }

  if (form.totalRooms < 1) {
    errorMessage.value = 'Jumlah kamar minimal 1.'
    return
  }

  if (nights.value < 1) {
    errorMessage.value = 'Tanggal check-out harus setelah check-in.'
    return
  }

  creating.value = true

  try {
    if (!selectedRoomTypeId.value) {
      errorMessage.value = 'Silakan pilih tipe kamar terlebih dahulu.'
      return
    }

    const availability = await roomStore.checkAvailability(
      form.checkIn,
      form.checkOut,
      selectedRoomTypeId.value,
      form.totalRooms
    )

    if (!availability.success) {
      errorMessage.value = availability.message || 'Kamar tidak tersedia untuk tanggal tersebut.'
      return
    }

    const paymentMethod = selectedPaymentMethod.value

    const result = await bookingStore.createBooking({
      room_type_id: selectedRoomTypeId.value,
      check_in: form.checkIn,
      check_out: form.checkOut,
      total_rooms: form.totalRooms,
      payment_method: paymentMethod,
      requested_checkin_time: paymentMethod === 'qris' ? form.requestedCheckinTime : null,
    }, authStore.token)

    if (!result.success || !result.data?.booking_id) {
      errorMessage.value = result.message || 'Gagal membuat pemesanan.'
      if ((result.message || '').toLowerCase().includes('unauthorized')) {
        await navigateTo('/login')
      }
      return
    }

    const nextPath = result.data?.next_step?.path
    if (typeof nextPath === 'string' && nextPath.startsWith('/')) {
      await navigateTo(nextPath)
      return
    }

    if (paymentMethod === 'cash') {
      const bookingIdentifier = encodeURIComponent(String(result.data.booking_number || result.data.booking_id))
      await navigateTo(`/guest/booking/${bookingIdentifier}`)
      return
    }

    await navigateTo(`/guest/payment/${result.data.booking_id}`)
  } finally {
    creating.value = false
  }
}

const loadRoomTypeDetail = async () => {
  if (!selectedRoomTypeId.value) {
    roomType.value = null
    return
  }

  const result = await roomStore.getRoomTypeDetail(selectedRoomTypeId.value)
  roomType.value = result.success && result.data ? result.data : null
}

watch(selectedRoomTypeId, async () => {
  await loadRoomTypeDetail()
})

onMounted(async () => {
  const roomTypesResult = await roomStore.getRoomTypes()
  if (roomTypesResult.success) {
    roomTypeOptions.value = roomTypesResult.data || []
  }

  await loadRoomTypeDetail()
  loadingRoom.value = false
})
</script>
