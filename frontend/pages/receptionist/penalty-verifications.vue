<template>
  <div class="min-h-screen bg-gradient-to-br from-[#f3f7fb] via-[#eef4fb] to-[#dbe6f3] text-slate-900">
    <nav class="sticky top-0 z-50 border-b border-white/70 bg-white/85 backdrop-blur-xl shadow-sm">
      <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-6">
        <NuxtLink to="/receptionist/dashboard" class="flex items-center gap-3">
          <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-r from-[#4b5563] via-[#6b7280] to-[#9ca3af] text-white shadow-lg">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
          </div>
          <div>
            <p class="text-lg font-bold text-slate-900">HotelKu</p>
            <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Reception Desk</p>
          </div>
        </NuxtLink>

        <div class="flex flex-wrap items-center gap-2 rounded-full border border-slate-200/80 bg-white/80 p-2 text-sm font-medium text-slate-600 shadow-lg shadow-slate-200/40 backdrop-blur">
          <NuxtLink to="/receptionist/dashboard" class="rounded-full px-4 py-2 transition hover:bg-slate-100 hover:text-slate-900">
            Dashboard
          </NuxtLink>
          <NuxtLink to="/receptionist/bookings" class="rounded-full px-4 py-2 transition hover:bg-slate-100 hover:text-slate-900">
            Bookings
          </NuxtLink>
          <NuxtLink to="/receptionist/reports" class="rounded-full px-4 py-2 transition hover:bg-slate-100 hover:text-slate-900">
            Reports
          </NuxtLink>
          <NuxtLink to="/receptionist/penalty-verifications" class="rounded-full bg-slate-800 px-4 py-2 font-semibold text-white shadow-md">
            Verifikasi Denda
          </NuxtLink>
          <NuxtLink to="/receptionist/penalty-reports" class="rounded-full px-4 py-2 transition hover:bg-slate-100 hover:text-slate-900">
            Laporan Denda
          </NuxtLink>
          <NuxtLink to="/profile" class="rounded-full px-4 py-2 transition hover:bg-slate-100 hover:text-slate-900">
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
        <div class="absolute right-12 bottom-0 h-64 w-64 rounded-full bg-slate-100/20 blur-3xl"></div>
      </div>
      <div class="relative mx-auto max-w-7xl px-4 py-14 sm:px-6">
        <div class="grid items-center gap-8 lg:grid-cols-[1.2fr_0.8fr]">
          <div>
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-200">Late Checkout</p>
            <h1 class="mt-4 text-4xl font-bold">Verifikasi Pembayaran Denda</h1>
            <p class="mt-4 max-w-2xl text-lg text-slate-100">Kelola antrean pembayaran denda checkout dan validasi bukti pembayaran tamu dengan cepat.</p>
          </div>
          <div class="grid gap-4 sm:grid-cols-2">
            <div class="rounded-3xl border border-white/15 bg-white/10 px-5 py-4 backdrop-blur">
              <p class="text-xs uppercase tracking-[0.2em] text-slate-200">Antrian</p>
              <p class="mt-2 text-lg font-semibold">{{ statusTabs.find((s) => s.value === statusFilter)?.label || 'Menunggu Verifikasi' }}</p>
            </div>
            <div class="rounded-3xl border border-white/15 bg-white/10 px-5 py-4 backdrop-blur">
              <p class="text-xs uppercase tracking-[0.2em] text-slate-200">Items/Page</p>
              <p class="mt-2 text-lg font-semibold">{{ perPage }} data</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="relative z-10 mx-auto max-w-7xl px-4 py-8 sm:px-6">
      <div class="mb-6 grid gap-4 md:grid-cols-3">
        <button
          v-for="item in statusTabs"
          :key="item.value"
          @click="switchStatus(item.value)"
          :class="statusFilter === item.value ? 'bg-slate-900 text-white' : 'bg-white text-slate-700'"
          class="rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold shadow-sm transition"
        >
          {{ item.label }}
        </button>
      </div>

      <div v-if="errorMessage" class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700">
        {{ errorMessage }}
      </div>

      <div class="overflow-hidden rounded-[2rem] border border-white/70 bg-white/92 shadow-2xl shadow-slate-200/60 backdrop-blur">
        <div class="border-b border-slate-100 bg-gradient-to-r from-slate-50 to-zinc-100 px-8 py-6">
          <p class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Verification Queue</p>
          <h2 class="mt-2 text-2xl font-bold text-slate-900">Daftar Pembayaran Denda</h2>
        </div>

        <div v-if="loading" class="py-12 text-center">
          <div class="inline-block h-8 w-8 animate-spin rounded-full border-b-2 border-slate-500"></div>
        </div>

        <div v-else class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-slate-50/80">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Booking</th>
                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Tamu</th>
                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Metode</th>
                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Nominal</th>
                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Bukti</th>
                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Status</th>
                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <tr v-for="payment in payments" :key="payment.id" class="transition hover:bg-slate-50/70">
                <td class="px-6 py-4">
                  <p class="text-sm font-semibold text-slate-900">{{ payment.booking?.booking_number }}</p>
                  <p class="text-xs text-slate-500">ID Payment #{{ payment.id }}</p>
                </td>
                <td class="px-6 py-4 text-sm text-slate-700">{{ payment.booking?.guest?.full_name || '-' }}</td>
                <td class="px-6 py-4 text-sm text-slate-700 uppercase">{{ payment.payment_method }}</td>
                <td class="px-6 py-4 text-sm font-semibold text-slate-800">Rp {{ formatPrice(payment.amount || 0) }}</td>
                <td class="px-6 py-4">
                  <span v-if="String(payment.payment_method || '').toLowerCase() === 'cash'" class="text-xs text-slate-500">-</span>
                  <a v-else-if="payment.proof_image" :href="payment.proof_image" target="_blank" class="text-xs font-semibold text-blue-600 underline">Lihat Bukti</a>
                  <span v-else class="text-xs text-slate-500">Tidak ada</span>
                </td>
                <td class="px-6 py-4">
                  <span :class="statusClass(payment.status)" class="rounded-full px-3 py-1 text-xs font-semibold">
                    {{ payment.status }}
                  </span>
                </td>
                <td class="px-6 py-4">
                  <div v-if="payment.status === 'pending'" class="flex flex-wrap gap-2">
                    <button @click="verify(payment.id, 'verified')" class="rounded-lg bg-emerald-600 px-3 py-1 text-xs font-semibold text-white">Verifikasi</button>
                    <button @click="verify(payment.id, 'rejected')" class="rounded-lg bg-rose-600 px-3 py-1 text-xs font-semibold text-white">Tolak</button>
                  </div>
                  <span v-else class="text-xs text-slate-500">Selesai</span>
                </td>
              </tr>
              <tr v-if="payments.length === 0">
                <td colspan="7" class="px-6 py-8 text-center text-slate-500">Tidak ada data pembayaran denda.</td>
              </tr>
            </tbody>
          </table>
        </div>

        <AppPagination
          v-if="!loading && pagination.total > 0"
          :model-value="pagination.current_page"
          :total-pages="pagination.last_page || 1"
          :from="pagination.from || 0"
          :to="pagination.to || 0"
          :total="pagination.total || 0"
          @update:model-value="changePage"
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

const loading = ref(false)
const statusFilter = ref('pending')
const payments = ref([])
const errorMessage = ref('')
const perPage = 6
const pagination = ref({
  current_page: 1,
  last_page: 1,
  per_page: perPage,
  total: 0,
  from: 0,
  to: 0,
})

const statusTabs = [
  { value: 'pending', label: 'Menunggu Verifikasi' },
  { value: 'verified', label: 'Terverifikasi' },
  { value: 'rejected', label: 'Ditolak' },
]

const formatPrice = (value) => new Intl.NumberFormat('id-ID').format(Number(value || 0))
const statusClass = (status) => ({
  pending: 'bg-amber-100 text-amber-700',
  verified: 'bg-emerald-100 text-emerald-700',
  rejected: 'bg-rose-100 text-rose-700',
}[status] || 'bg-slate-100 text-slate-700')

const loadQueue = async (page = 1) => {
  loading.value = true
  errorMessage.value = ''
  const result = await receptionistStore.getLateCheckoutPenaltyVerifications(statusFilter.value, authStore.token, page, perPage)
  if (result.success) {
    payments.value = result.data?.payments || []
    pagination.value = result.data?.pagination || {
      current_page: 1,
      last_page: 1,
      per_page: perPage,
      total: 0,
      from: 0,
      to: 0,
    }
  } else {
    payments.value = []
    pagination.value = {
      current_page: 1,
      last_page: 1,
      per_page: perPage,
      total: 0,
      from: 0,
      to: 0,
    }
    errorMessage.value = result.message || 'Gagal memuat antrean verifikasi denda.'
  }
  loading.value = false
}

const switchStatus = async (status) => {
  statusFilter.value = status
  await loadQueue(1)
}

const changePage = async (page) => {
  if (page < 1 || page > pagination.value.last_page) return
  await loadQueue(page)
}

const verify = async (paymentId, status) => {
  const actionText = status === 'verified' ? 'verifikasi' : 'tolak'
  if (!confirm(`Yakin ingin ${actionText} pembayaran denda ini?`)) return

  let notes = ''
  if (status === 'rejected') {
    notes = (prompt('Masukkan alasan penolakan pembayaran denda:') || '').trim()
    if (!notes) {
      alert('Alasan penolakan wajib diisi.')
      return
    }
  }

  const payload = status === 'rejected' ? { status, notes } : { status }
  const result = await receptionistStore.verifyLateCheckoutPenaltyPayment(paymentId, payload, authStore.token)
  if (!result.success) {
    alert(result.message || 'Gagal memproses verifikasi')
    return
  }

  alert(result.message || 'Berhasil diproses')
  await loadQueue()
}

const handleLogout = async () => {
  await authStore.logout()
  await navigateTo('/login')
}

onMounted(loadQueue)
</script>
