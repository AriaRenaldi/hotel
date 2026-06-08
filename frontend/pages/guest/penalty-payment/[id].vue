<template>
  <div class="relative min-h-screen overflow-hidden bg-gradient-to-br from-[#05142b] via-[#0c2d54] to-[#d9e6f5]">
    <div class="pointer-events-none absolute inset-0">
      <div class="absolute -top-24 left-1/3 h-72 w-72 rounded-full bg-[#2f5f95]/35 blur-3xl"></div>
      <div class="absolute bottom-0 right-16 h-72 w-72 rounded-full bg-[#89abd1]/25 blur-3xl"></div>
    </div>

    <div class="relative mx-auto max-w-5xl px-4 py-10 sm:px-6">
      <section class="rounded-[2rem] border border-slate-200 bg-white p-6 text-slate-900 shadow-2xl shadow-slate-300/60 md:p-8">
        <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
          <div>
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-[#0f2f57]">Late Checkout Penalty</p>
            <h1 class="mt-3 text-3xl font-bold sm:text-4xl">Pembayaran Denda Checkout</h1>
            <p class="mt-3 max-w-2xl text-sm text-slate-600 sm:text-base">
              Denda keterlambatan harus diselesaikan terlebih dahulu agar proses checkout bisa selesai.
            </p>
          </div>
          <NuxtLink :to="`/guest/booking/${bookingId}`" class="inline-flex items-center justify-center rounded-2xl bg-gradient-to-r from-[#061a33] via-[#0f2f57] to-[#1d4a7a] px-5 py-3 text-sm font-semibold text-white transition hover:brightness-110">
            Kembali ke Detail Booking
          </NuxtLink>
        </div>
      </section>

      <div v-if="loading" class="mt-8">
        <PageSkeletonLoader message="Memuat detail denda checkout..." :card-count="2" :row-count="6" />
      </div>

      <div v-else-if="penaltyDetail" class="mt-8 space-y-6">
        <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-2xl shadow-slate-300/60 md:p-8">
          <div class="grid gap-4 sm:grid-cols-2">
            <div class="rounded-2xl bg-slate-50 px-4 py-3">
              <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Nomor Booking</p>
              <p class="mt-1 text-sm font-semibold text-slate-900">{{ penaltyDetail.booking?.booking_number }}</p>
            </div>
            <div class="rounded-2xl bg-slate-50 px-4 py-3">
              <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Total Denda</p>
              <p class="mt-1 text-xl font-bold text-rose-600">Rp {{ formatPrice(penaltyDetail.late_checkout_penalty || 0) }}</p>
            </div>
            <div class="rounded-2xl bg-slate-50 px-4 py-3">
              <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Durasi Terlambat</p>
              <p class="mt-1 text-sm font-semibold text-slate-900">{{ penaltyDetail.late_checkout_hours || 0 }} jam</p>
            </div>
            <div class="rounded-2xl bg-slate-50 px-4 py-3">
              <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Tarif Denda</p>
              <p class="mt-1 text-sm font-semibold text-slate-900">Rp {{ formatPrice(penaltyDetail.late_checkout_penalty_per_hour || 0) }} / jam</p>
            </div>
          </div>
        </section>

        <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-2xl shadow-slate-300/60 md:p-8">
          <p class="text-xs font-semibold uppercase tracking-[0.25em] text-[#0f2f57]">Metode Pembayaran</p>
          <h3 class="mt-2 text-2xl font-bold text-slate-900">Bayar Denda</h3>

          <div class="mt-5 grid gap-4">
            <div>
              <label class="mb-2 block text-sm font-semibold text-slate-700">Pilih Metode</label>
              <select v-model="form.paymentMethod" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#0f2f57]">
                <option value="qris">QRIS</option>
                <option value="cash">Tunai (verifikasi otomatis)</option>
              </select>
            </div>

            <div>
              <label class="mb-2 block text-sm font-semibold text-slate-700">Nominal Bayar</label>
              <input
                v-model.number="form.paidAmount"
                type="number"
                min="0"
                step="0.01"
                class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#0f2f57]"
              >
              <p class="mt-2 text-xs text-slate-500">Minimal Rp {{ formatPrice(minimumAmount) }}.</p>
            </div>

            <div v-if="form.paymentMethod === 'qris'" class="space-y-3">
              <div class="rounded-3xl border border-slate-200 bg-gradient-to-br from-slate-50 to-blue-50 p-5">
                <img src="/images/qris-hotel.jpeg" alt="QRIS Hotel" class="mx-auto w-full max-w-[220px] rounded-2xl border border-slate-200 bg-white object-contain shadow-md">
              </div>

              <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50/80 p-4">
                <input id="penalty-proof-file" type="file" accept="image/*" class="hidden" @change="onSelectProof">
                <label for="penalty-proof-file" class="flex cursor-pointer items-center justify-between gap-4 rounded-xl bg-white px-4 py-3 text-sm font-medium text-slate-700 shadow-sm transition hover:bg-slate-100">
                  <span class="truncate">{{ selectedProofName || 'Pilih bukti pembayaran denda' }}</span>
                  <span class="rounded-lg bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">Browse</span>
                </label>
              </div>
            </div>

            <button :disabled="submitting" @click="submitPenaltyPayment" class="rounded-2xl bg-gradient-to-r from-[#061a33] via-[#0f2f57] to-[#1d4a7a] px-5 py-3.5 text-sm font-semibold text-white shadow-lg shadow-slate-300 transition hover:-translate-y-0.5 disabled:cursor-not-allowed disabled:opacity-60">
              {{ submitting ? 'Memproses...' : 'Bayar Denda Checkout' }}
            </button>
          </div>
        </section>

        <p v-if="message" class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
          {{ message }}
        </p>
        <p v-if="errorMessage" class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700">
          {{ errorMessage }}
        </p>
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

const bookingId = computed(() => String(route.params.id || ''))
const loading = ref(true)
const submitting = ref(false)
const penaltyDetail = ref(null)
const message = ref('')
const errorMessage = ref('')
const selectedProof = ref(null)

const form = reactive({
  paymentMethod: 'qris',
  paidAmount: 0,
})

const minimumAmount = computed(() => Number(penaltyDetail.value?.late_checkout_penalty || 0))
const selectedProofName = computed(() => selectedProof.value?.name || '')

const normalizeAmount = (value) => {
  const parsed = Number(value)
  if (!Number.isFinite(parsed)) return 0
  return Math.max(0, Math.round(parsed * 100) / 100)
}

const formatPrice = (price) => new Intl.NumberFormat('id-ID', {
  minimumFractionDigits: 0,
  maximumFractionDigits: 2,
}).format(normalizeAmount(price))

const onSelectProof = (event) => {
  const target = event.target
  selectedProof.value = target?.files?.[0] || null
}

const loadPenaltyDetail = async () => {
  loading.value = true
  const result = await bookingStore.getLateCheckoutPenaltyDetail(bookingId.value, authStore.token)
  if (!result.success || !result.data) {
    errorMessage.value = result.message || 'Gagal memuat detail denda checkout.'
    loading.value = false
    return
  }

  penaltyDetail.value = result.data
  form.paidAmount = normalizeAmount(result.data?.late_checkout_penalty || 0)
  loading.value = false
}

const submitPenaltyPayment = async () => {
  errorMessage.value = ''
  message.value = ''

  if (normalizeAmount(form.paidAmount) < normalizeAmount(minimumAmount.value)) {
    errorMessage.value = `Nominal pembayaran tidak boleh kurang dari Rp ${formatPrice(minimumAmount.value)}.`
    return
  }

  if (form.paymentMethod === 'qris' && !selectedProof.value) {
    errorMessage.value = 'Untuk metode QRIS, bukti pembayaran wajib diupload.'
    return
  }

  submitting.value = true

  const result = await bookingStore.payLateCheckoutPenalty(
    bookingId.value,
    {
      payment_method: form.paymentMethod,
      paid_amount: normalizeAmount(form.paidAmount),
      payment_proof: selectedProof.value,
    },
    authStore.token
  )

  if (!result.success) {
    errorMessage.value = result.message || 'Gagal memproses pembayaran denda.'
    submitting.value = false
    return
  }

  message.value = result.message || 'Pembayaran denda berhasil diproses.'

  if (result.data?.booking_status === 'checked_out' || result.data?.booking?.booking_status === 'checked_out') {
    await navigateTo(`/guest/booking/${bookingId.value}`)
    return
  }

  await loadPenaltyDetail()
  submitting.value = false
}

onMounted(loadPenaltyDetail)
</script>
