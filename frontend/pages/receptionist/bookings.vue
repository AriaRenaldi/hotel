<template>
  <div class="min-h-screen bg-gradient-to-br from-[#f3f7fb] via-[#eef4fb] to-[#dbe6f3] text-slate-900">
    <nav class="sticky top-0 z-50 border-b border-white/70 bg-white/85 backdrop-blur-xl shadow-sm">
      <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-6">
        <NuxtLink to="/receptionist/dashboard" class="flex items-center gap-3">
          <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-r from-[#4b5563] via-[#6b7280] to-[#9ca3af] text-white shadow-lg">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
              />
            </svg>
          </div>
          <div>
            <p class="text-lg font-bold text-slate-900">HotelKu</p>
            <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Reception Desk</p>
          </div>
        </NuxtLink>

        <div class="flex flex-wrap items-center gap-2 rounded-full border border-slate-200/80 bg-white/80 p-2 text-sm font-medium text-slate-600 shadow-lg shadow-slate-200/40 backdrop-blur">
          <NuxtLink
            to="/receptionist/dashboard"
            class="rounded-full px-4 py-2 transition hover:bg-slate-100 hover:text-slate-900"
          >
            Dashboard
          </NuxtLink>

          <NuxtLink
            to="/receptionist/bookings"
            class="rounded-full bg-slate-800 px-4 py-2 font-semibold text-white shadow-md"
          >
            Bookings
          </NuxtLink>

          <NuxtLink
            to="/receptionist/reports"
            class="rounded-full px-4 py-2 transition hover:bg-slate-100 hover:text-slate-900"
          >
            Reports
          </NuxtLink>
          <NuxtLink
            to="/receptionist/penalty-verifications"
            class="rounded-full px-4 py-2 transition hover:bg-slate-100 hover:text-slate-900"
          >
            Verifikasi Denda
          </NuxtLink>
          <NuxtLink
            to="/receptionist/penalty-reports"
            class="rounded-full px-4 py-2 transition hover:bg-slate-100 hover:text-slate-900"
          >
            Laporan Denda
          </NuxtLink>

          <NuxtLink
            to="/profile"
            class="rounded-full px-4 py-2 transition hover:bg-slate-100 hover:text-slate-900"
          >
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
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-200">Booking Overview</p>
            <h1 class="mt-4 text-4xl font-bold">Daftar Pemesanan</h1>
            <p class="mt-4 max-w-2xl text-lg text-slate-100">
              Kelola semua pemesanan hotel dengan tampilan silver yang elegan, hidup, dan profesional.
            </p>
          </div>

          <div class="grid gap-4 sm:grid-cols-2">
            <div class="rounded-3xl border border-white/15 bg-white/10 px-5 py-4 backdrop-blur shadow-lg shadow-slate-900/10">
              <p class="text-xs uppercase tracking-[0.2em] text-slate-200">Overview</p>
              <p class="mt-2 text-lg font-semibold">{{ allBookings.length }} reservasi</p>
            </div>

            <div class="rounded-3xl border border-white/15 bg-white/10 px-5 py-4 backdrop-blur shadow-lg shadow-slate-900/10">
              <p class="text-xs uppercase tracking-[0.2em] text-slate-200">Service</p>
              <p class="mt-2 text-lg font-semibold">Smooth & precise</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="relative z-10 mx-auto -mt-8 max-w-[100rem] px-4 pb-10 sm:px-6">
      <div class="mb-6 grid gap-4 md:grid-cols-4">
        <div class="rounded-[1.75rem] border border-white/70 bg-white/90 p-5 shadow-xl shadow-slate-200/60 backdrop-blur transition duration-300 hover:-translate-y-1 hover:shadow-2xl">
          <p class="text-sm font-medium text-slate-500">Total Pemesanan</p>
          <p class="mt-2 text-3xl font-bold text-slate-900">{{ allBookings.length }}</p>
        </div>

        <div class="rounded-[1.75rem] border border-white/70 bg-white/90 p-5 shadow-xl shadow-slate-200/60 backdrop-blur transition duration-300 hover:-translate-y-1 hover:shadow-2xl">
          <p class="text-sm font-medium text-slate-500">Check-in Hari Ini</p>
          <p class="mt-2 text-3xl font-bold text-emerald-600">{{ todayCheckIns }}</p>
        </div>

        <div class="rounded-[1.75rem] border border-white/70 bg-white/90 p-5 shadow-xl shadow-slate-200/60 backdrop-blur transition duration-300 hover:-translate-y-1 hover:shadow-2xl">
          <p class="text-sm font-medium text-slate-500">Check-out Hari Ini</p>
          <p class="mt-2 text-3xl font-bold text-amber-600">{{ todayCheckOuts }}</p>
        </div>

        <div class="rounded-[1.75rem] border border-white/70 bg-white/90 p-5 shadow-xl shadow-slate-200/60 backdrop-blur transition duration-300 hover:-translate-y-1 hover:shadow-2xl">
          <p class="text-sm font-medium text-slate-500">Menunggu Persetujuan Check-in</p>
          <p class="mt-2 text-3xl font-bold text-orange-600">{{ pendingPayments }}</p>
        </div>
      </div>

      <div class="mb-6 overflow-hidden rounded-[2rem] border border-white/70 bg-white/92 shadow-2xl shadow-slate-200/60 backdrop-blur">
        <div class="border-b border-slate-100 bg-gradient-to-r from-slate-50 to-zinc-100 px-8 py-6">
          <p class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Filter Panel</p>
          <h2 class="mt-2 text-2xl font-bold text-slate-900">Saring Data Pemesanan</h2>
        </div>

        <div class="p-6">
          <div class="grid gap-4 md:grid-cols-4">
            <div>
              <label class="mb-2 block text-sm font-semibold text-slate-700">Tanggal Check-in</label>
              <input
                v-model="filters.check_in_date"
                type="date"
                class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-700 shadow-sm transition focus:outline-none focus:ring-2 focus:ring-slate-400"
              >
            </div>

            <div>
              <label class="mb-2 block text-sm font-semibold text-slate-700">Nama Tamu</label>
              <input
                v-model="filters.guest_name"
                type="text"
                placeholder="Cari nama..."
                class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-700 shadow-sm transition focus:outline-none focus:ring-2 focus:ring-slate-400"
              >
            </div>

            <div>
              <label class="mb-2 block text-sm font-semibold text-slate-700">Status</label>
              <select
                v-model="filters.booking_status"
                class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-700 shadow-sm transition focus:outline-none focus:ring-2 focus:ring-slate-400"
              >
                <option value="">Semua</option>
                <option value="confirmed">Dikonfirmasi</option>
                <option value="checked_in">Check-in</option>
                <option value="checked_out">Check-out</option>
                <option value="cancelled">Dibatalkan</option>
              </select>
            </div>

            <div class="flex items-end">
              <button
                @click="loadBookings"
                class="w-full rounded-2xl bg-gradient-to-r from-[#4b5563] via-[#6b7280] to-[#9ca3af] py-3 text-sm font-semibold text-white shadow-lg shadow-slate-300 transition duration-300 hover:-translate-y-0.5 hover:shadow-xl"
              >
                Filter
              </button>
            </div>
          </div>
        </div>
      </div>

      <div class="overflow-hidden rounded-[2rem] border border-white/70 bg-white/92 shadow-2xl shadow-slate-200/60 backdrop-blur">
        <div class="border-b border-slate-100 bg-gradient-to-r from-slate-50 to-zinc-100 px-8 py-6">
          <p class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Booking Table</p>
          <h2 class="mt-2 text-2xl font-bold text-slate-900">Manajemen Pemesanan</h2>
        </div>

        <div v-if="loading" class="py-12 text-center">
          <div class="inline-block h-8 w-8 animate-spin rounded-full border-b-2 border-slate-500"></div>
        </div>

        <div v-else class="overflow-x-auto">
          <table class="w-full min-w-[1750px]">
            <thead class="bg-slate-50/80">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Booking</th>
                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Tamu</th>
                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Menginap</th>
                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Total</th>
                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Bukti Bayar Lunas</th>
                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Struk Check-in</th>
                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Disetujui Oleh</th>
                <th class="min-w-[170px] px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Status</th>
                <th class="min-w-[170px] px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Pembayaran</th>
                <th class="min-w-[400px] px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Aksi</th>
              </tr>
            </thead>

            <tbody class="divide-y divide-slate-100">
              <tr v-for="b in paginatedBookings" :key="b.id" class="transition hover:bg-slate-50/70">
                <td class="px-6 py-4">
                  <p class="text-sm font-semibold text-slate-900">{{ b.booking_number }}</p>
                  <p class="mt-1 text-xs text-slate-500">ID #{{ b.id }}</p>
                </td>

                <td class="px-6 py-4">
                  <div class="text-sm font-semibold text-slate-900">{{ b.guest?.full_name }}</div>
                  <div class="text-xs text-slate-500">{{ b.guest?.email }}</div>
                </td>

                <td class="px-6 py-4">
                  <div class="space-y-1 text-sm text-slate-600">
                    <p class="font-semibold text-slate-800">
                      Kamar {{ b.booking_details?.[0]?.room?.room_number || '-' }}
                      <span class="font-normal text-slate-500">({{ b.booking_details?.[0]?.room?.room_type?.type_name || '-' }})</span>
                    </p>
                    <p class="text-xs text-slate-500">{{ formatDate(b.check_in_date) }} - {{ formatDate(b.check_out_date) }}</p>
                    <p v-if="b.payment_method === 'qris' && b.requested_checkin_time" class="text-xs text-slate-500">
                      Jam check-in pilihan: {{ String(b.requested_checkin_time).slice(0, 5) }} WIB
                    </p>
                  </div>
                </td>

                <td class="px-6 py-4 text-sm font-semibold text-slate-700">Rp {{ formatPrice(b.total_amount) }}</td>

                <td class="px-6 py-4">
                  <span v-if="b.payment_method === 'cash'" class="text-xs font-semibold text-slate-600">Tunai (tanpa upload bukti)</span>
                  <a
                    v-else-if="getBookingProofUrl(b)"
                    :href="getBookingProofUrl(b)"
                    target="_blank"
                    class="text-xs font-semibold text-blue-600 underline"
                  >
                    Lihat Bukti
                  </a>
                  <span v-else class="text-xs text-slate-500">Belum upload</span>
                </td>
                <td class="px-6 py-4">
                  <a
                    v-if="getCheckinReceiptUrl(b)"
                    :href="getCheckinReceiptUrl(b)"
                    target="_blank"
                    class="text-xs font-semibold text-blue-600 underline"
                  >
                    Lihat Struk
                  </a>
                  <span v-else class="text-xs text-slate-500">Belum upload</span>
                </td>
                <td class="px-6 py-4">
                  <p class="text-xs font-semibold text-slate-700">{{ getPaymentVerifierName(b) }}</p>
                  <p v-if="getPaymentVerifiedAtText(b)" class="mt-1 text-xs text-slate-500">{{ getPaymentVerifiedAtText(b) }}</p>
                </td>

                <td class="px-6 py-4">
                  <span
                    :class="getStatusClass(b)"
                    class="rounded-full px-3 py-1.5 text-xs font-semibold"
                  >
                    {{ getStatusText(b) }}
                  </span>
                </td>

                <td class="px-6 py-4">
                  <span :class="getPaymentClass(b.payment_status)" class="rounded-full px-3 py-1.5 text-xs font-semibold">
                    {{ getPaymentText(b.payment_status) }}
                  </span>
                  <p v-if="getPaymentRejectionNote(b)" class="mt-2 text-xs text-rose-600">
                    Alasan ditolak: {{ getPaymentRejectionNote(b) }}
                  </p>
                </td>

                <td class="px-6 py-4 space-x-2">
                  <div class="flex flex-wrap items-center gap-2">
                    <div
                      v-if="canReviewPayment(b)"
                      class="flex flex-wrap items-center gap-2"
                    >
                      <select
                        v-model="paymentReviewActions[b.id]"
                        class="rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-slate-400"
                      >
                        <option value="verified">Setujui Pembayaran</option>
                        <option value="rejected">Tolak Pembayaran</option>
                      </select>
                      <input
                        v-if="paymentReviewActions[b.id] === 'rejected'"
                        v-model="paymentReviewNotes[b.id]"
                        type="text"
                        placeholder="Tulis alasan penolakan..."
                        class="min-w-[220px] rounded-xl border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-700 placeholder:text-rose-400 focus:outline-none focus:ring-2 focus:ring-rose-300"
                      >
                      <button
                        :disabled="processingBookingId === b.id"
                        @click="processPaymentReview(b)"
                        class="inline-flex items-center rounded-full bg-slate-700 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-60"
                      >
                        {{ processingBookingId === b.id ? 'Memproses...' : 'Proses Pembayaran' }}
                      </button>
                    </div>

                    <button
                      v-if="canApproveCheckIn(b)"
                      :disabled="processingBookingId === b.id"
                      @click="approveCheckIn(b)"
                      class="inline-flex items-center rounded-full bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60"
                    >
                      {{ processingBookingId === b.id ? 'Memproses...' : 'Setujui Check-in' }}
                    </button>
                    <span
                      v-else-if="b.booking_status === 'confirmed' && b.payment_status === 'verified' && !b.checkin_receipt_proof"
                      class="inline-flex rounded-full bg-amber-100 px-3 py-1.5 text-xs font-semibold text-amber-700"
                    >
                      Menunggu upload struk tamu
                    </span>
                    <span
                      v-else-if="b.booking_status === 'checked_in'"
                      class="text-xs font-semibold text-slate-500"
                    >
                      Check-out oleh guest
                    </span>
                    <span
                      v-else
                      class="inline-flex rounded-full bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-600"
                    >
                      {{ getActionText(b) }}
                    </span>
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

const bookings = ref([])
const allBookings = ref([])
const loading = ref(true)
const processingBookingId = ref(null)
const paymentReviewActions = reactive({})
const paymentReviewNotes = reactive({})
const currentPage = ref(1)
const itemsPerPage = 6
const filters = reactive({
  check_in_date: '',
  guest_name: '',
  booking_status: ''
})

const toLocalDateString = (value = new Date()) => {
  const date = new Date(value)
  if (Number.isNaN(date.getTime())) return ''
  const year = date.getFullYear()
  const month = String(date.getMonth() + 1).padStart(2, '0')
  const day = String(date.getDate()).padStart(2, '0')
  return `${year}-${month}-${day}`
}

const getDatePart = (value) => {
  if (!value) return ''
  if (typeof value === 'string') return value.slice(0, 10)
  return toLocalDateString(value)
}

const today = computed(() => toLocalDateString())
const todayCheckIns = computed(() => allBookings.value.filter(b => getDatePart(b.check_in_date) === today.value).length)
const todayCheckOuts = computed(() => allBookings.value.filter((b) => {
  if (b.booking_status !== 'checked_out') return false

  const actualCheckOut = getDatePart(b.actual_check_out_at)
  const scheduledCheckOut = getDatePart(b.check_out_date)

  return actualCheckOut === today.value || (!actualCheckOut && scheduledCheckOut === today.value)
}).length)
const pendingPayments = computed(() => allBookings.value.filter((b) => {
  return b.booking_status === 'confirmed'
    && b.payment_status === 'verified'
    && !b.checkin_receipt_proof
}).length)
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

const formatPrice = (p) => new Intl.NumberFormat('id-ID').format(p)
const formatDate = (d) => d ? new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : '-'
const FILE_BASE_URL = 'http://127.0.0.1:8000'
const normalizeProofUrl = (value) => {
  if (!value) return ''
  const raw = String(value).trim()
  if (!raw) return ''
  if (raw.startsWith('http://') || raw.startsWith('https://')) return raw
  if (raw.startsWith('/storage/')) return `${FILE_BASE_URL}${raw}`
  if (raw.startsWith('storage/')) return `${FILE_BASE_URL}/${raw}`
  return `${FILE_BASE_URL}/storage/${raw}`
}

const getBookingProofUrl = (booking) => {
  if (!booking) return ''
  const bookingProof = normalizeProofUrl(booking.payment_proof)
  if (bookingProof) return bookingProof

  const bookingPayment = Array.isArray(booking.payments)
    ? booking.payments.find((payment) => payment?.payment_type === 'booking')
    : null

  return normalizeProofUrl(bookingPayment?.proof_image)
}
const getCheckinReceiptUrl = (booking) => normalizeProofUrl(booking?.checkin_receipt_proof)
const getBookingPayment = (booking) => {
  if (!Array.isArray(booking?.payments)) return null
  return booking.payments
    .filter((payment) => payment?.payment_type === 'booking')
    .sort((a, b) => Number(b?.id || 0) - Number(a?.id || 0))[0] || null
}
const getPaymentVerifierName = (booking) => {
  if (booking?.payment_method === 'cash') return 'Tunai (tanpa verifikasi)'
  const payment = getBookingPayment(booking)
  if (!payment) return '-'
  if (payment?.status !== 'verified') return 'Belum diverifikasi'
  return payment?.verifier?.full_name || `ID ${payment?.verified_by || '-'}`
}
const getPaymentVerifiedAtText = (booking) => {
  const payment = getBookingPayment(booking)
  if (!payment?.verified_at) return ''
  return `Pada ${formatDate(payment.verified_at)}`
}
const getPaymentRejectionNote = (booking) => {
  const payment = getBookingPayment(booking)
  if (!payment || String(payment?.status) !== 'rejected') return ''
  return String(payment?.notes || '').trim()
}

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
  waiting_payment: 'bg-amber-100 text-amber-700',
  waiting_checkin: 'bg-blue-100 text-blue-800',
  checked_in: 'bg-green-100 text-green-800',
  checked_out: 'bg-gray-100 text-gray-800',
  cancelled: 'bg-red-100 text-red-800',
  rejected: 'bg-rose-100 text-rose-700',
}[deriveBookingStatus(booking)] || 'bg-gray-100 text-gray-800')

