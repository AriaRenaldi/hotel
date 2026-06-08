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
            <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Booking Detail</p>
          </div>
        </NuxtLink>

        <div class="flex flex-wrap items-center gap-4 text-sm font-medium text-slate-600">
          <NuxtLink to="/guest/dashboard" class="transition hover:text-[#1b456f]">Dashboard</NuxtLink>
          <NuxtLink to="/guest/rooms" class="transition hover:text-[#1b456f]">Rooms</NuxtLink>
          <NuxtLink to="/guest/booking" class="text-[#1b456f] font-semibold">Booking</NuxtLink>
          <NuxtLink to="/guest/check-booking" class="transition hover:text-[#1b456f]">Check Booking</NuxtLink>
          <NuxtLink to="/profile" class="transition hover:text-[#1b456f]">Profil</NuxtLink>
          <button @click="handleLogout" class="rounded-xl bg-rose-50 px-4 py-2 text-sm font-semibold text-rose-600 transition hover:bg-rose-100">Logout</button>
        </div>
      </div>
    </nav>

    <div class="mx-auto max-w-7xl px-4 sm:px-6">
      <section class="relative overflow-hidden rounded-[2rem] bg-gradient-to-br from-[#07111f] via-[#0f2742] to-[#163b63] px-8 py-10 text-white shadow-2xl shadow-slate-300/40">
        <div class="absolute inset-0 opacity-20">
          <div class="absolute right-10 top-0 h-56 w-56 rounded-full bg-blue-300/20 blur-3xl"></div>
          <div class="absolute bottom-0 left-10 h-40 w-40 rounded-full bg-amber-300/10 blur-3xl"></div>
        </div>
        <div class="relative flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
          <div>
            <NuxtLink to="/guest/dashboard" class="inline-flex items-center text-sm text-blue-100 transition hover:text-white">Kembali ke Dashboard</NuxtLink>
            <p class="mt-4 text-xs font-semibold uppercase tracking-[0.25em] text-blue-100">Reservation Detail</p>
            <h1 class="mt-3 text-4xl font-bold">Detail Pemesanan</h1>
            <p class="mt-3 text-slate-200">Nomor Booking: {{ booking?.booking_number }}</p>
          </div>
          <div class="grid gap-3 text-left sm:grid-cols-2">
            <div class="rounded-2xl border border-white/10 bg-white/10 px-5 py-4 backdrop-blur">
              <p class="text-xs uppercase tracking-[0.2em] text-slate-300">Status</p>
              <p class="mt-2 text-base font-semibold">{{ booking ? getBookingStatusText(booking) : 'Memuat' }}</p>
            </div>
            <div class="rounded-2xl border border-white/10 bg-white/10 px-5 py-4 backdrop-blur">
              <p class="text-xs uppercase tracking-[0.2em] text-slate-300">Payment</p>
              <p class="mt-2 text-base font-semibold">{{ booking ? getPaymentStatusText(booking.payment_status) : 'Memuat' }}</p>
            </div>
          </div>
        </div>
      </section>

      <div v-if="loading" class="mt-8">
        <PageSkeletonLoader message="Memuat detail pemesanan dan status terbaru..." :card-count="2" :row-count="7" />
      </div>

      <div v-else-if="booking" class="mt-8 grid items-start gap-8 xl:grid-cols-[1.25fr_0.85fr]">
        <div class="space-y-8">
          <div class="rounded-[2rem] border border-white/70 bg-white/90 p-8 shadow-2xl shadow-slate-200/70 backdrop-blur">
            <div class="mb-6 flex items-start justify-between gap-4">
              <div>
                <p class="text-xs font-semibold uppercase tracking-[0.25em] text-[#1b456f]">Stay Summary</p>
                <h2 class="mt-2 text-2xl font-bold text-slate-900">Detail Kamar & Menginap</h2>
              </div>
              <span :class="getStatusClass(booking.booking_status)" class="rounded-full px-3 py-1.5 text-xs font-semibold">{{ getBookingStatusText(booking) }}</span>
            </div>

            <div class="mb-6 rounded-[1.5rem] bg-gradient-to-r from-slate-50 to-blue-50 p-5">
              <div class="flex flex-col items-start gap-5 sm:flex-row">
                <img :src="booking.room_type?.image_url || 'https://via.placeholder.com/100x100'" class="h-28 w-full rounded-2xl object-cover shadow-md sm:w-28">
                <div>
                  <h3 class="text-xl font-bold text-slate-900">{{ booking.room_type?.type_name }}</h3>
                  <p class="mt-2 text-sm leading-relaxed text-slate-500">{{ booking.room_type?.description }}</p>
                  <p class="mt-3 text-sm text-slate-600"><span class="font-semibold text-slate-800">Fasilitas:</span> {{ booking.room_type?.facilities }}</p>
                </div>
              </div>
            </div>

            <div class="mb-6 grid gap-4 md:grid-cols-2">
              <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                <p class="text-sm text-slate-500">Check-in</p>
                <p class="mt-2 font-semibold text-slate-900">{{ formatDate(booking.check_in_date) }}</p>
                <p class="mt-1 text-sm text-slate-400">{{ checkInTimeLabel }}</p>
              </div>
              <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                <p class="text-sm text-slate-500">Check-out</p>
                <p class="mt-2 font-semibold text-slate-900">{{ displayCheckOutDate }}</p>
                <p class="mt-1 text-sm text-slate-400">{{ checkOutTimeLabel }}</p>
              </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
              <div class="rounded-2xl border border-slate-100 bg-white p-4 shadow-sm">
                <p class="text-sm text-slate-500">Jumlah Kamar</p>
                <p class="mt-2 font-semibold text-slate-900">{{ booking.total_rooms || 1 }} kamar</p>
              </div>
              <div class="rounded-2xl border border-slate-100 bg-white p-4 shadow-sm">
                <p class="text-sm text-slate-500">Lama Menginap</p>
                <p class="mt-2 font-semibold text-slate-900">{{ totalNights }} malam</p>
              </div>
            </div>
          </div>

          <div class="rounded-[2rem] border border-white/70 bg-white/90 p-8 shadow-2xl shadow-slate-200/70 backdrop-blur">
            <p class="text-xs font-semibold uppercase tracking-[0.25em] text-[#1b456f]">Payment Summary</p>
            <h2 class="mt-2 text-2xl font-bold text-slate-900">Rincian Biaya</h2>
            <div class="mt-6 space-y-4">
              <div class="flex justify-between"><span class="text-slate-500">Harga per Malam</span><span class="font-semibold text-slate-900">Rp {{ formatPrice(booking.room_type?.price_per_night || 0) }}</span></div>
              <div class="flex justify-between"><span class="text-slate-500">Subtotal</span><span class="font-semibold text-slate-900">Rp {{ formatPrice(subtotal) }}</span></div>
              <div class="flex justify-between"><span class="text-slate-500">Pajak (10%)</span><span class="font-semibold text-slate-900">Rp {{ formatPrice(tax) }}</span></div>
              <div class="flex justify-between" :class="lateCheckoutPenalty > 0 ? 'text-rose-700' : ''">
                <span>
                  Denda Keterlambatan
                  <template v-if="lateCheckoutHours > 0">({{ lateCheckoutHours }} jam)</template>
                </span>
                <span class="font-semibold">Rp {{ formatPrice(lateCheckoutPenalty) }}</span>
              </div>
              <div v-if="lateCheckoutPenalty > 0" class="flex justify-between">
                <span class="text-slate-500">Status Pembayaran Denda</span>
                <span :class="penaltyPaymentStatusClass" class="font-semibold">
                  {{ penaltyPaymentStatusText }}
                </span>
              </div>
              <div class="flex justify-between border-t border-slate-100 pt-4 text-lg font-bold">
                <span class="text-slate-900">Total Tagihan Saat Ini</span>
                <span :class="billingStatusClass">{{ billingStatusText }}</span>
              </div>
            </div>
          </div>
        </div>

        <div class="space-y-8">
          <div class="rounded-[2rem] border border-white/70 bg-white/90 p-8 shadow-2xl shadow-slate-200/70 backdrop-blur">
            <div class="mb-5 flex items-start justify-between gap-4">
              <div>
                <p class="text-xs font-semibold uppercase tracking-[0.25em] text-[#1b456f]">Guest Info</p>
                <h3 class="mt-2 text-2xl font-bold text-slate-900">Informasi Tamu</h3>
              </div>
              <span :class="getPaymentStatusClass(booking.payment_status)" class="rounded-full px-3 py-1.5 text-xs font-semibold">{{ getPaymentStatusText(booking.payment_status) }}</span>
            </div>
            <div class="space-y-4">
              <div class="flex justify-between gap-4"><span class="text-slate-500">Nama Lengkap</span><span class="text-right font-semibold text-slate-900">{{ booking.guest?.full_name || 'N/A' }}</span></div>
              <div class="flex justify-between gap-4"><span class="text-slate-500">Email</span><span class="break-all text-right font-semibold text-slate-900">{{ booking.guest?.email || 'N/A' }}</span></div>
              <div class="flex justify-between gap-4"><span class="text-slate-500">No. Telepon</span><span class="text-right font-semibold text-slate-900">{{ booking.guest?.phone || 'N/A' }}</span></div>
            </div>
          </div>

          <div class="rounded-[2rem] border border-white/70 bg-white/90 p-8 shadow-2xl shadow-slate-200/70 backdrop-blur">
            <p class="text-xs font-semibold uppercase tracking-[0.25em] text-[#1b456f]">Payment Info</p>
            <h3 class="mt-2 text-2xl font-bold text-slate-900">Informasi Pembayaran</h3>
            <div class="mt-6 space-y-4">
              <div class="flex justify-between gap-4"><span class="text-slate-500">Metode Pembayaran</span><span class="font-semibold text-slate-900">{{ booking.payment_method === 'qris' ? 'QRIS' : booking.payment_method === 'cash' ? 'Tunai' : booking.payment_method }}</span></div>
              <div v-if="booking.payment_method === 'qris' && booking.requested_checkin_time" class="flex justify-between gap-4">
                <span class="text-slate-500">Jam Check-in Pilihan</span>
                <span class="font-semibold text-slate-900">{{ formatTimeOnly(booking.requested_checkin_time) }} WIB</span>
              </div>
              <div v-if="booking.payment_method === 'qris'" class="rounded-2xl bg-slate-50 p-4">
                <p class="text-sm text-slate-500">Pembayaran dilakukan melalui scan QRIS lalu upload bukti pembayaran.</p>
              </div>
              <div v-if="booking.payment_method === 'cash'" class="rounded-2xl bg-slate-50 p-4">
                <p class="text-sm text-slate-500">Pembayaran dilakukan secara tunai saat check-in di hotel.</p>
              </div>
              <div v-if="booking.payment_proof">
                <p class="mb-2 text-sm text-slate-500">Bukti Pembayaran</p>
                <img :src="booking.payment_proof" class="w-full rounded-2xl shadow-md" alt="Bukti Pembayaran">
              </div>
              <div v-if="paymentRejectionReason" class="rounded-2xl border border-rose-200 bg-rose-50 p-4">
                <p class="text-sm font-semibold text-rose-700">Alasan Penolakan Pembayaran</p>
                <p class="mt-1 text-sm text-rose-700">{{ paymentRejectionReason }}</p>
              </div>
              <div v-if="booking.checkin_receipt_proof">
                <p class="mb-2 text-sm text-slate-500">Struk Check-in (Diupload Tamu)</p>
                <img :src="booking.checkin_receipt_proof" class="w-full rounded-2xl shadow-md" alt="Struk Check-in">
                <p v-if="booking.checkin_receipt_uploaded_at" class="mt-2 text-xs text-slate-500">
                  Diupload: {{ formatDate(booking.checkin_receipt_uploaded_at) }} {{ formatTimeWib(booking.checkin_receipt_uploaded_at) || '' }}
                </p>
              </div>
            </div>
          </div>

          <div class="rounded-[2rem] border border-white/70 bg-white/90 p-8 shadow-2xl shadow-slate-200/70 backdrop-blur">
            <p class="text-xs font-semibold uppercase tracking-[0.25em] text-[#1b456f]">Action Center</p>
            <h3 class="mt-2 text-2xl font-bold text-slate-900">Aksi</h3>
            <div class="mt-6 space-y-3">
              <div
                v-if="booking.booking_status === 'confirmed' && booking.payment_method === 'qris' && isPaymentCompleted"
                class="rounded-2xl border border-blue-100 bg-blue-50 p-4 text-sm font-medium text-[#1b456f]"
              >
                Pembayaran QRIS sudah terverifikasi. Check-in akan diproses setelah disetujui resepsionis.
              </div>
              <div
                v-if="booking.booking_status === 'confirmed' && booking.payment_method === 'qris' && isPaymentCompleted && !booking.checkin_receipt_proof"
                class="rounded-2xl border border-amber-100 bg-amber-50 p-4 text-sm font-medium text-amber-700"
              >
                Silakan cetak struk booking lalu upload di sini agar resepsionis dapat menyetujui check-in Anda.
              </div>
              <div
                v-if="booking.booking_status === 'confirmed' && booking.payment_method === 'qris' && isPaymentCompleted && booking.checkin_receipt_proof"
                class="rounded-2xl border border-emerald-100 bg-emerald-50 p-4 text-sm font-medium text-emerald-700"
              >
                Struk check-in sudah terupload. Resepsionis dapat melihat bukti dan menyetujui check-in Anda.
              </div>
              <div
                v-if="booking.booking_status === 'confirmed' && booking.payment_method === 'qris' && booking.payment_status === 'paid'"
                class="rounded-2xl border border-amber-100 bg-amber-50 p-4 text-sm font-medium text-amber-700"
              >
                Bukti pembayaran QRIS sudah diupload. Saat ini menunggu persetujuan pembayaran dari resepsionis.
              </div>
              <div
                v-if="booking.booking_status === 'confirmed' && booking.payment_method === 'qris' && booking.payment_status === 'rejected'"
                class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm font-medium text-rose-700"
              >
                Pembayaran ditolak oleh resepsionis. Silakan upload ulang bukti pembayaran sesuai nominal tagihan.
              </div>
              <div
                v-if="booking.booking_status === 'confirmed' && booking.payment_method === 'cash' && isPaymentCompleted"
                class="rounded-2xl border border-blue-100 bg-blue-50 p-4 text-sm font-medium text-[#1b456f]"
              >
                Booking tunai tidak perlu upload bukti pembayaran. Silakan upload struk booking agar resepsionis dapat menyetujui check-in.
              </div>
              <div
                v-if="booking.booking_status === 'confirmed' && booking.payment_method === 'cash' && isPaymentCompleted && booking.checkin_receipt_proof"
                class="rounded-2xl border border-emerald-100 bg-emerald-50 p-4 text-sm font-medium text-emerald-700"
              >
                Struk check-in sudah terupload. Resepsionis dapat melihat bukti dan menyetujui check-in Anda.
              </div>
              <div
                v-if="hasPayableLateCheckoutPenalty"
                class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm font-medium text-rose-700"
              >
                Checkout sudah melewati batas waktu. Denda berjalan: Rp {{ formatPrice(lateCheckoutPenalty) }} ({{ lateCheckoutHours }} jam x Rp {{ formatPrice(lateCheckoutPenaltyPerHour) }}/jam).
              </div>
              <div
                v-if="isPenaltyPaymentWaitingVerification"
                class="rounded-2xl border border-amber-100 bg-amber-50 p-4 text-sm font-medium text-amber-700"
              >
                Bukti pembayaran denda QRIS sudah diupload. Menunggu verifikasi resepsionis.
              </div>
              <div
                v-if="isPenaltyPaymentRejected && hasPayableLateCheckoutPenalty"
                class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm font-medium text-rose-700"
              >
                Pembayaran denda ditolak. Silakan bayar dan upload ulang bukti pembayaran denda.
              </div>
              <div
                v-if="isAutoCheckoutInProgress"
                class="rounded-2xl border border-blue-100 bg-blue-50 p-4 text-sm font-medium text-[#1b456f]"
              >
                Pembayaran denda sudah terverifikasi. Check-out sedang diproses otomatis...
              </div>
              <div
                v-if="penaltyPaymentRejectionReason"
                class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm font-medium text-rose-700"
              >
                Alasan penolakan pembayaran denda: {{ penaltyPaymentRejectionReason }}
              </div>
              <button v-if="canShowCheckoutButton" @click="checkOut" class="w-full rounded-2xl bg-[#1b456f] py-3.5 font-semibold text-white transition hover:bg-[#163b63]">Check-out</button>
              <button
                v-if="canShowPayPenaltyButton"
                @click="goToPenaltyPayment"
                class="w-full rounded-2xl bg-amber-500 py-3.5 font-semibold text-white transition hover:bg-amber-600"
              >
                Bayar Denda Checkout
              </button>
              <div
                v-if="booking.booking_status === 'confirmed' && isPaymentCompleted"
                class="space-y-2"
              >
                <template v-if="!booking.checkin_receipt_proof">
                  <input
                    ref="checkinReceiptInput"
                    type="file"
                    accept="image/*"
                    class="hidden"
                    @change="handleCheckinReceiptChange"
                  >
                  <button
                    @click="openCheckinReceiptPicker"
                    class="w-full rounded-2xl border border-[#1b456f] bg-white py-3 text-sm font-semibold text-[#1b456f] transition hover:bg-blue-50"
                  >
                    Pilih File Struk Check-in
                  </button>
                  <p v-if="selectedCheckinReceiptName" class="text-xs text-slate-600">
                    File dipilih: {{ selectedCheckinReceiptName }}
                  </p>
                  <button
                    :disabled="!selectedCheckinReceipt || uploadingCheckinReceipt"
                    @click="submitCheckinReceipt"
                    class="w-full rounded-2xl bg-[#1b456f] py-3.5 font-semibold text-white transition hover:bg-[#163b63] disabled:cursor-not-allowed disabled:opacity-60"
                  >
                    {{ uploadingCheckinReceipt ? 'Mengupload...' : 'Upload Struk Check-in' }}
                  </button>
                </template>
              </div>
              <button v-if="booking.booking_status === 'confirmed' && booking.payment_method === 'qris' && ['pending', 'rejected'].includes(String(booking.payment_status))" @click="uploadPaymentProof" class="w-full rounded-2xl bg-amber-500 py-3.5 font-semibold text-white transition hover:bg-amber-600">Upload Bukti Bayar</button>
              <button v-if="booking.booking_status === 'confirmed' && booking.payment_status === 'pending'" @click="cancelBooking" class="w-full rounded-2xl bg-rose-600 py-3.5 font-semibold text-white transition hover:bg-rose-700">Batalkan Pesanan</button>
            </div>
          </div>
        </div>
      </div>

      <div v-else class="py-16 text-center">
        <p class="text-slate-600">Pemesanan tidak ditemukan</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '~/stores/auth'
