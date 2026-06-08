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
            class="rounded-full px-4 py-2 transition hover:bg-slate-100 hover:text-slate-900"
          >
            Bookings
          </NuxtLink>

          <NuxtLink
            to="/receptionist/reports"
            class="rounded-full bg-slate-800 px-4 py-2 font-semibold text-white shadow-md"
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
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-200">Reports Overview</p>
            <h1 class="mt-4 text-4xl font-bold">Laporan Harian</h1>
            <p class="mt-4 max-w-2xl text-lg text-slate-100">
              Pantau ringkasan check-in dan check-out harian dengan tampilan silver yang rapi, elegan, dan tetap nyaman dibaca.
            </p>
          </div>

          <div class="grid gap-4 sm:grid-cols-2">
            <div class="rounded-3xl border border-white/15 bg-white/10 px-5 py-4 backdrop-blur shadow-lg shadow-slate-900/10">
              <p class="text-xs uppercase tracking-[0.2em] text-slate-200">Report Date</p>
              <p class="mt-2 text-lg font-semibold">{{ selectedDate }}</p>
            </div>

            <div class="rounded-3xl border border-white/15 bg-white/10 px-5 py-4 backdrop-blur shadow-lg shadow-slate-900/10">
              <p class="text-xs uppercase tracking-[0.2em] text-slate-200">Summary</p>
              <p class="mt-2 text-lg font-semibold">{{ checkIns.length + checkOuts.length }} aktivitas</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="relative z-10 mx-auto -mt-8 max-w-7xl px-4 pb-10 sm:px-6">
      <div class="mb-8 overflow-hidden rounded-[2rem] border border-white/70 bg-white/92 shadow-2xl shadow-slate-200/60 backdrop-blur">
        <div class="border-b border-slate-100 bg-gradient-to-r from-slate-50 to-zinc-100 px-8 py-6">
          <p class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Report Filter</p>
          <h2 class="mt-2 text-2xl font-bold text-slate-900">Filter Laporan Harian</h2>
        </div>

        <div class="p-6">
          <div class="grid items-end gap-4 md:grid-cols-3">
            <div>
              <label class="mb-2 block text-sm font-semibold text-slate-700">Tanggal</label>
              <input
                v-model="selectedDate"
                type="date"
                class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-700 shadow-sm transition focus:outline-none focus:ring-2 focus:ring-slate-400"
              >
            </div>

            <div>
              <button
                @click="loadReports"
                class="w-full rounded-2xl bg-gradient-to-r from-[#4b5563] via-[#6b7280] to-[#9ca3af] py-3 text-sm font-semibold text-white shadow-lg shadow-slate-300 transition duration-300 hover:-translate-y-0.5 hover:shadow-xl"
              >
                Tampilkan
              </button>
            </div>

            <div>
              <button
                @click="downloadReport"
                class="w-full rounded-2xl bg-gradient-to-r from-emerald-500 to-emerald-600 py-3 text-sm font-semibold text-white shadow-lg shadow-emerald-200 transition duration-300 hover:-translate-y-0.5 hover:shadow-xl"
              >
                Download Excel
              </button>
            </div>
          </div>
        </div>
      </div>

      <div class="mb-8 grid gap-6 md:grid-cols-2">
        <div class="rounded-[1.75rem] border border-white/70 bg-white/90 p-6 shadow-xl shadow-slate-200/60 backdrop-blur transition duration-300 hover:-translate-y-1 hover:shadow-2xl">
          <p class="text-sm font-medium text-slate-500">{{ checkInCardTitle }}</p>
          <p class="mt-2 text-3xl font-bold text-emerald-600">{{ checkIns.length }}</p>
        </div>

        <div class="rounded-[1.75rem] border border-white/70 bg-white/90 p-6 shadow-xl shadow-slate-200/60 backdrop-blur transition duration-300 hover:-translate-y-1 hover:shadow-2xl">
          <p class="text-sm font-medium text-slate-500">{{ checkOutCardTitle }}</p>
          <p class="mt-2 text-3xl font-bold text-amber-600">{{ checkOuts.length }}</p>
        </div>
      </div>

      <div class="mb-8 overflow-hidden rounded-[2rem] border border-white/70 bg-white/92 shadow-2xl shadow-slate-200/60 backdrop-blur">
        <div class="border-b border-slate-100 bg-gradient-to-r from-emerald-50 to-white px-8 py-6">
          <p class="text-xs font-semibold uppercase tracking-[0.25em] text-emerald-600">Check-In Report</p>
          <h2 class="mt-2 text-2xl font-bold text-slate-900">Daftar Check-in</h2>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-slate-50/80">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">No. Booking</th>
                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Nama Tamu</th>
                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Kamar</th>
                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Check-in</th>
                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Status</th>
              </tr>
            </thead>

            <tbody class="divide-y divide-slate-100">
              <tr v-for="b in paginatedCheckIns" :key="b.id" class="transition hover:bg-slate-50/70">
                <td class="px-6 py-4 text-sm font-semibold text-slate-900">{{ b.booking_number }}</td>
                <td class="px-6 py-4 text-sm text-slate-600">{{ b.guest?.full_name }}</td>
                <td class="px-6 py-4 text-sm text-slate-600">{{ b.booking_details?.[0]?.room?.room_number || '-' }}</td>
                <td class="px-6 py-4 text-sm text-slate-600">{{ formatDate(b.check_in_date) }}</td>
                <td class="px-6 py-4">
                  <span :class="getStatusClass(b.booking_status)" class="rounded-full px-3 py-1.5 text-xs font-semibold">
                    {{ getStatusText(b.booking_status) }}
                  </span>
                </td>
              </tr>

              <tr v-if="checkIns.length === 0">
                <td colspan="5" class="px-6 py-8 text-center text-slate-500">Tidak ada check-in pada tanggal ini</td>
              </tr>
            </tbody>
          </table>
        </div>
        <AppPagination
          v-if="checkInTotalPages > 1"
          :model-value="checkInPage"
          :total-pages="checkInTotalPages"
          :from="checkInDisplayFrom"
          :to="checkInDisplayTo"
          :total="checkIns.length"
          @update:model-value="setCheckInPage"
        />
      </div>

      <div class="overflow-hidden rounded-[2rem] border border-white/70 bg-white/92 shadow-2xl shadow-slate-200/60 backdrop-blur">
        <div class="border-b border-slate-100 bg-gradient-to-r from-amber-50 to-white px-8 py-6">
          <p class="text-xs font-semibold uppercase tracking-[0.25em] text-amber-600">Check-Out Report</p>
          <h2 class="mt-2 text-2xl font-bold text-slate-900">Daftar Check-out</h2>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-slate-50/80">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">No. Booking</th>
                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Nama Tamu</th>
                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Kamar</th>
                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Check-out</th>
                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Status</th>
              </tr>
            </thead>

            <tbody class="divide-y divide-slate-100">
              <tr v-for="b in paginatedCheckOuts" :key="b.id" class="transition hover:bg-slate-50/70">
                <td class="px-6 py-4 text-sm font-semibold text-slate-900">{{ b.booking_number }}</td>
                <td class="px-6 py-4 text-sm text-slate-600">{{ b.guest?.full_name }}</td>
                <td class="px-6 py-4 text-sm text-slate-600">{{ b.booking_details?.[0]?.room?.room_number || '-' }}</td>
                <td class="px-6 py-4 text-sm text-slate-600">{{ formatDate(getCheckOutDisplayDate(b)) }}</td>
                <td class="px-6 py-4">
                  <span :class="getStatusClass(b.booking_status)" class="rounded-full px-3 py-1.5 text-xs font-semibold">
                    {{ getStatusText(b.booking_status) }}
                  </span>
                </td>
              </tr>

              <tr v-if="checkOuts.length === 0">
                <td colspan="5" class="px-6 py-8 text-center text-slate-500">Tidak ada check-out pada tanggal ini</td>
              </tr>
            </tbody>
          </table>
        </div>
        <AppPagination
          v-if="checkOutTotalPages > 1"
          :model-value="checkOutPage"
          :total-pages="checkOutTotalPages"
          :from="checkOutDisplayFrom"
          :to="checkOutDisplayTo"
          :total="checkOuts.length"
          @update:model-value="setCheckOutPage"
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

