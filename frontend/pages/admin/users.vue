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
          <NuxtLink to="/admin/facilities" class="rounded-full px-4 py-2 transition hover:bg-amber-50 hover:text-slate-900">Facilities</NuxtLink>
          <NuxtLink to="/admin/users" class="rounded-full bg-slate-900 px-4 py-2 font-semibold text-white shadow-md">Users</NuxtLink>
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
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-100">User Management</p>
            <h1 class="mt-4 text-4xl font-bold">Kelola User</h1>
            <p class="mt-4 max-w-2xl text-lg text-amber-50">Atur pengguna sistem, verifikasi akun, dan tambah resepsionis dengan tampilan emas yang lebih eksklusif.</p>
          </div>
          <div class="grid gap-4 sm:grid-cols-2">
            <div class="rounded-3xl border border-white/15 bg-white/10 px-5 py-4 backdrop-blur">
              <p class="text-xs uppercase tracking-[0.2em] text-amber-100">Users</p>
              <p class="mt-2 text-lg font-semibold">{{ users.length }} akun</p>
            </div>
            <div class="rounded-3xl border border-white/15 bg-white/10 px-5 py-4 backdrop-blur">
              <p class="text-xs uppercase tracking-[0.2em] text-amber-100">Control</p>
              <p class="mt-2 text-lg font-semibold">Approval ready</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="relative z-10 mx-auto -mt-8 max-w-7xl px-4 pb-12 sm:px-6">
      <div class="mb-6 flex items-center justify-between rounded-[2rem] border border-amber-100 bg-white px-6 py-5 shadow-xl shadow-amber-100/50">
        <div>
          <p class="text-xs font-semibold uppercase tracking-[0.25em] text-amber-700">User Actions</p>
          <h2 class="mt-2 text-2xl font-bold text-slate-900">Manajemen pengguna sistem</h2>
        </div>
        <button @click="showModal = true" class="rounded-2xl bg-gradient-to-r from-[#6b4f1d] via-[#9c7a2b] to-[#d4af37] px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-amber-200 transition duration-300 hover:-translate-y-0.5 hover:shadow-xl">+ Tambah Resepsionis</button>
      </div>

      <div class="mb-6 rounded-[2rem] border border-amber-100 bg-white p-6 shadow-xl shadow-amber-100/60">
        <div class="grid gap-4 md:grid-cols-3">
          <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">Role</label>
            <select v-model="filters.role" @change="loadUsers" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500">
              <option value="">Semua</option>
              <option value="guest">Tamu</option>
              <option value="receptionist">Resepsionis</option>
              <option value="admin">Admin</option>
            </select>
          </div>
          <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700">Cari</label>
            <input v-model="filters.search" @input="loadUsers" type="text" placeholder="Nama/email/username..." class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500">
          </div>
        </div>
      </div>

      <div class="overflow-hidden rounded-[2rem] border border-amber-100 bg-white shadow-2xl shadow-amber-100/70 ring-1 ring-amber-100">
        <div v-if="loading" class="py-12 text-center">
          <div class="inline-block h-8 w-8 animate-spin rounded-full border-b-2 border-amber-600"></div>
        </div>
        <div v-else class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gradient-to-r from-amber-50 to-[#fff8e7]">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Foto</th>
                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Nama</th>
                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Email</th>
                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Username</th>
                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Role</th>
                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Status</th>
                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-amber-100">
              <tr v-for="user in paginatedUsers" :key="user.id" class="transition hover:bg-amber-50/40">
                <td class="px-6 py-4">
                  <div class="h-12 w-12 overflow-hidden rounded-xl border border-amber-100 bg-amber-50">
                    <img v-if="user.photo_url" :src="user.photo_url" :alt="`Foto ${user.full_name}`" class="h-full w-full object-cover">
                    <div v-else class="flex h-full w-full items-center justify-center text-sm font-bold text-amber-700">
                      {{ (user.full_name || user.username || 'U').charAt(0).toUpperCase() }}
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 text-sm font-semibold text-slate-900">{{ user.full_name }}</td>
                <td class="px-6 py-4 text-sm text-slate-600">{{ user.email }}</td>
                <td class="px-6 py-4 text-sm text-slate-600">{{ user.username }}</td>
                <td class="px-6 py-4">
                  <span :class="getRoleClass(user.role)" class="rounded-full px-3 py-1 text-xs font-semibold">{{ getRoleText(user.role) }}</span>
                </td>
                <td class="px-6 py-4">
                  <span :class="user.is_verified ? 'bg-emerald-100 text-emerald-800' : 'bg-yellow-100 text-yellow-800'" class="rounded-full px-3 py-1 text-xs font-semibold">{{ user.is_verified ? 'Terverifikasi' : 'Belum Verifikasi' }}</span>
                </td>
                <td class="px-6 py-4 space-x-2">
                  <button @click="editUser(user)" class="text-sm font-semibold text-amber-700 transition hover:text-amber-800">Edit</button>
                  <button v-if="!user.is_verified" @click="approveUser(user.id)" class="text-sm font-semibold text-emerald-600 transition hover:text-emerald-700">Setujui</button>
                  <button v-if="user.role !== 'admin' && user.role !== 'guest'" @click="deleteUser(user.id)" class="text-sm font-semibold text-rose-600 transition hover:text-rose-700">Hapus</button>
                </td>
              </tr>
            </tbody>
          </table>
          <AppPagination
            v-if="users.length > 0 && totalPages > 1"
            theme="amber"
            :model-value="currentPage"
            :total-pages="totalPages"
            :from="displayFrom"
            :to="displayTo"
            :total="users.length"
            @update:model-value="setCurrentPage"
          />
        </div>
      </div>
    </section>

    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/55 p-4 backdrop-blur-sm">
      <div class="w-full max-w-md rounded-[2rem] border border-white/70 bg-white/95 p-6 shadow-2xl shadow-amber-200/50">
        <h2 class="text-2xl font-bold text-slate-900">{{ editing ? 'Edit User' : 'Tambah Resepsionis' }}</h2>
        <p class="mt-2 text-sm text-slate-500">Lengkapi data akun untuk menambah atau memperbarui pengguna sistem.</p>
        <form @submit.prevent="saveUser" class="mt-6">
          <div class="space-y-4">
            <div>
              <label class="mb-2 block text-sm font-semibold text-slate-700">Username</label>
              <input v-model="form.username" type="text" required class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500">
            </div>
            <div>
              <label class="mb-2 block text-sm font-semibold text-slate-700">Email</label>
              <input v-model="form.email" type="email" required class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500">
            </div>
            <div>
              <label class="mb-2 block text-sm font-semibold text-slate-700">Password {{ editing ? '(kosongkan jika tidak diubah)' : '' }}</label>
              <input v-model="form.password" type="password" :required="!editing" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500">
            </div>
            <div>
              <label class="mb-2 block text-sm font-semibold text-slate-700">Nama Lengkap</label>
              <input v-model="form.full_name" type="text" required class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500">
            </div>
            <div>
              <label class="mb-2 block text-sm font-semibold text-slate-700">No. Telepon</label>
              <input v-model="form.phone" type="text" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500">
            </div>
            <div>
              <label class="mb-2 block text-sm font-semibold text-slate-700">Alamat</label>
              <textarea v-model="form.address" rows="2" class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500"></textarea>
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
import { useAdminStore } from '~/stores/admin'

definePageMeta({ middleware: 'admin' })

const authStore = useAuthStore()

const handleLogout = async () => {
  await authStore.logout()
  await navigateTo('/login')
}
const adminStore = useAdminStore()

const users = ref([])
const loading = ref(true)
const showModal = ref(false)
const editing = ref(null)
const ITEMS_PER_PAGE = 6
const currentPage = ref(1)
const filters = reactive({ role: '', search: '' })
const form = reactive({ username: '', email: '', password: '', full_name: '', phone: '', address: '' })

const getRoleClass = (role) => ({ admin: 'bg-amber-100 text-amber-800', receptionist: 'bg-blue-100 text-blue-800', guest: 'bg-slate-100 text-slate-700' }[role] || 'bg-slate-100 text-slate-700')
const getRoleText = (role) => ({ admin: 'Admin', receptionist: 'Resepsionis', guest: 'Tamu' }[role] || role)

const totalPages = computed(() => {
  const pages = Math.ceil(users.value.length / ITEMS_PER_PAGE)
  return Math.max(1, pages)
})

const paginatedUsers = computed(() => {
  const start = (currentPage.value - 1) * ITEMS_PER_PAGE
  const end = start + ITEMS_PER_PAGE
  return users.value.slice(start, end)
})

const displayFrom = computed(() => {
  if (users.value.length === 0) return 0
  return (currentPage.value - 1) * ITEMS_PER_PAGE + 1
})

const displayTo = computed(() => {
  if (users.value.length === 0) return 0
  return Math.min(currentPage.value * ITEMS_PER_PAGE, users.value.length)
})

const setCurrentPage = (page) => { currentPage.value = page }

const loadUsers = async () => {
  const result = await adminStore.getAllUsers(authStore.token)
  if (result.success) {
    let data = result.data.data || result.data
    if (filters.role) data = data.filter(u => u.role === filters.role)
    if (filters.search) {
      const s = filters.search.toLowerCase()
      data = data.filter(u => u.full_name?.toLowerCase().includes(s) || u.email?.toLowerCase().includes(s) || u.username?.toLowerCase().includes(s))
    }
    users.value = data
    currentPage.value = 1
  }
}

watch(users, () => {
  if (currentPage.value > totalPages.value) {
    currentPage.value = totalPages.value
  }
})

const editUser = (user) => {
  editing.value = user
  Object.assign(form, { username: user.username, email: user.email, password: '', full_name: user.full_name, phone: user.phone || '', address: user.address || '' })
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  editing.value = null
  Object.assign(form, { username: '', email: '', password: '', full_name: '', phone: '', address: '' })
}

const saveUser = async () => {
  const data = { ...form }
  if (editing.value && !data.password) delete data.password

  const result = editing.value
    ? await adminStore.updateUser(editing.value.id, data, authStore.token)
    : await adminStore.createReceptionist(data, authStore.token)

  if (result.success) {
    alert(editing.value ? 'User berhasil diupdate' : 'Resepsionis berhasil ditambahkan')
    closeModal()
    loadUsers()
  } else {
    alert(result.message || 'Gagal menyimpan')
  }
}

const approveUser = async (id) => {
  const result = await adminStore.approveUser(id, authStore.token)
  if (result.success) { alert('User berhasil disetujui'); loadUsers() }
  else alert(result.message || 'Gagal menyetujui')
}

const deleteUser = async (id) => {
  if (confirm('Yakin ingin menghapus user ini?')) {
    const result = await adminStore.deleteUser(id, authStore.token)
    if (result.success) { alert('User berhasil dihapus'); loadUsers() }
    else alert(result.message || 'Gagal menghapus')
  }
}

onMounted(async () => { await loadUsers(); loading.value = false })
</script>
