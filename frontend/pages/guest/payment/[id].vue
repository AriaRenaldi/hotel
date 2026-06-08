<template>
  <div class="relative min-h-screen overflow-hidden bg-gradient-to-br from-[#05142b] via-[#0c2d54] to-[#d9e6f5]">
    <div class="pointer-events-none absolute inset-0">
      <div class="absolute inset-y-0 left-0 w-28 bg-gradient-to-r from-white/85 via-white/20 to-transparent"></div>
      <div class="absolute inset-y-0 right-0 w-28 bg-gradient-to-l from-white/85 via-white/20 to-transparent"></div>
      <div class="absolute -top-24 left-1/3 h-72 w-72 rounded-full bg-[#2f5f95]/35 blur-3xl"></div>
      <div class="absolute bottom-0 right-16 h-72 w-72 rounded-full bg-[#89abd1]/25 blur-3xl"></div>
    </div>

    <div class="relative mx-auto max-w-5xl px-4 py-10 sm:px-6">
      <section class="rounded-[2rem] border border-slate-200 bg-white p-6 text-slate-900 shadow-2xl shadow-slate-300/60 md:p-8">
        <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
          <div>
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-[#0f2f57]">Payment Center</p>
            <h1 class="mt-3 text-3xl font-bold sm:text-4xl">Pembayaran Booking</h1>
            <p class="mt-3 max-w-2xl text-sm text-slate-600 sm:text-base">
              Selesaikan pembayaran reservasi dengan tampilan navy yang lebih elegan dan alur yang lebih jelas.
            </p>
          </div>
          <NuxtLink to="/guest/booking" class="inline-flex items-center justify-center rounded-2xl bg-gradient-to-r from-[#061a33] via-[#0f2f57] to-[#1d4a7a] px-5 py-3 text-sm font-semibold text-white transition hover:brightness-110">
            Lihat Semua Booking
          </NuxtLink>
        </div>
      </section>

      <div v-if="loading" class="mt-8">
        <PageSkeletonLoader message="Memuat data pembayaran booking Anda..." :card-count="2" :row-count="5" />
      </div>

      <div v-else-if="booking" class="mt-8 space-y-6">
        <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-2xl shadow-slate-300/60 md:p-8">
          <div class="flex flex-col gap-5 md:flex-row md:items-start md:justify-between">
            <div>
              <p class="text-xs font-semibold uppercase tracking-[0.25em] text-[#0f2f57]">Booking Summary</p>
              <h2 class="mt-2 text-2xl font-bold text-slate-900">{{ booking.booking_number }}</h2>
            </div>
            <div class="grid gap-2 sm:grid-cols-2">
              <span :class="getPaymentStatusClass(booking.payment_status)" class="rounded-full px-3 py-1.5 text-xs font-semibold text-center">
                {{ getPaymentStatusText(booking.payment_status) }}
              </span>
              <span class="rounded-full bg-slate-100 px-3 py-1.5 text-center text-xs font-semibold text-slate-700">
                {{ getBookingStatusText(booking.booking_status) }}
              </span>
            </div>
          </div>

          <div class="mt-6 grid gap-4 sm:grid-cols-2">
            <div class="rounded-2xl bg-slate-50 px-4 py-3">
              <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Total Tagihan</p>
              <p class="mt-1 text-xl font-bold text-[#0f2f57]">Rp {{ formatPrice(booking.total_amount) }}</p>
            </div>
            <div class="rounded-2xl bg-slate-50 px-4 py-3">
              <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Metode Pembayaran</p>
              <p class="mt-1 text-sm font-semibold text-slate-900">{{ getPaymentMethodText(booking.payment_method) }}</p>
            </div>
          </div>
        </section>

        <section v-if="booking.payment_method === 'qris' && ['pending', 'rejected'].includes(String(booking.payment_status))" class="rounded-3xl border border-slate-200 bg-white p-6 shadow-2xl shadow-slate-300/60 md:p-8">
          <p class="text-xs font-semibold uppercase tracking-[0.25em] text-[#0f2f57]">QRIS Payment</p>
          <h3 class="mt-2 text-2xl font-bold text-slate-900">Pembayaran QRIS</h3>
          <p class="mt-3 text-sm text-slate-600">
            Scan QRIS berikut, lalu upload bukti pembayaran. Nominal pembayaran tidak boleh kurang dari total tagihan.
          </p>
          <p v-if="booking.payment_status === 'rejected'" class="mt-3 rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-700">
            Pembayaran sebelumnya ditolak resepsionis. Silakan upload ulang bukti pembayaran yang benar.
          </p>

          <div class="mt-5 rounded-3xl border border-slate-200 bg-gradient-to-br from-slate-50 to-blue-50 p-5">
            <img src="/images/qris-hotel.jpeg" alt="QRIS Hotel" class="mx-auto w-full max-w-[220px] rounded-2xl border border-slate-200 bg-white object-contain shadow-md">
          </div>

          <div class="mt-6 grid gap-4">
            <div>
              <label class="mb-2 block text-sm font-semibold text-slate-700">Nominal yang Dibayar</label>
              <input
                v-model.number="paidAmount"
                type="number"
                min="0"
                step="0.01"
                class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#0f2f57]"
                placeholder="Masukkan nominal pembayaran"
              >
              <p class="mt-2 text-xs text-slate-500">Minimal Rp {{ formatPrice(booking.total_amount) }}. Boleh lebih besar, tidak boleh lebih kecil.</p>
            </div>

            <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50/80 p-4">
              <input id="proof-file" type="file" accept="image/*" class="hidden" @change="onSelectProof">
              <label for="proof-file" class="flex cursor-pointer items-center justify-between gap-4 rounded-xl bg-white px-4 py-3 text-sm font-medium text-slate-700 shadow-sm transition hover:bg-slate-100">
                <span class="truncate">{{ selectedProofName || 'Pilih bukti pembayaran' }}</span>
                <span class="rounded-lg bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">Browse</span>
              </label>
            </div>

            <button :disabled="uploading || !selectedProof" @click="uploadProof" class="rounded-2xl bg-gradient-to-r from-[#061a33] via-[#0f2f57] to-[#1d4a7a] px-5 py-3.5 text-sm font-semibold text-white shadow-lg shadow-slate-300 transition hover:-translate-y-0.5 disabled:cursor-not-allowed disabled:opacity-60">
              {{ uploading ? 'Mengupload...' : 'Upload Bukti Pembayaran' }}
            </button>
          </div>
        </section>

        <section v-if="booking.payment_method === 'cash'" class="rounded-3xl border border-slate-200 bg-white p-6 shadow-2xl shadow-slate-300/60 md:p-8">
          <p class="text-xs font-semibold uppercase tracking-[0.25em] text-[#0f2f57]">Cash Payment</p>
          <h3 class="mt-2 text-2xl font-bold text-slate-900">Pembayaran Tunai</h3>
          <p class="mt-3 text-sm leading-relaxed text-slate-600">
            Booking Anda menggunakan metode tunai. Tidak perlu upload bukti pembayaran, namun saat check-in Anda wajib upload struk booking dan menunggu persetujuan resepsionis.
          </p>
        </section>

        <section v-if="!['qris', 'cash'].includes(booking.payment_method)" class="rounded-3xl border border-slate-200 bg-white p-6 shadow-2xl shadow-slate-300/60 md:p-8">
          <h3 class="text-xl font-bold text-slate-900">Metode Pembayaran Tidak Didukung</h3>
          <p class="mt-2 text-sm text-slate-600">Silakan hubungi admin untuk konfirmasi metode pembayaran.</p>
        </section>

        <p v-if="message" class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
          {{ message }}
        </p>
        <p v-if="errorMessage" class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700">
          {{ errorMessage }}
        </p>

        <div class="flex flex-wrap gap-3">
          <NuxtLink :to="`/guest/booking/${booking.id}`" class="rounded-2xl bg-slate-100 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-200">
            Lihat Detail Booking
          </NuxtLink>
          <NuxtLink to="/guest/check-booking" class="rounded-2xl bg-slate-100 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-200">
            Cek Pesanan
          </NuxtLink>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useAuthStore } from '~/stores/auth'
