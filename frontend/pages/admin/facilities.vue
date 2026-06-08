<template>
  <div class="min-h-screen bg-gradient-to-br from-[#fff8e7] via-[#f8f1dc] to-[#efe2b8] text-slate-900">
    <nav class="sticky top-0 z-50 border-b border-white/70 bg-white/85 backdrop-blur-xl shadow-sm">
      <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-6">
        <NuxtLink to="/admin/dashboard" class="flex items-center gap-3">
          <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-r from-[#6b4f1d] via-[#9c7a2b] to-[#d4af37] text-white shadow-lg">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
          </div>
          <div>
            <p class="text-lg font-bold text-slate-900">HotelKu</p>
            <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Admin Panel</p>
          </div>
        </NuxtLink>
        <div class="flex flex-wrap items-center gap-2 rounded-full border border-amber-100/80 bg-white/80 p-2 text-sm font-medium text-slate-600 shadow-lg shadow-amber-100/40 backdrop-blur">
          <NuxtLink to="/admin/dashboard" class="rounded-full px-4 py-2 transition hover:bg-amber-50 hover:text-slate-900">Dashboard</NuxtLink>
          <NuxtLink to="/admin/rooms" class="rounded-full px-4 py-2 transition hover:bg-amber-50 hover:text-slate-900">Rooms</NuxtLink>
          <NuxtLink to="/admin/room-types" class="rounded-full px-4 py-2 transition hover:bg-amber-50 hover:text-slate-900">Room Types</NuxtLink>
          <NuxtLink to="/admin/facilities" class="rounded-full bg-slate-900 px-4 py-2 font-semibold text-white shadow-md">Facilities</NuxtLink>
          <NuxtLink to="/admin/users" class="rounded-full px-4 py-2 transition hover:bg-amber-50 hover:text-slate-900">Users</NuxtLink>
          <NuxtLink to="/admin/reports" class="rounded-full px-4 py-2 transition hover:bg-amber-50 hover:text-slate-900">Reports</NuxtLink>
          <NuxtLink to="/admin/penalty-reports" class="rounded-full px-4 py-2 transition hover:bg-amber-50 hover:text-slate-900">Penalty Reports</NuxtLink>
          <NuxtLink to="/profile" class="rounded-full px-4 py-2 transition hover:bg-amber-50 hover:text-slate-900">Profil</NuxtLink>       
          <button @click="handleLogout" class="rounded-full bg-rose-50 px-4 py-2 font-semibold text-rose-600 transition hover:bg-rose-100">Logout</button>
        </div>
      </div>
    </nav>

    <section class="relative overflow-hidden bg-gradient-to-br from-[#6b4f1d] via-[#9c7a2b] to-[#d4af37] text-white">
      <div class="absolute inset-0 opacity-20">
        <div class="absolute left-8 top-8 h-48 w-48 rounded-full bg-white/15 blur-3xl"></div>
        <div class="absolute right-12 bottom-0 h-64 w-64 rounded-full bg-amber-100/20 blur-3xl"></div>
      </div>
      <div class="relative mx-auto max-w-7xl px-4 py-16 sm:px-6">
        <div class="grid items-center gap-8 lg:grid-cols-[1.2fr_0.8fr]">
          <div>
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-100">Facilities Overview</p>
            <h1 class="mt-4 text-4xl font-bold">Manajemen Fasilitas</h1>
            <p class="mt-4 max-w-2xl text-lg text-amber-50">Kelola fasilitas hotel dengan tampilan emas yang lebih mewah, hangat, dan konsisten dengan seluruh area admin.</p>
          </div>
          <div class="grid gap-4 sm:grid-cols-2">
            <div class="rounded-3xl border border-white/15 bg-white/10 px-5 py-4 backdrop-blur">
              <p class="text-xs uppercase tracking-[0.2em] text-amber-100">Total</p>
              <p class="mt-2 text-lg font-semibold">{{ facilities.length }} fasilitas</p>
            </div>
            <div class="rounded-3xl border border-white/15 bg-white/10 px-5 py-4 backdrop-blur">
              <p class="text-xs uppercase tracking-[0.2em] text-amber-100">Control</p>
              <p class="mt-2 text-lg font-semibold">Easy update</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="relative z-10 mx-auto -mt-8 max-w-7xl px-4 pb-12 sm:px-6">
      <div class="mb-8 flex items-center justify-between rounded-[2rem] border border-amber-100 bg-white px-6 py-5 shadow-xl shadow-amber-100/50">
        <div>
          <p class="text-xs font-semibold uppercase tracking-[0.25em] text-amber-700">Facility Actions</p>
          <h2 class="mt-2 text-2xl font-bold text-slate-900">Kelola daftar fasilitas hotel</h2>
        </div>
        <button @click="showModal = true" class="rounded-2xl bg-gradient-to-r from-[#6b4f1d] via-[#9c7a2b] to-[#d4af37] px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-amber-200 transition duration-300 hover:-translate-y-0.5 hover:shadow-xl">+ Tambah Fasilitas</button>
      </div>

      <div class="rounded-[2rem] border border-amber-100/70 p-2 shadow-xl shadow-amber-100/40">
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
          <div
            v-for="facility in paginatedFacilities"
            :key="facility.id"
            class="group relative overflow-hidden rounded-[2rem] border border-amber-200/80 bg-gradient-to-br from-[#fffdf7] via-[#fff7df] to-[#f9edd0] p-6 shadow-[0_14px_35px_rgba(180,136,28,0.22)] ring-1 ring-amber-100/70 transition duration-300 hover:-translate-y-1 hover:shadow-[0_20px_45px_rgba(180,136,28,0.30)]"
          >
          <div class="pointer-events-none absolute inset-0">
            <div class="absolute -right-10 -top-10 h-32 w-32 rounded-full bg-amber-200/40 blur-2xl"></div>
            <div class="absolute -left-8 bottom-0 h-24 w-24 rounded-full bg-yellow-100/60 blur-2xl"></div>
            <div class="absolute inset-x-6 top-0 h-px bg-gradient-to-r from-transparent via-amber-300 to-transparent"></div>
          </div>
          <div class="relative z-10 mb-5 flex items-start justify-between gap-4">
            <div class="h-32 w-40 overflow-hidden rounded-2xl border border-amber-200 bg-amber-50">
              <img :src="facility.image_url || 'https://via.placeholder.com/320x240?text=No+Photo'" :alt="facility.facility_name" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
            </div>
            <span :class="facility.is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800'" class="rounded-full px-3 py-1 text-xs font-semibold">{{ facility.is_active ? 'Aktif' : 'Nonaktif' }}</span>
          </div>
          <div class="relative z-10">
            <p class="inline-flex rounded-full bg-amber-100/80 px-3 py-1 text-[10px] font-semibold uppercase tracking-[0.2em] text-amber-800">Golden Accent</p>
            <h3 class="mt-3 text-2xl font-black tracking-tight text-slate-900">{{ facility.facility_name }}</h3>
            <div class="mt-3 rounded-2xl border border-amber-200/80 bg-white/70 p-3">
              <p class="line-clamp-4 text-sm leading-relaxed text-slate-700">{{ facility.description || 'Tidak ada deskripsi' }}</p>
            </div>
          </div>
          <div class="relative z-10 mt-6 flex gap-3">
            <button @click="editFacility(facility)" class="rounded-xl bg-gradient-to-r from-amber-100 to-amber-200 px-4 py-2 text-sm font-semibold text-amber-800 transition hover:brightness-105">Edit</button>
            <button @click="deleteFacility(facility.id)" class="rounded-xl bg-gradient-to-r from-rose-100 to-rose-200 px-4 py-2 text-sm font-semibold text-rose-700 transition hover:brightness-105">Hapus</button>
          </div>
        </div>
        </div>
        <AppPagination
          v-if="totalPages > 1"
          class="mt-6"
          theme="amber"
          :model-value="currentPage"
          :total-pages="totalPages"
          :from="displayFrom"
          :to="displayTo"
          :total="facilities.length"
          @update:model-value="setCurrentPage"
        />
      </div>
    </section>

    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/55 p-4 backdrop-blur-sm">
      <div class="w-full max-w-md rounded-[2rem] border border-white/70 bg-white/95 p-6 shadow-2xl shadow-amber-200/50">
        <h2 class="text-2xl font-bold text-slate-900">{{ editing ? 'Edit Fasilitas' : 'Tambah Fasilitas' }}</h2>
        <p class="mt-2 text-sm text-slate-500">Lengkapi informasi fasilitas untuk mempercantik katalog hotel.</p>
        <form @submit.prevent="saveFacility" class="mt-6">
          <div class="space-y-4">
            <div>
              <label class="mb-2 block text-sm font-semibold text-slate-700">Nama Fasilitas</label>
              <input v-model="form.facility_name" type="text" required class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500">
            </div>
            <div>
              <label class="mb-2 block text-sm font-semibold text-slate-700">Deskripsi</label>
              <textarea v-model="form.description" rows="3" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500"></textarea>
            </div>
            <div>
              <label class="mb-2 block text-sm font-semibold text-slate-700">Foto Fasilitas</label>
              <input type="file" accept="image/*" @change="handleImageChange" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 file:mr-3 file:rounded-xl file:border-0 file:bg-amber-100 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-amber-700">
              <p class="mt-2 text-xs text-slate-500">Format: JPG, PNG, WEBP. Maksimal 2MB.</p>
              <div v-if="previewImage" class="mt-3 h-24 w-24 overflow-hidden rounded-2xl border border-amber-100 bg-amber-50/60">
                <img :src="previewImage" alt="Preview foto fasilitas" class="h-full w-full object-cover">
              </div>
            </div>
          </div>
          <div class="mt-6 flex justify-end gap-3">
            <button type="button" @click="closeModal" class="rounded-2xl border border-slate-200 px-4 py-2.5 font-semibold text-slate-600 transition hover:bg-slate-50">Batal</button>
            <button type="submit" class="rounded-2xl bg-gradient-to-r from-[#6b4f1d] via-[#9c7a2b] to-[#d4af37] px-5 py-2.5 font-semibold text-white shadow-lg shadow-amber-200 transition hover:-translate-y-0.5">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useAuthStore } from '~/stores/auth'
