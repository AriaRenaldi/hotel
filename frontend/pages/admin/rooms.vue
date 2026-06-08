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
          <NuxtLink to="/admin/rooms" class="rounded-full bg-slate-900 px-4 py-2 font-semibold text-white shadow-md">Rooms</NuxtLink>
          <NuxtLink to="/admin/room-types" class="rounded-full px-4 py-2 transition hover:bg-amber-50 hover:text-slate-900">Room Types</NuxtLink>
          <NuxtLink to="/admin/facilities" class="rounded-full px-4 py-2 transition hover:bg-amber-50 hover:text-slate-900">Facilities</NuxtLink>
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
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-100">Rooms Overview</p>
            <h1 class="mt-4 text-4xl font-bold">Manajemen Kamar</h1>
            <p class="mt-4 max-w-2xl text-lg text-amber-50">Kelola data kamar hotel, status operasional, dan konfigurasi unit dalam tampilan emas yang konsisten.</p>
          </div>
          <div class="grid gap-4 sm:grid-cols-2">
            <div class="rounded-3xl border border-white/15 bg-white/10 px-5 py-4 backdrop-blur">
              <p class="text-xs uppercase tracking-[0.2em] text-amber-100">Rooms</p>
              <p class="mt-2 text-lg font-semibold">{{ rooms.length }} unit</p>
            </div>
            <div class="rounded-3xl border border-white/15 bg-white/10 px-5 py-4 backdrop-blur">
              <p class="text-xs uppercase tracking-[0.2em] text-amber-100">Status</p>
              <p class="mt-2 text-lg font-semibold">Live management</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="relative z-10 mx-auto -mt-8 max-w-7xl px-4 pb-12 sm:px-6">
      <div class="mb-6 grid gap-4 md:grid-cols-4">
        <div class="rounded-[1.75rem] border border-white/70 bg-white/90 p-5 shadow-xl shadow-amber-100/60 backdrop-blur transition duration-300 hover:-translate-y-1 hover:shadow-2xl">
          <p class="text-sm font-medium text-slate-500">Total Kamar</p>
          <p class="mt-2 text-3xl font-bold text-slate-900">{{ roomOverviewLoaded ? roomOverview.total_rooms : rooms.length }}</p>
        </div>
        <div class="rounded-[1.75rem] border border-white/70 bg-white/90 p-5 shadow-xl shadow-amber-100/60 backdrop-blur transition duration-300 hover:-translate-y-1 hover:shadow-2xl">
          <p class="text-sm font-medium text-slate-500">Tersedia</p>
          <p class="mt-2 text-3xl font-bold text-emerald-600">{{ roomOverviewLoaded ? roomOverview.available_rooms : rooms.filter(r => r.status === 'available').length }}</p>
        </div>
        <div class="rounded-[1.75rem] border border-white/70 bg-white/90 p-5 shadow-xl shadow-amber-100/60 backdrop-blur transition duration-300 hover:-translate-y-1 hover:shadow-2xl">
          <p class="text-sm font-medium text-slate-500">Terisi</p>
          <p class="mt-2 text-3xl font-bold text-yellow-600">{{ roomOverviewLoaded ? roomOverview.occupied_rooms : rooms.filter(r => r.status === 'occupied').length }}</p>
        </div>
        <div class="rounded-[1.75rem] border border-white/70 bg-white/90 p-5 shadow-xl shadow-amber-100/60 backdrop-blur transition duration-300 hover:-translate-y-1 hover:shadow-2xl">
          <p class="text-sm font-medium text-slate-500">Maintenance</p>
          <p class="mt-2 text-3xl font-bold text-rose-600">{{ roomOverviewLoaded ? roomOverview.maintenance_rooms : rooms.filter(r => r.status === 'maintenance').length }}</p>
        </div>
      </div>

      <div class="mb-8 flex items-center justify-between rounded-[2rem] border border-amber-100 bg-white px-6 py-5 shadow-xl shadow-amber-100/50">
        <div>
          <p class="text-xs font-semibold uppercase tracking-[0.25em] text-amber-700">Room Actions</p>
          <h2 class="mt-2 text-2xl font-bold text-slate-900">Kelola daftar kamar hotel</h2>
        </div>
        <button @click="showModal = true" class="rounded-2xl bg-gradient-to-r from-[#6b4f1d] via-[#9c7a2b] to-[#d4af37] px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-amber-200 transition duration-300 hover:-translate-y-0.5 hover:shadow-xl">+ Tambah Kamar</button>
      </div>

      <div class="rounded-[2rem] border border-amber-100/70 p-2 shadow-xl shadow-amber-100/40">
        <div v-if="loading" class="py-12 text-center">
          <div class="inline-block h-8 w-8 animate-spin rounded-full border-b-2 border-amber-600"></div>
        </div>
        <div v-else class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
          <div
            v-for="room in paginatedRooms"
            :key="room.id"
            class="group relative overflow-hidden rounded-[2rem] border border-amber-200/80 bg-gradient-to-br from-[#fffdf7] via-[#fff7df] to-[#f9edd0] p-6 shadow-[0_14px_35px_rgba(180,136,28,0.22)] ring-1 ring-amber-100/70 transition duration-300 hover:-translate-y-1 hover:shadow-[0_20px_45px_rgba(180,136,28,0.30)]"
          >
            <div class="pointer-events-none absolute inset-0">
              <div class="absolute -right-10 -top-10 h-32 w-32 rounded-full bg-amber-200/40 blur-2xl"></div>
              <div class="absolute -left-8 bottom-0 h-24 w-24 rounded-full bg-yellow-100/60 blur-2xl"></div>
              <div class="absolute inset-x-6 top-0 h-px bg-gradient-to-r from-transparent via-amber-300 to-transparent"></div>
            </div>

            <div class="relative z-10 mb-5 flex items-start justify-between gap-4">
              <div class="h-32 w-40 overflow-hidden rounded-2xl border border-amber-200 bg-amber-50">
                <img :src="room.image_url || room.room_type?.image_url || 'https://via.placeholder.com/320x240?text=No+Photo'" :alt="`Kamar ${room.room_number}`" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
              </div>
              <span :class="room.is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800'" class="rounded-full px-3 py-1 text-xs font-semibold">{{ room.is_active ? 'Aktif' : 'Nonaktif' }}</span>
            </div>

            <div class="relative z-10">
              <p class="inline-flex rounded-full bg-amber-100/80 px-3 py-1 text-[10px] font-semibold uppercase tracking-[0.2em] text-amber-800">Golden Suite</p>
              <h3 class="mt-3 text-2xl font-black tracking-tight text-slate-900">Kamar {{ room.room_number }}</h3>

              <div class="mt-3 grid gap-2 rounded-2xl border border-amber-200/80 bg-white/70 p-3 text-sm text-slate-700">
                <div class="flex items-center justify-between">
                  <span class="font-medium text-slate-500">Tipe</span>
                  <span class="font-semibold text-slate-900">{{ room.room_type?.type_name || '-' }}</span>
                </div>
                <div class="h-px bg-gradient-to-r from-transparent via-amber-200 to-transparent"></div>
                <div class="flex items-center justify-between">
                  <span class="font-medium text-slate-500">Lantai</span>
                  <span class="font-semibold text-slate-900">{{ room.floor || '-' }}</span>
                </div>
              </div>
            </div>

            <div class="relative z-10 mt-4">
              <label class="mb-2 block text-[11px] font-semibold uppercase tracking-[0.25em] text-amber-700">Status Kamar</label>
              <select
                :value="room.status"
                :disabled="room.status === 'occupied'"
                :title="room.status === 'occupied' ? 'Kamar terisi tidak dapat diubah statusnya oleh admin' : ''"
                @change="updateStatus(room, $event.target.value)"
                :class="getStatusClass(room.status)"
                class="rounded-full border-0 px-4 py-2 text-xs font-semibold shadow-sm disabled:cursor-not-allowed disabled:opacity-70"
              >
                <option value="available">Tersedia</option>
                <option value="occupied">Terisi</option>
                <option value="maintenance">Maintenance</option>
              </select>
            </div>

            <div class="relative z-10 mt-6 flex gap-3">
              <button @click="editRoom(room)" class="rounded-xl bg-gradient-to-r from-amber-100 to-amber-200 px-4 py-2 text-sm font-semibold text-amber-800 transition hover:brightness-105">Edit</button>
              <button @click="deleteRoom(room.id)" class="rounded-xl bg-gradient-to-r from-rose-100 to-rose-200 px-4 py-2 text-sm font-semibold text-rose-700 transition hover:brightness-105">Hapus</button>
            </div>
          </div>
        </div>

        <AppPagination
          v-if="!loading && rooms.length > 0 && totalPages > 1"
          class="mt-6"
          theme="amber"
          :model-value="currentPage"
          :total-pages="totalPages"
          :from="paginationStart"
          :to="paginationEnd"
          :total="rooms.length"
          @update:model-value="setCurrentPage"
        />
      </div>
    </section>

    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/55 p-4 backdrop-blur-sm">
      <div class="w-full max-w-md rounded-[2rem] border border-white/70 bg-white/95 p-6 shadow-2xl shadow-amber-200/50">
        <h2 class="text-2xl font-bold text-slate-900">{{ editingRoom ? 'Edit Kamar' : 'Tambah Kamar' }}</h2>
        <p class="mt-2 text-sm text-slate-500">Lengkapi informasi kamar agar inventaris hotel tetap tertata rapi.</p>
        <form @submit.prevent="saveRoom" class="mt-6">
          <div class="space-y-4">
            <div>
              <label class="mb-2 block text-sm font-semibold text-slate-700">No. Kamar</label>
              <input v-model="form.room_number" type="text" required class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500">
            </div>
            <div>
              <label class="mb-2 block text-sm font-semibold text-slate-700">Tipe Kamar</label>
              <select v-model="form.room_type_id" required class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500">
                <option value="">Pilih Tipe</option>
                <option v-for="rt in roomTypes" :key="rt.id" :value="rt.id">{{ rt.type_name }}</option>
              </select>
            </div>
            <div>
              <label class="mb-2 block text-sm font-semibold text-slate-700">Lantai</label>
              <input v-model.number="form.floor" type="number" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500">
            </div>
            <div>
              <label class="mb-2 block text-sm font-semibold text-slate-700">Status</label>
              <select v-model="form.status" required class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500">
                <option value="available">Tersedia</option>
                <option value="occupied">Terisi</option>
                <option value="maintenance">Maintenance</option>
              </select>
            </div>
            <div>
              <label class="mb-2 block text-sm font-semibold text-slate-700">Foto Kamar</label>
              <input type="file" accept="image/*" @change="handleImageChange" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 file:mr-3 file:rounded-xl file:border-0 file:bg-amber-100 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-amber-700">
              <p class="mt-2 text-xs text-slate-500">Format: JPG, PNG, WEBP. Maksimal 2MB.</p>
              <div v-if="previewImage" class="mt-3 h-24 w-24 overflow-hidden rounded-2xl border border-amber-100 bg-amber-50/60">
                <img :src="previewImage" alt="Preview foto kamar" class="h-full w-full object-cover">
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
import { useRoomStore } from '~/stores/room'