import { useBookingStore } from '~/stores/booking'

definePageMeta({ middleware: 'guest' })

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()
const bookingStore = useBookingStore()
const bookingId = computed(() => {
  const raw = route.params.id
  const normalize = (value) => {
    try {
      return decodeURIComponent(value)
    } catch {
      return value
    }
  }
  if (Array.isArray(raw)) {
    return normalize(String(raw[0] || ''))
  }
  return normalize(String(raw || ''))
})
const booking = ref(null)
const loading = ref(true)
const checkinReceiptInput = ref(null)
const selectedCheckinReceipt = ref(null)
const selectedCheckinReceiptName = ref('')
const uploadingCheckinReceipt = ref(false)
const isAutoCheckoutInProgress = ref(false)
const nowTick = ref(Date.now())
let nowTickInterval = null

const isPaymentCompleted = computed(() => {
  if (!booking.value) return false
  return String(booking.value.payment_status) === 'verified'
})
const isBookingPaymentWaitingVerification = computed(() => {
  if (!booking.value) return false
  return booking.value.payment_method === 'qris' && String(booking.value.payment_status) === 'paid'
})
const getBookingPayment = (bookingValue) => {
  if (!Array.isArray(bookingValue?.payments)) return null
  return bookingValue.payments
    .filter((payment) => payment?.payment_type === 'booking')
    .sort((a, b) => Number(b?.id || 0) - Number(a?.id || 0))[0] || null
}
const getLatestPenaltyPayment = (bookingValue) => {
  if (!Array.isArray(bookingValue?.payments)) return null
  return bookingValue.payments
    .filter((payment) => payment?.payment_type === 'late_checkout_penalty')
    .sort((a, b) => Number(b?.id || 0) - Number(a?.id || 0))[0] || null
}
const paymentRejectionReason = computed(() => {
  const payment = getBookingPayment(booking.value)
  if (!payment || String(payment?.status) !== 'rejected') return ''
  return String(payment?.notes || '').trim()
})
const penaltyPaymentRejectionReason = computed(() => {
  const payment = getLatestPenaltyPayment(booking.value)
  if (!payment || String(payment?.status) !== 'rejected') return ''
  return String(payment?.notes || '').trim()
})
const latestPenaltyPayment = computed(() => getLatestPenaltyPayment(booking.value))
const latestPenaltyPaymentStatus = computed(() => String(latestPenaltyPayment.value?.status || '').toLowerCase())
const latestPenaltyPaymentMethod = computed(() => String(latestPenaltyPayment.value?.payment_method || '').toLowerCase())