import { useBookingStore } from '~/stores/booking'

const API_BASE = 'http://127.0.0.1:8000/api'

definePageMeta({ middleware: 'admin' })

const authStore = useAuthStore()

const handleLogout = async () => {
  await authStore.logout()
  await navigateTo('/login')
}
const bookingStore = useBookingStore()

const facilities = ref([])
const currentPage = ref(1)
const itemsPerPage = 6
const showModal = ref(false)
const editing = ref(null)
const previewImage = ref('')
const form = reactive({ facility_name: '', description: '', image: null })
const totalPages = computed(() => Math.max(1, Math.ceil(facilities.value.length / itemsPerPage)))
const paginatedFacilities = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  return facilities.value.slice(start, start + itemsPerPage)
})
const displayFrom = computed(() => (facilities.value.length ? ((currentPage.value - 1) * itemsPerPage) + 1 : 0))
const displayTo = computed(() => Math.min(currentPage.value * itemsPerPage, facilities.value.length))
const setCurrentPage = (page) => { currentPage.value = page }
watch(totalPages, (value) => {
  if (currentPage.value > value) currentPage.value = value
})

const loadFacilities = async () => {
  const result = await bookingStore.getFacilities()
  if (result.success) {
    facilities.value = result.data
    currentPage.value = 1
  }
}