import { useBookingStore } from '~/stores/booking'

definePageMeta({ middleware: 'guest' })

const route = useRoute()
const authStore = useAuthStore()
const bookingStore = useBookingStore()
const booking = ref(null)
const selectedProof = ref(null)
const paidAmount = ref(0)
const loading = ref(true)
const uploading = ref(false)
const errorMessage = ref('')
const message = ref('')

const bookingId = computed(() => String(route.params.id || ''))

const normalizeAmount = (value) => {
  const parsed = Number(value)
  if (!Number.isFinite(parsed)) return 0
  return Math.max(0, Math.round(parsed * 100) / 100)
}

const formatPrice = (price) => new Intl.NumberFormat('id-ID', {
  minimumFractionDigits: 0,
  maximumFractionDigits: 2,
}).format(normalizeAmount(price))

const selectedProofName = computed(() => selectedProof.value?.name || '')

const getPaymentStatusText = (status) => ({
  pending: 'Menunggu Pembayaran',
  paid: 'Pembayaran Diproses',
  verified: 'Lunas',
  cancelled: 'Dibatalkan',
}[status] || status)

const getPaymentStatusClass = (status) => ({
  pending: 'bg-amber-100 text-amber-700',
  paid: 'bg-orange-100 text-orange-700',
  verified: 'bg-emerald-100 text-emerald-700',
  cancelled: 'bg-rose-100 text-rose-700',
}[status] || 'bg-slate-100 text-slate-700')