const toLocalDateString = (value = new Date()) => {
  const date = new Date(value)
  if (Number.isNaN(date.getTime())) return ''
  const year = date.getFullYear()
  const month = String(date.getMonth() + 1).padStart(2, '0')
  const day = String(date.getDate()).padStart(2, '0')
  return `${year}-${month}-${day}`
}

const selectedDate = ref(toLocalDateString())
const checkIns = ref([])
const checkOuts = ref([])
const checkInPage = ref(1)
const checkOutPage = ref(1)
const itemsPerPage = 6
const todayLocal = computed(() => toLocalDateString())
const isTodaySelected = computed(() => selectedDate.value === todayLocal.value)
const checkInCardTitle = computed(() => isTodaySelected.value ? 'Check-in Hari Ini' : 'Check-in (Tanggal Dipilih)')
const checkOutCardTitle = computed(() => isTodaySelected.value ? 'Check-out Hari Ini' : 'Check-out (Tanggal Dipilih)')

const checkInTotalPages = computed(() => Math.max(1, Math.ceil(checkIns.value.length / itemsPerPage)))
const checkOutTotalPages = computed(() => Math.max(1, Math.ceil(checkOuts.value.length / itemsPerPage)))
const paginatedCheckIns = computed(() => {
  const start = (checkInPage.value - 1) * itemsPerPage
  return checkIns.value.slice(start, start + itemsPerPage)
})
const paginatedCheckOuts = computed(() => {
  const start = (checkOutPage.value - 1) * itemsPerPage
  return checkOuts.value.slice(start, start + itemsPerPage)
})
const checkInDisplayFrom = computed(() => (checkIns.value.length ? ((checkInPage.value - 1) * itemsPerPage) + 1 : 0))
const checkInDisplayTo = computed(() => Math.min(checkInPage.value * itemsPerPage, checkIns.value.length))
const checkOutDisplayFrom = computed(() => (checkOuts.value.length ? ((checkOutPage.value - 1) * itemsPerPage) + 1 : 0))
const checkOutDisplayTo = computed(() => Math.min(checkOutPage.value * itemsPerPage, checkOuts.value.length))
const setCheckInPage = (page) => { checkInPage.value = page }
const setCheckOutPage = (page) => { checkOutPage.value = page }
watch(checkInTotalPages, (value) => {
  if (checkInPage.value > value) checkInPage.value = value
})
watch(checkOutTotalPages, (value) => {
  if (checkOutPage.value > value) checkOutPage.value = value
})