const getPaymentClass = (s) => ({
  pending: 'bg-yellow-100 text-yellow-800',
  paid: 'bg-orange-100 text-orange-800',
  verified: 'bg-green-100 text-green-800',
  rejected: 'bg-rose-100 text-rose-700',
  cancelled: 'bg-red-100 text-red-800'
}[s] || 'bg-gray-100 text-gray-800')

const getPaymentText = (s) => ({
  pending: 'Menunggu',
  paid: 'Menunggu Verifikasi',
  verified: 'Lunas',
  rejected: 'Ditolak',
  cancelled: 'Dibatalkan'
}[s] || s)

const getStatusText = (booking) => ({
  waiting_payment: 'Menunggu Pembayaran',
  waiting_checkin: 'Menunggu Check-in',
  checked_in: 'Check-in',
  checked_out: 'Selesai',
  cancelled: 'Dibatalkan',
  rejected: 'Ditolak',
}[deriveBookingStatus(booking)] || deriveBookingStatus(booking))

const getActionText = (booking) => {
  const derivedStatus = deriveBookingStatus(booking)
  if (derivedStatus === 'cancelled') return 'Dibatalkan'
  if (derivedStatus === 'checked_out') return 'Selesai'
  if (derivedStatus === 'waiting_checkin') return 'Menunggu persetujuan check-in'
  if (derivedStatus === 'waiting_payment') return 'Menunggu pembayaran'
  if (derivedStatus === 'rejected') return 'Pembayaran ditolak'
  return 'Pantau status'
}

