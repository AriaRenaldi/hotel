<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-slate-100 to-slate-200 text-slate-900">
    <ReceptionistSidebar active="profile" />
    <div :class="collapsed ? 'lg:ml-20' : 'lg:ml-72'" class="transition-all duration-300 p-8">
      <section class="rounded-[2rem] border border-white/60 bg-gradient-to-r from-slate-400 via-slate-300 to-slate-200 p-6 mb-8 shadow-2xl backdrop-blur">
        <h1 class="text-3xl font-bold text-slate-900">Receptionist Profile Overview</h1>
        <p class="mt-2 text-slate-700">Data dan kontrol profil untuk sistem front desk HotelKu.</p>
      </section>

      <div class="max-w-3xl mx-auto">
        <h2 class="text-2xl font-semibold text-slate-900 mb-8">Profil Saya</h2>

        <div class="bg-slate-50/90 rounded-xl border border-slate-200 shadow-xl p-6 mb-6">
          <div class="flex items-center space-x-6 mb-6">
            <div class="w-20 h-20 bg-slate-200 rounded-full flex items-center justify-center">
              <span class="text-3xl font-bold text-slate-700">{{ authStore.user?.full_name?.charAt(0) || 'R' }}</span>
            </div>
            <div>
              <h3 class="text-xl font-bold text-slate-900">{{ authStore.user?.full_name }}</h3>
              <p class="text-slate-600">{{ authStore.user?.email }}</p>
              <span class="px-3 py-1 text-xs rounded-full bg-slate-100 text-slate-700 capitalize">{{ authStore.user?.role }}</span>
            </div>
          </div>

          <form @submit.prevent="updateProfile">
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Username</label>
                <input v-model="profileForm.username" type="text" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-400 bg-white">
              </div>
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap</label>
                <input v-model="profileForm.full_name" type="text" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-400 bg-white">
              </div>
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">No. Telepon</label>
                <input v-model="profileForm.phone" type="text" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-400 bg-white">
              </div>
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Alamat</label>
                <textarea v-model="profileForm.address" rows="3" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-400 bg-white"></textarea>
              </div>
              <button type="submit" class="w-full bg-gradient-to-r from-slate-600 via-slate-500 to-slate-400 text-white py-2 rounded-lg hover:bg-slate-500 transition font-semibold">Simpan Perubahan</button>
            </div>
          </form>
        </div>

        <div class="bg-slate-50/90 rounded-xl border border-slate-200 shadow-xl p-6">
          <h3 class="text-lg font-semibold text-slate-900 mb-4">Ubah Password</h3>
          <form @submit.prevent="changePassword">
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Password Saat Ini</label>
                <input v-model="passwordForm.current_password" type="password" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-400 bg-white">
              </div>
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Password Baru</label>
                <input v-model="passwordForm.new_password" type="password" required minlength="6" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-400 bg-white">
              </div>
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Konfirmasi Password Baru</label>
                <input v-model="passwordForm.new_password_confirmation" type="password" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-400 bg-white">
              </div>
              <button type="submit" class="w-full bg-gradient-to-r from-slate-600 to-slate-400 text-white py-2 rounded-lg hover:bg-slate-500 transition font-semibold">Ubah Password</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useAuthStore } from '~/stores/auth'
import ReceptionistSidebar from '~/components/ReceptionistSidebar.vue'

definePageMeta({ middleware: 'receptionist' })

const authStore = useAuthStore()

const profileForm = reactive({
  username: authStore.user?.username || '',
  full_name: authStore.user?.full_name || '',
  phone: authStore.user?.phone || '',
  address: authStore.user?.address || ''
})

const passwordForm = reactive({
  current_password: '',
  new_password: '',
  new_password_confirmation: ''
})

const updateProfile = async () => {
  // Implementasi update profile
  alert('Fitur update profile akan diimplementasikan')
}

const changePassword = async () => {
  // Implementasi change password
  alert('Fitur change password akan diimplementasikan')
}
</script>