const formatDate = (d) => d ? new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : '-'
const getCheckOutDisplayDate = (booking) => booking?.actual_check_out_at || booking?.check_out_date || null
const toTimestamp = (value) => {
  const date = new Date(value || '')
  const time = date.getTime()
  return Number.isNaN(time) ? 0 : time
}

const sortNewestFirst = (items, dateGetter) => {
  return [...items].sort((a, b) => {
    const primaryDiff = toTimestamp(dateGetter(b)) - toTimestamp(dateGetter(a))
    if (primaryDiff !== 0) return primaryDiff

    const createdDiff = toTimestamp(b?.created_at) - toTimestamp(a?.created_at)
    if (createdDiff !== 0) return createdDiff

    return Number(b?.id || 0) - Number(a?.id || 0)
  })
}

const getStatusClass = (s) => ({
  confirmed: 'bg-blue-100 text-blue-800',
  checked_in: 'bg-green-100 text-green-800',
  checked_out: 'bg-gray-100 text-gray-800',
  cancelled: 'bg-red-100 text-red-800'
}[s] || 'bg-gray-100 text-gray-800')

const getStatusText = (s) => ({
  confirmed: 'Dikonfirmasi',
  checked_in: 'Check-in',
  checked_out: 'Selesai',
  cancelled: 'Dibatalkan'
}[s] || s)

const handleLogout = async () => {
  await authStore.logout()
  await navigateTo('/login')
}

const loadReports = async () => {
  const [ciResult, coResult] = await Promise.all([
    receptionistStore.getCheckInsReport(selectedDate.value, authStore.token),
    receptionistStore.getCheckOutsReport(selectedDate.value, authStore.token)
  ])

  if (ciResult.success) {
    const rows = ciResult.data.bookings || ciResult.data.check_ins || []
    checkIns.value = sortNewestFirst(rows, (item) => item?.check_in_date)
  }

  if (coResult.success) {
    const rows = coResult.data.bookings || coResult.data.check_outs || []
    checkOuts.value = sortNewestFirst(rows, (item) => item?.actual_check_out_at || item?.check_out_date)
  }
  checkInPage.value = 1
  checkOutPage.value = 1
}

const downloadReport = async () => {
  try {
    const API_BASE = 'http://127.0.0.1:8000/api'
    const response = await $fetch(`${API_BASE}/receptionist/reports/export/excel?date=${selectedDate.value}&type=both`, {
      headers: { Authorization: `Bearer ${authStore.token}` }
    })

    if (response.success && response.data.records?.length) {
      const csv = [
        Object.keys(response.data.records[0]).join(','),
        ...response.data.records.map(r => Object.values(r).join(','))
      ].join('\n')

      const blob = new Blob([csv], { type: 'text/csv' })
      const url = URL.createObjectURL(blob)
      const a = document.createElement('a')
      a.href = url
      a.download = `laporan-harian-${selectedDate.value}.csv`
      a.click()
    }
  } catch (e) {
    alert('Gagal mengunduh laporan')
  }
}

onMounted(() => loadReports())
</script>
