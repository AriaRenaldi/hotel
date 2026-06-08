<template>
  <div class="min-h-screen bg-slate-100 py-8 print:bg-white print:py-0">
    <div class="mx-auto max-w-3xl px-4 print:max-w-none print:px-0">
      <div class="mb-4 flex justify-end gap-2 print:hidden">
        <NuxtLink to="/guest/booking" class="rounded-lg bg-slate-200 px-4 py-2 text-sm font-semibold text-slate-700">
          Kembali
        </NuxtLink>
        <button @click="printReceipt" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white">
          Cetak Struk
        </button>
      </div>

      <div v-if="loading" class="rounded-2xl bg-white p-8 text-center text-slate-600 shadow-sm">
        Memuat data struk...
      </div>

      <div v-else-if="errorMessage" class="rounded-2xl bg-white p-8 text-center text-rose-700 shadow-sm">
        {{ errorMessage }}
      </div>

      <div v-else-if="booking" class="rounded-2xl bg-white p-8 shadow-sm print:rounded-none print:shadow-none">
        <div class="mb-6 border-b border-dashed border-slate-300 pb-6 text-center">
          <h1 class="text-2xl font-bold text-slate-900">HOTELKU</h1>
          <p class="text-sm text-slate-500">Bukti Pemesanan / Struk Reservasi</p>
        </div>

        <div class="mb-6 grid gap-3 text-sm sm:grid-cols-2">
          <div class="flex justify-between gap-4"><span class="text-slate-500">No. Booking</span><span class="font-semibold text-slate-900">{{ booking.booking_number }}</span></div>
          <div class="flex justify-between gap-4"><span class="text-slate-500">Tanggal Cetak</span><span class="font-semibold text-slate-900">{{ formatDateTime(new Date()) }}</span></div>
          <div class="flex justify-between gap-4"><span class="text-slate-500">Nama Tamu</span><span class="font-semibold text-slate-900">{{ booking.guest?.full_name || '-' }}</span></div>
          <div class="flex justify-between gap-4"><span class="text-slate-500">Email</span><span class="font-semibold text-slate-900">{{ booking.guest?.email || '-' }}</span></div>
          <div class="flex justify-between gap-4"><span class="text-slate-500">Check-in</span><span class="font-semibold text-slate-900">{{ formatDate(booking.check_in_date) }}</span></div>
          <div class="flex justify-between gap-4"><span class="text-slate-500">Check-out</span><span class="font-semibold text-slate-900">{{ displayCheckOutDate }}</span></div>
          <div class="flex justify-between gap-4"><span class="text-slate-500">Tipe Kamar</span><span class="font-semibold text-slate-900">{{ booking.room_type?.type_name || '-' }}</span></div>
          <div class="flex justify-between gap-4"><span class="text-slate-500">Jumlah Kamar</span><span class="font-semibold text-slate-900">{{ booking.total_rooms }}</span></div>
          <div class="flex justify-between gap-4"><span class="text-slate-500">Metode Bayar</span><span class="font-semibold text-slate-900">{{ booking.payment_method === 'qris' ? 'QRIS' : booking.payment_method }}</span></div>
          <div class="flex justify-between gap-4"><span class="text-slate-500">Status Bayar</span><span class="font-semibold text-emerald-700">{{ paymentStatusText }}</span></div>
        </div>

        <div class="mb-6 rounded-xl border border-slate-200 p-4">
          <div class="mb-2 flex justify-between text-sm"><span class="text-slate-500">Subtotal</span><span class="font-semibold text-slate-900">Rp {{ formatPrice(booking.subtotal) }}</span></div>
          <div class="mb-2 flex justify-between text-sm"><span class="text-slate-500">Pajak</span><span class="font-semibold text-slate-900">Rp {{ formatPrice(booking.tax) }}</span></div>
          <div class="flex justify-between border-t border-dashed border-slate-300 pt-2 text-base"><span class="font-bold text-slate-900">Total</span><span class="font-bold text-slate-900">Rp {{ formatPrice(booking.total_amount) }}</span></div>
        </div>

        <p class="text-center text-xs text-slate-500">
          Tunjukkan struk ini kepada resepsionis saat check-in.
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useBookingStore } from '~/stores/booking'

const route = useRoute()
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
const errorMessage = ref('')

const paymentStatusText = computed(() => {
  if (!booking.value) return '-'
  return 'Lunas'
})

const displayCheckOutDate = computed(() => {
  if (!booking.value) return '-'
  if (booking.value.booking_status === 'checked_out' && booking.value.actual_check_out_at) {
    return formatDate(booking.value.actual_check_out_at)
  }
  return formatDate(booking.value.check_out_date)
})

const formatPrice = (value) => new Intl.NumberFormat('id-ID').format(Number(value || 0))
const formatDate = (value) => new Date(value).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
const formatDateTime = (value) => new Date(value).toLocaleString('id-ID')

const printReceipt = () => {
  window.print()
}

onMounted(async () => {
  if (!bookingId.value) {
    errorMessage.value = 'ID pemesanan tidak valid.'
    loading.value = false
    return
  }

  const result = await bookingStore.getPrintableBooking(bookingId.value)

  if (!result.success) {
    errorMessage.value = result.message || 'Gagal memuat struk.'
    loading.value = false
    return
  }

  booking.value = result.data
  loading.value = false
})
</script>