const editFacility = (f) => {
  editing.value = f
  Object.assign(form, { facility_name: f.facility_name, description: f.description || '', image: null })
  previewImage.value = f.image_url || ''
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  editing.value = null
  Object.assign(form, { facility_name: '', description: '', image: null })
  previewImage.value = ''
}

const handleImageChange = (event) => {
  const file = event.target?.files?.[0] || null
  form.image = file
  previewImage.value = file ? URL.createObjectURL(file) : (editing.value?.image_url || '')
}

const saveFacility = async () => {
  try {
    const url = editing.value ? `${API_BASE}/admin/facilities/${editing.value.id}` : `${API_BASE}/admin/facilities`
    const payload = new FormData()
    payload.append('facility_name', String(form.facility_name))
    payload.append('description', String(form.description || ''))
    if (form.image) payload.append('image', form.image)
    if (editing.value) payload.append('_method', 'PUT')

    const response = await $fetch(url, {
      method: 'POST',
      headers: { Authorization: `Bearer ${authStore.token}` },
      body: payload
    })

    if (response.success) {
      alert(editing.value ? 'Fasilitas berhasil diupdate' : 'Fasilitas berhasil ditambahkan')
      closeModal()
      loadFacilities()
    }
  } catch (e) {
    alert('Gagal menyimpan fasilitas')
  }
}

const deleteFacility = async (id) => {
  if (confirm('Yakin ingin menghapus fasilitas ini?')) {
    try {
      const response = await $fetch(`${API_BASE}/admin/facilities/${id}`, { method: 'DELETE', headers: { Authorization: `Bearer ${authStore.token}` } })
      if (response.success) { alert('Berhasil dihapus'); loadFacilities() }
    } catch (e) { alert('Gagal menghapus') }
  }
}

onMounted(() => loadFacilities())
</script>
