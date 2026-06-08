<template>
  <div class="h-screen overflow-hidden bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 flex">
    <div class="hidden lg:flex lg:flex-1 h-full bg-gradient-to-br from-slate-900 via-[#0f2742] to-[#143a63] relative overflow-hidden">
      <div class="absolute inset-0 opacity-10">
        <div class="absolute top-12 left-12 w-40 h-40 border border-white/20 rounded-full"></div>
        <div class="absolute top-40 right-16 w-24 h-24 bg-white/10 rounded-full blur-sm"></div>
        <div class="absolute bottom-24 left-20 w-32 h-32 border border-amber-200/20 rounded-[2rem] rotate-12"></div>
        <div class="absolute bottom-16 right-24 w-56 h-56 bg-blue-300/10 rounded-full blur-3xl"></div>
      </div>

      <div class="relative z-10 flex h-full flex-col justify-center px-12 xl:px-20 py-10 text-white">
        <div class="mb-12">
          <div class="w-16 h-16 bg-gradient-to-r from-amber-400 to-yellow-500 rounded-2xl flex items-center justify-center shadow-2xl shadow-amber-500/20 mb-5">
            <svg class="w-8 h-8 text-slate-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
          </div>
          <h1 class="text-4xl font-bold tracking-tight">HotelKu</h1>
          <p class="text-slate-300 mt-3 text-lg">Bergabunglah dengan sistem hotel modern yang mengutamakan kenyamanan, kepercayaan, dan privasi setiap tamu.</p>
        </div>

        <div class="space-y-8">
          <div class="flex items-start space-x-4"><div class="w-11 h-11 bg-white/10 rounded-2xl flex items-center justify-center flex-shrink-0 mt-1 border border-white/10"><svg class="w-5 h-5 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div><div><h3 class="text-lg font-semibold text-white">Registrasi Lebih Nyaman</h3><p class="text-slate-300 mt-1">Buat akun Anda dengan proses yang ringkas untuk mulai menikmati layanan reservasi hotel yang lebih praktis.</p></div></div>
          <div class="flex items-start space-x-4"><div class="w-11 h-11 bg-white/10 rounded-2xl flex items-center justify-center flex-shrink-0 mt-1 border border-white/10"><svg class="w-5 h-5 text-sky-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5V4H2v16h5m10 0v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6m10 0H7"></path></svg></div><div><h3 class="text-lg font-semibold text-white">Akses Fitur Hotel Lengkap</h3><p class="text-slate-300 mt-1">Setelah terdaftar, Anda bisa menikmati pemesanan kamar, verifikasi cepat, dan pengelolaan profil dengan mudah.</p></div></div>
          <div class="flex items-start space-x-4"><div class="w-11 h-11 bg-white/10 rounded-2xl flex items-center justify-center flex-shrink-0 mt-1 border border-white/10"><svg class="w-5 h-5 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg></div><div><h3 class="text-lg font-semibold text-white">Data Anda Terjaga</h3><p class="text-slate-300 mt-1">Informasi pribadi dan detail akun diproses dengan aman agar pengalaman Anda terasa lebih tenang sejak awal.</p></div></div>
        </div>
      </div>
    </div>

    <div class="flex-1 h-full flex flex-col justify-center px-4 py-8 sm:px-6 lg:px-20">
      <div class="mx-auto w-full max-w-lg">
        <div class="lg:hidden flex justify-center mb-8"><div class="w-16 h-16 bg-gradient-to-r from-slate-800 to-[#143a63] rounded-2xl flex items-center justify-center shadow-lg"><svg class="w-8 h-8 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg></div></div>

        <div class="bg-white/90 backdrop-blur-xl rounded-[1.75rem] shadow-2xl border border-slate-200/80 p-6 sm:p-8 relative overflow-hidden">
          <div class="absolute inset-x-0 top-0 h-1.5 bg-gradient-to-r from-amber-400 via-blue-500 to-slate-800"></div>
          <div class="text-center mb-8 relative">
            <div class="inline-flex items-center gap-2 rounded-full border border-blue-100 bg-blue-50 px-4 py-2 text-xs font-semibold uppercase tracking-[0.25em] text-[#143a63] shadow-sm">
              <span class="inline-block h-2 w-2 rounded-full bg-amber-400 animate-pulse"></span>
              Guest Registration
            </div>
            <h2 class="mt-4 text-2xl font-bold text-slate-800 sm:text-3xl">Buat Akun Baru</h2>
            <p class="mt-2 text-sm text-slate-600 leading-relaxed max-w-md mx-auto">Selamat datang di HotelKu. Lengkapi data Anda untuk menikmati layanan hotel yang eksklusif, nyaman, dan terpercaya.</p>
            <div class="mt-4 grid grid-cols-2 gap-2.5 text-left">
              <div class="rounded-xl border border-slate-100 bg-slate-50/80 px-3.5 py-2.5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Journey</p>
                <p class="mt-1 text-sm font-semibold text-slate-700">Mulai reservasi</p>
              </div>
              <div class="rounded-xl border border-blue-100 bg-blue-50/80 px-3.5 py-2.5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-blue-400">Trusted</p>
                <p class="mt-1 text-sm font-semibold text-[#143a63]">Data aman</p>
              </div>
            </div>
          </div>

          <form @submit.prevent="handleRegister" class="space-y-4">
            <div class="grid sm:grid-cols-2 gap-3.5">
              <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Role</label>
                <div class="inline-flex items-center rounded-xl border border-emerald-200 bg-emerald-50 px-3.5 py-2.5 text-sm font-semibold text-emerald-800">
                  Tamu
                </div>
              </div>
              <div><label for="full_name" class="block text-sm font-semibold text-slate-700 mb-2">Nama Lengkap</label><input id="full_name" v-model="form.full_name" type="text" required class="block w-full px-3.5 py-3 border border-slate-200 rounded-xl placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white transition-all duration-200" placeholder="Masukkan nama lengkap"></div>
            </div>
            <div class="grid sm:grid-cols-2 gap-3.5">
              <div><label for="username" class="block text-sm font-semibold text-slate-700 mb-2">Username</label><input id="username" v-model="form.username" type="text" required class="block w-full px-3.5 py-3 border border-slate-200 rounded-xl placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white transition-all duration-200" placeholder="Masukkan username"></div>
              <div><label for="email" class="block text-sm font-semibold text-slate-700 mb-2">Email</label><input id="email" v-model="form.email" type="email" required class="block w-full px-3.5 py-3 border border-slate-200 rounded-xl placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white transition-all duration-200" placeholder="Masukkan email"></div>
            </div>
            <div class="grid sm:grid-cols-2 gap-3.5">
              <div><label for="phone" class="block text-sm font-semibold text-slate-700 mb-2">Nomor Telepon</label><input id="phone" v-model="form.phone" type="tel" required class="block w-full px-3.5 py-3 border border-slate-200 rounded-xl placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white transition-all duration-200" placeholder="Masukkan nomor telepon"></div>
              <div><label for="address" class="block text-sm font-semibold text-slate-700 mb-2">Alamat</label><textarea id="address" v-model="form.address" rows="1" required class="block w-full px-3.5 py-3 border border-slate-200 rounded-xl placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white transition-all duration-200 resize-none" placeholder="Masukan alamat"></textarea></div>
            </div>
            <div class="grid sm:grid-cols-2 gap-3.5">
              <div><label for="password" class="block text-sm font-semibold text-slate-700 mb-2">Password</label><div class="relative"><input id="password" v-model="form.password" :type="showPassword ? 'text' : 'password'" required minlength="8" class="block w-full px-3.5 pr-12 py-3 border border-slate-200 rounded-xl placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white transition-all duration-200" placeholder="Minimal 8 karakter"><button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-blue-500 transition-colors duration-200"><span class="text-sm font-semibold">{{ showPassword ? 'Hide' : 'Show' }}</span></button></div></div>
              <div><label for="password_confirmation" class="block text-sm font-semibold text-slate-700 mb-2">Konfirmasi Password</label><input id="password_confirmation" v-model="form.password_confirmation" :type="showPassword ? 'text' : 'password'" required class="block w-full px-3.5 py-3 border border-slate-200 rounded-xl placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white transition-all duration-200" placeholder="Ulangi password"></div>
            </div>
            <div>
              <p class="block text-sm font-semibold text-slate-700 mb-2">Metode Verifikasi OTP</p>
              <div class="grid sm:grid-cols-2 gap-3.5">
                <label class="flex items-center gap-2 rounded-xl border border-slate-200 px-3.5 py-3 cursor-pointer">
                  <input v-model="form.otp_channel" type="radio" value="gmail" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-slate-300">
                  <span class="text-sm font-medium text-slate-700">Gmail</span>
                </label>
                <label class="flex items-center gap-2 rounded-xl border border-slate-200 px-3.5 py-3 cursor-pointer">
                  <input v-model="form.otp_channel" type="radio" value="whatsapp" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-slate-300">
                  <span class="text-sm font-medium text-slate-700">WhatsApp</span>
                </label>
              </div>
            </div>
            <label class="flex items-center text-sm text-slate-600"><input v-model="showPassword" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-slate-300 rounded mr-2">Tampilkan Password</label>
            <div v-if="error" class="rounded-2xl bg-red-50 border border-red-200 p-4 text-sm font-medium text-red-800">{{ error }}</div>
            <div v-if="success" class="rounded-2xl bg-emerald-50 border border-emerald-200 p-4 text-sm font-medium text-emerald-800">{{ success }}</div>
            <button type="submit" :disabled="loading" :class="['group relative w-full flex justify-center items-center py-3.5 px-4 border border-transparent rounded-2xl text-sm font-semibold text-white transition-all duration-300', loading ? 'bg-slate-400 cursor-not-allowed' : 'bg-gradient-to-r from-slate-800 via-[#143a63] to-[#1d4d7d] hover:from-slate-900 hover:via-[#123556] hover:to-[#17466f] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 shadow-lg hover:shadow-xl hover:-translate-y-0.5']"><span v-if="loading" class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></span>{{ loading ? 'Mendaftarkan...' : 'Buat Akun HotelKu' }}</button>
          </form>

          <div class="mt-8 text-center"><p class="text-sm text-slate-600">Sudah punya akun? <NuxtLink to="/login" class="font-medium text-blue-600 hover:text-blue-500 transition-colors duration-200">Masuk Sekarang</NuxtLink></p></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useAuthStore } from '~/stores/auth'

definePageMeta({ middleware: false })

const authStore = useAuthStore()
const form = reactive({ role: 'guest', full_name: '', username: '', email: '', phone: '', address: '', password: '', password_confirmation: '', otp_channel: '' })
const loading = ref(false)
const error = ref('')
const success = ref('')
const showPassword = ref(false)

const handleRegister = async () => {
  loading.value = true
  error.value = ''
  success.value = ''
  if (form.password !== form.password_confirmation) {
    error.value = 'Password dan konfirmasi password tidak cocok'
    loading.value = false
    return
  }
  if (!form.otp_channel) {
    error.value = 'Pilih metode verifikasi OTP terlebih dahulu'
    loading.value = false
    return
  }
  const result = await authStore.register(form)
  if (result.success) {
    success.value = result.message || 'Registration successful'
    const recipient = form.otp_channel === 'whatsapp' ? form.phone : form.email
    setTimeout(async () => { await navigateTo(`/verify-otp?email=${encodeURIComponent(form.email)}&channel=${form.otp_channel}&recipient=${encodeURIComponent(recipient)}`) }, 2000)
  } else {
    error.value = result.message || 'Registration failed'
  }
  loading.value = false
}
</script>