const totalNights = computed(() => {
  if (!booking.value?.check_in_date || !booking.value?.check_out_date) return 0
  const start = new Date(booking.value.check_in_date)
  const end = new Date(booking.value.check_out_date)
  return Math.ceil((end - start) / (1000 * 60 * 60 * 24))
})

const toNumber = (value) => {
  if (typeof value === 'number') return Number.isFinite(value) ? value : 0
  if (typeof value === 'string') {
    const normalized = value.replace(/[^\d.-]/g, '')
    const parsed = Number(normalized)
    return Number.isFinite(parsed) ? parsed : 0
  }
  const parsed = Number(value)
  return Number.isFinite(parsed) ? parsed : 0
}

const subtotal = computed(() => {
  if (!booking.value) return 0
  return toNumber(booking.value.room_type?.price_per_night) * totalNights.value * toNumber(booking.value.total_rooms || 1)
})

const tax = computed(() => subtotal.value * 0.1)
const formatPrice = (price) => new Intl.NumberFormat('id-ID').format(price)
const lateCheckoutPenaltyPerHour = computed(() => toNumber(booking.value?.late_checkout_penalty_per_hour || 75000))

const getWibTimestamp = (dateValue = new Date()) => {
  const formatter = new Intl.DateTimeFormat('en-CA', {
    timeZone: 'Asia/Jakarta',
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit',
    hour12: false,
  })
  const parts = formatter.formatToParts(dateValue)
  const read = (type) => Number(parts.find((p) => p.type === type)?.value || 0)
  const year = read('year')
  const month = read('month')
  const day = read('day')
  const hour = read('hour')
  const minute = read('minute')
  const second = read('second')
  return Date.UTC(year, month - 1, day, hour, minute, second)
}