const getBookingStatusText = (status) => ({
  confirmed: 'Dikonfirmasi',
  checked_in: 'Check-in',
  checked_out: 'Selesai',
  cancelled: 'Dibatalkan',
}[status] || status)

const getPaymentMethodText = (method) => ({
  qris: 'QRIS',
  cash: 'Tunai',
}[method] || method)

const loadBooking = async () => {
  loading.value = true
  const result = await bookingStore.getBookingDetail(bookingId.value, authStore.token)
  if (!result.success || !result.data) {
    await navigateTo('/guest/booking')
    return
  }
  booking.value = result.data
  paidAmount.value = normalizeAmount(result.data?.total_amount || 0)
  loading.value = false
}

const onSelectProof = (event) => {
  const target = event.target
  const file = target?.files?.[0]
  selectedProof.value = file || null
}

const uploadProof = async () => {
  if (!selectedProof.value || !booking.value) return
  if (Number.isNaN(paidAmount.value) || paidAmount.value < 0) {
    errorMessage.value = 'Nominal pembayaran minimal 0.'
    return
  }

  if (normalizeAmount(paidAmount.value) < normalizeAmount(booking.value.total_amount || 0)) {
    errorMessage.value = `Nominal pembayaran tidak boleh kurang dari total tagihan (Rp ${formatPrice(booking.value.total_amount)}).`
    return
  }

  uploading.value = true
  errorMessage.value = ''
  message.value = ''

  const result = await bookingStore.uploadPaymentProof(
    String(booking.value.id),
    selectedProof.value,
    normalizeAmount(paidAmount.value),
    authStore.token
  )

  if (result.success) {
    message.value = result.message || 'Bukti pembayaran berhasil diupload.'
    selectedProof.value = null
    await loadBooking()
  } else {
    errorMessage.value = result.message || 'Upload bukti pembayaran gagal.'
  }

  uploading.value = false
}

onMounted(loadBooking)
</script>