definePageMeta({ middleware: 'admin' })

const authStore = useAuthStore()

const handleLogout = async () => {
  await authStore.logout()
  await navigateTo('/login')
}
const roomStore = useRoomStore()

const rooms = ref([])
const roomTypes = ref([])
const loading = ref(true)
const showModal = ref(false)
const editingRoom = ref(null)
const previewImage = ref('')
const roomOverviewLoaded = ref(false)
const currentPage = ref(1)
const itemsPerPage = 6
const roomOverview = reactive({
  total_rooms: 0,
  available_rooms: 0,
  occupied_rooms: 0,
  maintenance_rooms: 0,
})

const totalPages = computed(() => Math.max(1, Math.ceil(rooms.value.length / itemsPerPage)))
const paginatedRooms = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  return rooms.value.slice(start, start + itemsPerPage)
})
const paginationStart = computed(() => ((currentPage.value - 1) * itemsPerPage) + 1)
const paginationEnd = computed(() => Math.min(currentPage.value * itemsPerPage, rooms.value.length))

const setCurrentPage = (page) => { currentPage.value = page }

const form = reactive({
  room_number: '',
  room_type_id: '',
  floor: null,
  status: 'available',
  image: null
})

const getStatusClass = (status) => ({
  available: 'bg-emerald-100 text-emerald-800',
  occupied: 'bg-yellow-100 text-yellow-800',
  maintenance: 'bg-rose-100 text-rose-800'
}[status] || 'bg-gray-100 text-gray-800')