const computeLiveLateCheckoutHours = () => {
  if (!booking.value) return 0
  if (String(booking.value.booking_status || '') !== 'checked_in') return 0

  const checkOutDate = String(booking.value.check_out_date || '')
  const dateMatch = checkOutDate.match(/^(\d{4})-(\d{2})-(\d{2})/)
  if (!dateMatch) return 0

  const year = Number(dateMatch[1])
  const month = Number(dateMatch[2])
  const day = Number(dateMatch[3])
  const scheduledCheckOutWibTs = Date.UTC(year, month - 1, day, 12, 0, 0)
  const nowWibTs = getWibTimestamp(new Date(nowTick.value))
  const lateMinutes = Math.floor((nowWibTs - scheduledCheckOutWibTs) / 60000)
  if (lateMinutes <= 0) return 0

  return Math.ceil(lateMinutes / 60)
}

const lateCheckoutHours = computed(() => {
  if (!booking.value) return 0
  const status = String(booking.value.booking_status || '')
  if (status === 'checked_out') return toNumber(booking.value.late_checkout_hours || booking.value.pending_late_checkout_hours || 0)
  if (status === 'checked_in') {
    const recordedHours = toNumber(booking.value.late_checkout_hours || booking.value.pending_late_checkout_hours || 0)
    const liveHours = computeLiveLateCheckoutHours()
    return Math.max(recordedHours, liveHours)
  }
  return 0
})
const lateCheckoutPenalty = computed(() => {
  if (!booking.value) return 0

  const status = String(booking.value.booking_status || '')
  if (!['checked_in', 'checked_out'].includes(status)) return 0

  const recordedPenalty = toNumber(booking.value.late_checkout_penalty || booking.value.pending_late_checkout_penalty || 0)
  if (recordedPenalty > 0) {
    return recordedPenalty
  }

  // Fallback: hitung otomatis berdasarkan lama telat checkout.
  return lateCheckoutHours.value * lateCheckoutPenaltyPerHour.value
})
const hasPayableLateCheckoutPenalty = computed(() => {
  if (!booking.value || String(booking.value.booking_status) !== 'checked_in') return false
  if (lateCheckoutPenalty.value <= 0) return false
  return Boolean(booking.value.requires_penalty_payment ?? true)
})
const isPenaltyPaymentWaitingVerification = computed(() => {
  if (!hasPayableLateCheckoutPenalty.value) return false
  if (latestPenaltyPaymentMethod.value !== 'qris') return false
  return ['pending', 'paid'].includes(latestPenaltyPaymentStatus.value)
})
const isPenaltyPaymentRejected = computed(() => {
  if (!hasPayableLateCheckoutPenalty.value) return false
  return latestPenaltyPaymentStatus.value === 'rejected'
})
const canShowPayPenaltyButton = computed(() => {
  if (!hasPayableLateCheckoutPenalty.value) return false
  return !isPenaltyPaymentWaitingVerification.value
})
const canShowCheckoutButton = computed(() => {
  if (!booking.value || String(booking.value.booking_status) !== 'checked_in') return false
  if (lateCheckoutPenalty.value <= 0) return true
  return !Boolean(booking.value.requires_penalty_payment ?? true)
})
const penaltyPaymentStatusText = computed(() => {
  if (isPenaltyPaymentSettled.value) return 'Lunas'
  if (isPenaltyPaymentWaitingVerification.value) return 'Menunggu Verifikasi Resepsionis'
  return 'Belum Lunas'
})
const penaltyPaymentStatusClass = computed(() => {
  if (isPenaltyPaymentSettled.value) return 'text-emerald-700'
  if (isPenaltyPaymentWaitingVerification.value) return 'text-amber-700'
  return 'text-rose-700'
})
const isPenaltyPaymentSettled = computed(() => {
  if (!booking.value) return false
  if (lateCheckoutPenalty.value <= 0) return true

  if (Boolean(booking.value.requires_penalty_payment ?? false)) {
    return false
  }

  if (String(booking.value.booking_status || '') === 'checked_out') {
    return true
  }

  const latestPenaltyPayment = getLatestPenaltyPayment(booking.value)
  return String(latestPenaltyPayment?.status || '') === 'verified'
})
const isBillingSettled = computed(() => {
  if (!booking.value) return false

  const bookingPaymentSettled = String(booking.value.payment_status || '') === 'verified'
  if (!bookingPaymentSettled) return false

  return isPenaltyPaymentSettled.value
})
const billingStatusText = computed(() => {
  if (isBillingSettled.value) return 'Lunas'
  if (isBookingPaymentWaitingVerification.value) return 'Menunggu Verifikasi Resepsionis'
  if (isPenaltyPaymentWaitingVerification.value) return 'Menunggu Verifikasi Resepsionis'
  return 'Belum Lunas'
})
const billingStatusClass = computed(() => {
  if (isBillingSettled.value) return 'text-emerald-700'
  if (isBookingPaymentWaitingVerification.value) return 'text-amber-700'
  if (isPenaltyPaymentWaitingVerification.value) return 'text-amber-700'
  return 'text-rose-700'
})
const formatDate = (dateValue) => {
  if (!dateValue) return ''

  const text = String(dateValue)
  if (/^\d{4}-\d{2}-\d{2}$/.test(text)) {
    const [year, month, day] = text.split('-').map((part) => Number(part))
    return new Intl.DateTimeFormat('id-ID', {
      day: 'numeric',
      month: 'long',
      year: 'numeric',
      timeZone: 'Asia/Jakarta',
    }).format(new Date(Date.UTC(year, month - 1, day)))
  }

  return new Intl.DateTimeFormat('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
    timeZone: 'Asia/Jakarta',
  }).format(new Date(text))
}
const formatTimeWib = (dateTime) => {
  if (!dateTime) return null
  const formatted = new Intl.DateTimeFormat('id-ID', {
    hour: '2-digit',
    minute: '2-digit',
    hour12: false,
    timeZone: 'Asia/Jakarta',
  }).format(new Date(dateTime))
  return `${formatted.replace('.', ':')} WIB`
}
const formatTimeOnly = (value) => {
  if (!value) return '-'
  return String(value).slice(0, 5)
}

