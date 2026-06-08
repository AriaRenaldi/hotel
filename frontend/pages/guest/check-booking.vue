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
            <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Booking Lookup</p>
          </div>
        </NuxtLink>

        <div class="flex flex-wrap items-center gap-4 text-sm font-medium text-slate-600">
          <NuxtLink to="/guest/dashboard" class="transition hover:text-[#1b456f]">Dashboard</NuxtLink>
          <NuxtLink to="/guest/rooms" class="transition hover:text-[#1b456f]">Rooms</NuxtLink>
          <NuxtLink to="/guest/booking" class="transition hover:text-[#1b456f]">Booking</NuxtLink>
          <NuxtLink to="/guest/check-booking" class="text-[#1b456f] font-semibold">Check Booking</NuxtLink>
          <NuxtLink to="/profile" class="transition hover:text-[#1b456f]">Profil</NuxtLink>
          <button @click="handleLogout" class="rounded-xl bg-rose-50 px-4 py-2 text-sm font-semibold text-rose-600 transition hover:bg-rose-100">
            Logout
          </button>
        </div>
      </div>
    </nav>

    <div class="mx-auto max-w-6xl px-4 py-12 sm:px-6">
      <section class="relative overflow-hidden rounded-[2rem] bg-gradient-to-br from-[#07111f] via-[#0f2742] to-[#163b63] px-8 py-12 text-white shadow-2xl shadow-slate-300/40">
        <div class="absolute inset-0 opacity-20">
          <div class="absolute left-8 top-8 h-48 w-48 rounded-full bg-white/10 blur-3xl"></div>
          <div class="absolute right-12 bottom-0 h-64 w-64 rounded-full bg-blue-300/20 blur-3xl"></div>
        </div>
        <div class="relative max-w-3xl">
          <p class="text-xs font-semibold uppercase tracking-[0.3em] text-blue-100">Reservation Search</p>
          <h1 class="mt-4 text-4xl font-bold">Cek Pesanan Anda</h1>
          <p class="mt-4 text-lg leading-relaxed text-slate-200">
            Masukkan nomor pemesanan dan email untuk melihat detail reservasi Anda dalam tampilan yang lebih nyaman dan elegan.
          </p>
        </div>
      </section>

      <div class="mt-8 grid items-start gap-8 lg:grid-cols-[0.95fr_1.05fr]">
        <div class="rounded-[2rem] border border-white/70 bg-white/90 p-8 shadow-2xl shadow-slate-200/70 backdrop-blur">
          <div class="mb-8">
            <div class="inline-flex items-center gap-2 rounded-full border border-blue-100 bg-blue-50 px-4 py-2 text-xs font-semibold uppercase tracking-[0.25em] text-[#1b456f]">
              <span class="h-2 w-2 animate-pulse rounded-full bg-emerald-400"></span>
              Lookup Form
            </div>
            <h2 class="mt-4 text-3xl font-bold text-slate-900">Temukan reservasi Anda</h2>
            <p class="mt-3 text-slate-500">Cari data booking dengan cepat untuk melihat status pemesanan dan pembayaran terbaru.</p>
          </div>

          <form @submit.prevent="checkBooking" class="space-y-6">
            <div>
              <label class="mb-2 block text-sm font-semibold text-slate-700">Nomor Pemesanan</label>
              <input v-model="form.booking_number" type="text" required placeholder="Contoh: BK202604021234" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-[#1b456f]">
            </div>
            <div>
              <label class="mb-2 block text-sm font-semibold text-slate-700">Alamat Email</label>
              <input v-model="form.email" type="email" required placeholder="email@example.com" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-[#1b456f]">
            </div>
            <button type="submit" :disabled="searching" class="w-full rounded-2xl bg-gradient-to-r from-[#07111f] via-[#0f2742] to-[#1b456f] py-4 text-sm font-semibold text-white shadow-lg transition hover:-translate-y-0.5 disabled:opacity-60">
              {{ searching ? 'Mencari...' : 'Cari Pesanan' }}
            </button>
          </form>
        </div>

        <div class="space-y-6">
          <div v-if="booking" class="overflow-hidden rounded-[2rem] border border-white/70 bg-white/90 shadow-2xl shadow-slate-200/70 backdrop-blur">
            <div class="border-b border-slate-100 bg-gradient-to-r from-emerald-50 to-blue-50 px-8 py-6">
              <p class="text-xs font-semibold uppercase tracking-[0.25em] text-emerald-600">Reservation Found</p>
              <h2 class="mt-2 text-2xl font-bold text-slate-900">Kode Batang Reservasi</h2>
            </div>
            <div class="p-8">
              <p class="mb-4 text-sm text-slate-500">Scan barcode di bawah untuk membuka halaman cetak bukti pemesanan.</p>
              <div class="rounded-2xl border border-slate-200 bg-white p-6">
                <div class="overflow-x-auto rounded-xl border border-slate-100 bg-slate-50 p-4">
                  <canvas
                    ref="barcodeCanvas"
                    class="mx-auto min-w-[560px] bg-white"
                    aria-label="Barcode Reservasi"
                  />
                </div>
                <p v-if="barcodeLoadError" class="mt-3 text-sm font-medium text-rose-600">{{ barcodeLoadError }}</p>
              </div>
              <p class="mt-4 break-all text-xs text-slate-400">{{ printUrl }}</p>
            </div>
          </div>

          <div v-if="error" class="rounded-[2rem] border border-rose-200 bg-rose-50 p-8 text-center shadow-lg">
            <p class="font-semibold text-rose-700">{{ error }}</p>
          </div>

          <div v-if="!booking && !error" class="rounded-[2rem] border border-white/70 bg-white/80 p-8 shadow-xl shadow-slate-200/60 backdrop-blur">
            <p class="text-xs font-semibold uppercase tracking-[0.25em] text-[#1b456f]">Elegant Lookup</p>
            <h3 class="mt-3 text-2xl font-bold text-slate-900">Semua detail reservasi dalam satu tempat</h3>
            <p class="mt-3 leading-relaxed text-slate-500">Mulai dari status booking sampai pembayaran, semuanya bisa Anda cek dengan cepat untuk pengalaman tamu yang lebih tenang.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useAuthStore } from '~/stores/auth'
import { useBookingStore } from '~/stores/booking'

definePageMeta({ middleware: 'guest' })

const authStore = useAuthStore()
const bookingStore = useBookingStore()
const form = reactive({ booking_number: '', email: '' })
const booking = ref(null)
const error = ref('')
const searching = ref(false)
const barcodeCanvas = ref(null)
const barcodeLoadError = ref('')
const scannerBuffer = ref('')
const scannerTimer = ref(null)

const formatPrice = (price) => new Intl.NumberFormat('id-ID').format(price)
const formatDate = (date) => date ? new Date(date).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : '-'
const getStatusClass = (status) => ({ confirmed: 'bg-blue-100 text-[#1b456f]', checked_in: 'bg-emerald-100 text-emerald-700', checked_out: 'bg-slate-100 text-slate-700', cancelled: 'bg-rose-100 text-rose-700' }[status] || 'bg-gray-100 text-gray-800')
const getStatusText = (status) => ({ confirmed: 'Dikonfirmasi', checked_in: 'Check-in', checked_out: 'Selesai', cancelled: 'Dibatalkan' }[status] || status)
const getPaymentClass = (status) => ({ pending: 'bg-amber-100 text-amber-700', paid: 'bg-orange-100 text-orange-700', verified: 'bg-emerald-100 text-emerald-700', cancelled: 'bg-rose-100 text-rose-700' }[status] || 'bg-gray-100 text-gray-800')
const getPaymentText = (status) => ({ pending: 'Menunggu', paid: 'Sudah Bayar', verified: 'Lunas', cancelled: 'Dibatalkan' }[status] || status)
const printPath = computed(() => {
  const identifier = booking.value?.id || booking.value?.booking_number
  if (!identifier) return ''
  return `/p/${encodeURIComponent(String(identifier))}`
})
const printUrl = computed(() => {
  if (!printPath.value) return ''
  if (process.client) {
    return `${window.location.origin}${printPath.value}`
  }
  return printPath.value
})
const getBookingNumericId = () => {
  const rawId = booking.value?.id
  const parsed = Number(rawId)
  if (Number.isFinite(parsed) && parsed > 0) return parsed

  const bookingNumber = String(booking.value?.booking_number || '')
  const digits = bookingNumber.replace(/\D/g, '')
  if (!digits) return 0
  return Number(digits.slice(-9))
}

const calculateEAN13Checksum = (ean12) => {
  let sum = 0
  for (let i = 0; i < 12; i += 1) {
    const digit = Number(ean12[i] || 0)
    sum += i % 2 === 0 ? digit : digit * 3
  }
  const remainder = sum % 10
  return remainder === 0 ? 0 : 10 - remainder
}

const generateBookingEAN13 = () => {
  const bookingNumericId = getBookingNumericId()
  if (!bookingNumericId) return ''
  const idPart = String(bookingNumericId).slice(-9).padStart(9, '0')
  const ean12 = `899${idPart}`
  const checksum = calculateEAN13Checksum(ean12)
  return `${ean12}${checksum}`
}

const barcodeValue = computed(() => generateBookingEAN13())

const loadJsBarcodeLibrary = () => {
  return new Promise((resolve, reject) => {
    if (process.server) {
      resolve()
      return
    }

    if (typeof window.JsBarcode !== 'undefined') {
      resolve()
      return
    }

    const existingScript = document.querySelector('script[data-jsbarcode="true"]')
    if (existingScript) {
      existingScript.addEventListener('load', () => resolve())
      existingScript.addEventListener('error', () => reject(new Error('Gagal memuat JsBarcode')))
      return
    }

    const script = document.createElement('script')
    script.src = 'https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js'
    script.async = true
    script.dataset.jsbarcode = 'true'
    script.onload = () => resolve()
    script.onerror = () => reject(new Error('Gagal memuat JsBarcode'))
    document.head.appendChild(script)
  })
}

const renderBarcode = async () => {
  if (process.server || !barcodeValue.value || !barcodeCanvas.value) return

  try {
    barcodeLoadError.value = ''
    await loadJsBarcodeLibrary()

    window.JsBarcode(barcodeCanvas.value, barcodeValue.value, {
      format: 'EAN13',
      width: 2,
      height: 86,
      displayValue: true,
      fontSize: 12,
      fontOptions: 'bold',
      textMargin: 8,
      margin: 10,
      background: '#ffffff',
      lineColor: '#000000'
    })
  } catch (err) {
    barcodeLoadError.value = err?.message || 'Barcode gagal dibuat'
  }
}

const resolveScannedTarget = (rawValue) => {
  const value = String(rawValue || '').trim()
  if (!value) return ''

  if (/^https?:\/\//i.test(value)) return value
  if (value.startsWith('/')) return value

  if (/^\d{13}$/.test(value) && value.startsWith('899')) {
    const extractedId = String(parseInt(value.slice(3, 12), 10))
    return `/guest/booking/print/${encodeURIComponent(extractedId)}`
  }

  if (/^\d+$/.test(value) || /^BK/i.test(value)) {
    return `/guest/booking/print/${encodeURIComponent(value)}`
  }

  return ''
}

const processScannerBuffer = () => {
  const target = resolveScannedTarget(scannerBuffer.value)
  scannerBuffer.value = ''

  if (!target || process.server) return

  if (/^https?:\/\//i.test(target)) {
    window.location.href = target
    return
  }

  navigateTo(target)
}

const handleScannerKeydown = (event) => {
  const key = event.key
  if (!key) return

  if (key === 'Enter') {
    processScannerBuffer()
    if (scannerTimer.value) clearTimeout(scannerTimer.value)
    scannerTimer.value = null
    return
  }

  if (key.length !== 1) return

  scannerBuffer.value += key

  if (scannerTimer.value) clearTimeout(scannerTimer.value)
  scannerTimer.value = setTimeout(() => {
    processScannerBuffer()
  }, 120)
}

const checkBooking = async () => {
  searching.value = true
  error.value = ''
  booking.value = null

  const result = await bookingStore.checkBooking(form.booking_number, form.email)
  if (result.success) {
    booking.value = result.data
  } else {
    error.value = result.message || 'Pesanan tidak ditemukan'
  }

  searching.value = false
}

watch(barcodeValue, async (value) => {
  if (!value) return
  await nextTick()
  await renderBarcode()
})

onMounted(() => {
  document.addEventListener('keydown', handleScannerKeydown)
})

onUnmounted(() => {
  document.removeEventListener('keydown', handleScannerKeydown)
  if (scannerTimer.value) clearTimeout(scannerTimer.value)
})

const handleLogout = async () => {
  await authStore.logout()
  await navigateTo('/login')
}



</script>