const canApproveCheckIn = (booking) => {
  return booking?.booking_status === 'confirmed'
    && booking?.payment_status === 'verified'
    && Boolean(booking?.checkin_receipt_proof)
}

const canReviewPayment = (booking) => {
  return booking?.booking_status === 'confirmed'
    && booking?.payment_method === 'qris'
    && booking?.payment_status === 'paid'
}

const approveCheckIn = async (booking) => {
  if (!booking?.id || processingBookingId.value === booking.id) return

  processingBookingId.value = booking.id
  try {
    const result = await receptionistStore.processCheckIn(booking.id, authStore.token)
    if (!result.success) {
      alert(result.message || 'Gagal menyetujui check-in')
      return
    }

    await loadBookings()
  } finally {
    processingBookingId.value = null
  }
}

const processPaymentReview = async (booking) => {
  if (!booking?.id || processingBookingId.value === booking.id) return

  const selectedStatus = paymentReviewActions[booking.id] || 'verified'
  const note = String(paymentReviewNotes[booking.id] || '').trim()
  if (selectedStatus === 'rejected' && !note) {
    alert('Alasan penolakan wajib diisi.')
    return
  }
  const actionText = selectedStatus === 'verified' ? 'setujui' : 'tolak'
  if (!confirm(`Yakin ingin ${actionText} pembayaran booking ini?`)) {
    return
  }

  processingBookingId.value = booking.id
  try {
    const payload = selectedStatus === 'rejected'
      ? { status: selectedStatus, notes: note }
      : { status: selectedStatus }
    const result = await receptionistStore.verifyPayment(booking.id, payload, authStore.token)
    if (!result.success) {
      alert(result.message || 'Gagal memproses verifikasi pembayaran')
      return
    }

    if (selectedStatus === 'verified') {
      paymentReviewNotes[booking.id] = ''
    }

    await loadBookings()
  } finally {
    processingBookingId.value = null
  }
}

const handleLogout = async () => {
  await authStore.logout()
  await navigateTo('/login')
}

const loadBookings = async () => {
  loading.value = true
  const result = await receptionistStore.getAllBookings(authStore.token)
  if (result.success) {
    const allData = Array.isArray(result.data) ? result.data : []
    allBookings.value = allData

    let data = [...allData]
    if (filters.check_in_date) data = data.filter(b => getDatePart(b.check_in_date) === filters.check_in_date)
    if (filters.guest_name) {
      const name = filters.guest_name.toLowerCase()
      data = data.filter(b => b.guest?.full_name?.toLowerCase().includes(name))
    }
    if (filters.booking_status) data = data.filter(b => b.booking_status === filters.booking_status)
    data.forEach((item) => {
      if (!paymentReviewActions[item.id]) {
        paymentReviewActions[item.id] = 'verified'
      }
      if (paymentReviewNotes[item.id] === undefined) {
        paymentReviewNotes[item.id] = ''
      }
    })
    bookings.value = data
    currentPage.value = 1
  }
  loading.value = false
}

onMounted(() => loadBookings())
</script>