const loadRooms = async () => {
  const result = await roomStore.getAllRooms(authStore.token)
  if (result.success) {
    rooms.value = result.data
    if (currentPage.value > totalPages.value) {
      currentPage.value = totalPages.value
    }
    await loadRoomOverview()
  }
}

const loadRoomTypes = async () => {
  const result = await roomStore.getAllRoomTypes(authStore.token)
  if (result.success) roomTypes.value = result.data
}

const loadRoomOverview = async () => {
  try {
    const response = await $fetch('http://127.0.0.1:8000/api/admin/dashboard', {
      headers: {
        Authorization: `Bearer ${authStore.token}`,
      },
    })

    if (response?.success) {
      roomOverview.total_rooms = Number(response.data?.total_rooms || 0)
      roomOverview.available_rooms = Number(response.data?.available_rooms || 0)
      roomOverview.occupied_rooms = Number(response.data?.occupied_rooms || 0)
      roomOverview.maintenance_rooms = Number(response.data?.maintenance_rooms || 0)
      roomOverviewLoaded.value = true
    }
  } catch {
    // fallback to local room status counters when dashboard endpoint fails
  }
}

const editRoom = (room) => {
  editingRoom.value = room
  form.room_number = room.room_number
  form.room_type_id = room.room_type_id
  form.floor = room.floor
  form.status = room.status
  form.image = null
  previewImage.value = room.image_url || room.room_type?.image_url || ''
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  editingRoom.value = null
  form.room_number = ''
  form.room_type_id = ''
  form.floor = null
  form.status = 'available'
  form.image = null
  previewImage.value = ''
}