const checkInTimeLabel = computed(() => {
  if (!booking.value) return '14:00 WIB'

  const hasCheckedIn = ['checked_in', 'checked_out'].includes(String(booking.value.booking_status))
  if (hasCheckedIn && booking.value.actual_check_in_at) {
    return formatTimeWib(booking.value.actual_check_in_at) || '14:00 WIB'
  }

  if (booking.value.payment_method === 'qris' && booking.value.requested_checkin_time) {
    return `${formatTimeOnly(booking.value.requested_checkin_time)} WIB`
  }

  return '14:00 WIB'
})

const checkOutTimeLabel = computed(() => {
  if (!booking.value) return '12:00 WIB'

  if (String(booking.value.booking_status) === 'checked_out' && booking.value.actual_check_out_at) {
    return formatTimeWib(booking.value.actual_check_out_at) || '12:00 WIB'
  }

  return '12:00 WIB'
})

const displayCheckOutDate = computed(() => {
  if (!booking.value) return '-'

  if (String(booking.value.booking_status) === 'checked_out' && booking.value.actual_check_out_at) {
    return formatDate(booking.value.actual_check_out_at)
  }

  return formatDate(booking.value.check_out_date)
})

const getStatusClass = (status) => ({ confirmed: 'bg-blue-100 text-[#1b456f]', checked_in: 'bg-emerald-100 text-emerald-700', checked_out: 'bg-slate-100 text-slate-700', cancelled: 'bg-rose-100 text-rose-700' }[status] || 'bg-gray-100 text-gray-800')
const getStatusText = (status) => ({ confirmed: 'Dikonfirmasi', checked_in: 'Check-in', checked_out: 'Selesai', cancelled: 'Dibatalkan' }[status] || status)
const getBookingStatusText = (bookingValue) => {
  if (!bookingValue) return 'Memuat'
  if (bookingValue.booking_status === 'confirmed' && bookingValue.payment_method === 'qris' && ['pending', 'paid'].includes(String(bookingValue.payment_status))) {
    return 'Menunggu Persetujuan Pembayaran'
  }
  return getStatusText(bookingValue.booking_status)
}
const getPaymentStatusClass = (status) => ({ pending: 'bg-amber-100 text-amber-700', paid: 'bg-blue-100 text-[#1b456f]', verified: 'bg-emerald-100 text-emerald-700', rejected: 'bg-rose-100 text-rose-700' }[status] || 'bg-gray-100 text-gray-800')
const getPaymentStatusText = (status) => ({ pending: 'Menunggu Pembayaran', paid: 'Menunggu Persetujuan', verified: 'Terverifikasi', rejected: 'Ditolak' }[status] || status)