const handleImageChange = (event) => {
  const file = event.target?.files?.[0] || null
  form.image = file
  previewImage.value = file ? URL.createObjectURL(file) : (editingRoom.value?.image_url || editingRoom.value?.room_type?.image_url || '')
}

const saveRoom = async () => {
  const payload = new FormData()
  payload.append('room_number', String(form.room_number))
  payload.append('room_type_id', String(form.room_type_id))
  if (form.floor !== null && form.floor !== undefined && form.floor !== '') payload.append('floor', String(form.floor))
  payload.append('status', String(form.status))
  if (form.image) payload.append('image', form.image)

  const result = editingRoom.value
    ? await roomStore.updateRoom(editingRoom.value.id, payload, authStore.token)
    : await roomStore.createRoom(payload, authStore.token)

  if (result.success) {
    alert(editingRoom.value ? 'Kamar berhasil diupdate' : 'Kamar berhasil ditambahkan')
    closeModal()
    loadRooms()
  } else {
    alert(result.message || 'Gagal menyimpan kamar')
  }
}

const updateStatus = async (room, status) => {
  if (room?.status === 'occupied' && status !== 'occupied') {
    alert('Status kamar tidak bisa diubah karena kamar sedang terisi.')
    return
  }

  const result = await roomStore.updateRoom(room.id, { status }, authStore.token)
  if (result.success) {
    loadRooms()
  } else {
    alert(result.message || 'Gagal mengubah status kamar')
  }
}

const deleteRoom = async (id) => {
  if (confirm('Yakin ingin menghapus kamar ini?')) {
    const result = await roomStore.deleteRoom(id, authStore.token)
    if (result.success) {
      alert('Kamar berhasil dihapus')
      loadRooms()
    } else {
      alert(result.message || 'Gagal menghapus kamar')
    }
  }
}

onMounted(async () => {
  await Promise.all([loadRooms(), loadRoomTypes()])
  loading.value = false
})
</script>