const refreshBookingDetail = async () => {
  const result = await bookingStore.getBookingDetail(bookingId.value, authStore.token)
  if (result.success) {
    booking.value = result.data
  }
}
const tryAutoCheckoutAfterPenaltyVerified = async () => {
  if (isAutoCheckoutInProgress.value) return
  if (!booking.value || String(booking.value.booking_status) !== 'checked_in') return
  if (lateCheckoutPenalty.value <= 0) return
  if (latestPenaltyPaymentStatus.value !== 'verified') return

  isAutoCheckoutInProgress.value = true
  const result = await bookingStore.guestCheckOut(bookingId.value, authStore.token)
  if (result.success) {
    await refreshBookingDetail()
    if (String(booking.value?.booking_status || '') === 'checked_out') {
      alert('Pembayaran denda terverifikasi. Check-out berhasil diproses otomatis.')
    }
  }
  isAutoCheckoutInProgress.value = false
}

const checkOut = async () => {
  if (!confirm('Lanjutkan proses check-out sekarang?')) {
    return
  }

  const result = await bookingStore.guestCheckOut(bookingId.value, authStore.token)
  if (!result.success) {
    alert(result.message || 'Gagal check-out')
    return
  }

  if (result?.data?.requires_penalty_payment) {
    alert(`Checkout belum bisa selesai.\nDenda keterlambatan: Rp ${formatPrice(Number(result?.data?.late_checkout_penalty || 0))} (${result?.data?.late_checkout_hours || 0} jam).\nSilakan bayar denda terlebih dahulu.`)
    await navigateTo(`/guest/penalty-payment/${encodeURIComponent(String(bookingId.value))}`)
    return
  }

  const penalty = Number(result?.data?.late_checkout_penalty || 0)
  if (penalty > 0) {
    alert(`Check-out berhasil.\nDenda keterlambatan: Rp ${formatPrice(penalty)} (${result?.data?.late_checkout_hours || 0} jam).`)
  } else {
    alert('Check-out berhasil')
  }

  const refreshed = await bookingStore.getBookingDetail(bookingId.value, authStore.token)
  if (refreshed.success) {
    booking.value = refreshed.data
  }
}
const uploadPaymentProof = () => {
  navigateTo(`/guest/payment/${bookingId.value}`)
}
const openCheckinReceiptPicker = () => {
  checkinReceiptInput.value?.click()
}
const handleCheckinReceiptChange = (event) => {
  const file = event?.target?.files?.[0] || null
  selectedCheckinReceipt.value = file
  selectedCheckinReceiptName.value = file ? file.name : ''
}
const submitCheckinReceipt = async () => {
  if (!selectedCheckinReceipt.value) {
    alert('Pilih file struk check-in terlebih dahulu.')
    return
  }

  uploadingCheckinReceipt.value = true
  const result = await bookingStore.uploadCheckInReceipt(String(bookingId.value), selectedCheckinReceipt.value, authStore.token)
  uploadingCheckinReceipt.value = false

  if (!result.success) {
    alert(result.message || 'Gagal upload struk check-in')
    return
  }

  alert('Struk check-in berhasil diupload')
  selectedCheckinReceipt.value = null
  selectedCheckinReceiptName.value = ''
  if (checkinReceiptInput.value) {
    checkinReceiptInput.value.value = ''
  }
  await refreshBookingDetail()
}
const goToPenaltyPayment = () => {
  navigateTo(`/guest/penalty-payment/${encodeURIComponent(String(bookingId.value))}`)
}
const cancelBooking = async () => {
  if (confirm('Apakah Anda yakin ingin membatalkan pemesanan ini?')) {
    const result = await bookingStore.cancelBooking(bookingId.value, authStore.token)
    if (result.success) {
      alert('Pemesanan berhasil dibatalkan')
      router.push('/guest/dashboard')
    } else {
      alert(result.message || 'Gagal membatalkan pemesanan')
    }
  }
}
const handleLogout = async () => {
  await authStore.logout()
  await navigateTo('/login')
}

onMounted(async () => {
  nowTickInterval = setInterval(() => {
    nowTick.value = Date.now()
  }, 30000)

  if (!bookingId.value) {
    alert('ID pemesanan tidak valid')
    router.push('/guest/booking')
    return
  }

  const result = await bookingStore.getBookingDetail(bookingId.value, authStore.token)
  if (result.success) {
    booking.value = result.data
    await tryAutoCheckoutAfterPenaltyVerified()
  } else {
    alert('Pemesanan tidak ditemukan')
    router.push('/guest/dashboard')
  }
  loading.value = false
})

onUnmounted(() => {
  if (nowTickInterval) {
    clearInterval(nowTickInterval)
    nowTickInterval = null
  }
})

watch(
  () => [
    String(booking.value?.booking_status || ''),
    String(booking.value?.requires_penalty_payment ?? ''),
    lateCheckoutPenalty.value,
    latestPenaltyPaymentStatus.value,
  ],
  async () => {
    await tryAutoCheckoutAfterPenaltyVerified()
  }
)

</script>
