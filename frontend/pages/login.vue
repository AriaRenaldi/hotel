<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 flex">
    <div class="hidden lg:flex lg:flex-1 bg-gradient-to-br from-slate-900 via-[#0f2742] to-[#143a63] relative overflow-hidden">
      <div class="absolute inset-0 opacity-10">
        <div class="absolute top-12 left-12 w-40 h-40 border border-white/20 rounded-full"></div>
        <div class="absolute top-40 right-16 w-24 h-24 bg-white/10 rounded-full blur-sm"></div>
        <div class="absolute bottom-24 left-20 w-32 h-32 border border-amber-200/20 rounded-[2rem] rotate-12"></div>
        <div class="absolute bottom-16 right-24 w-56 h-56 bg-blue-300/10 rounded-full blur-3xl"></div>
      </div>

      <div class="relative z-10 flex flex-col justify-center px-12 xl:px-20 py-12 text-white">
        <div class="mb-12">
          <div class="w-16 h-16 bg-gradient-to-r from-amber-400 to-yellow-500 rounded-2xl flex items-center justify-center shadow-2xl shadow-amber-500/20 mb-5">
            <svg class="w-8 h-8 text-slate-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
          </div>
          <h1 class="text-4xl font-bold tracking-tight">HotelKu</h1>
          <p class="text-slate-300 mt-3 text-lg">Pengalaman menginap premium dengan sistem reservasi yang aman, elegan, dan mudah diakses.</p>
        </div>

        <div class="space-y-8">
          <div class="flex items-start space-x-4">
            <div class="w-11 h-11 bg-white/10 rounded-2xl flex items-center justify-center flex-shrink-0 mt-1 border border-white/10">
              <svg class="w-5 h-5 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
              </svg>
            </div>
            <div>
              <h3 class="text-lg font-semibold text-white">Reservasi Lebih Tertata</h3>
              <p class="text-slate-300 mt-1">Kelola pemesanan, verifikasi tamu, dan akses akun dengan alur yang nyaman untuk tim maupun pelanggan.</p>
            </div>
          </div>

          <div class="flex items-start space-x-4">
            <div class="w-11 h-11 bg-white/10 rounded-2xl flex items-center justify-center flex-shrink-0 mt-1 border border-white/10">
              <svg class="w-5 h-5 text-sky-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <div>
              <h3 class="text-lg font-semibold text-white">Akses Cepat dan Responsif</h3>
              <p class="text-slate-300 mt-1">Masuk ke dashboard hotel kapan saja untuk memantau operasional, kamar, dan layanan tamu secara efisien.</p>
            </div>
          </div>

          <div class="flex items-start space-x-4">
            <div class="w-11 h-11 bg-white/10 rounded-2xl flex items-center justify-center flex-shrink-0 mt-1 border border-white/10">
              <svg class="w-5 h-5 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
              </svg>
            </div>
            <div>
              <h3 class="text-lg font-semibold text-white">Privasi Tamu Terjaga</h3>
              <p class="text-slate-300 mt-1">Keamanan dan privasi Anda kami utamakan dalam setiap proses login, verifikasi, dan pengelolaan data hotel.</p>
            </div>
          </div>
        </div>

        <div class="mt-16 p-6 bg-white/10 backdrop-blur-md rounded-3xl border border-white/15 shadow-2xl">
          <div class="flex items-center space-x-3 mb-3">
            <div class="w-10 h-10 bg-gradient-to-r from-amber-400 to-yellow-500 rounded-full flex items-center justify-center text-slate-900 font-bold text-sm">
              HK
            </div>
            <div>
              <h4 class="text-white font-semibold">Layanan Hotel Premium</h4>
              <p class="text-slate-300 text-sm">Hospitality Management System</p>
            </div>
          </div>
          <p class="text-slate-200 italic leading-relaxed">
            "Selamat datang di HotelKu, tempat kenyamanan, keamanan, dan pelayanan elegan bertemu dalam satu pengalaman digital yang modern."
          </p>
        </div>
      </div>
    </div>

    <div class="flex-1 flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-20">
      <div class="mx-auto w-full max-w-md">
        <div class="lg:hidden flex justify-center mb-8">
          <div class="w-16 h-16 bg-gradient-to-r from-slate-800 to-[#143a63] rounded-2xl flex items-center justify-center shadow-lg">
            <svg class="w-8 h-8 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
          </div>
        </div>

        <div class="bg-white/90 backdrop-blur-xl rounded-[2rem] shadow-2xl border border-slate-200/80 p-8 sm:p-10 relative overflow-hidden">
          <div class="absolute inset-x-0 top-0 h-1.5 bg-gradient-to-r from-amber-400 via-blue-500 to-slate-800"></div>
          <div class="text-center mb-8 relative">
            <div class="inline-flex items-center gap-2 rounded-full border border-blue-100 bg-blue-50 px-4 py-2 text-xs font-semibold uppercase tracking-[0.25em] text-[#143a63] shadow-sm">
              <span class="inline-block h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
              Secure Hotel Access
            </div>
            <h2 class="mt-5 text-3xl font-bold text-slate-800 sm:text-4xl">Masuk ke Akun Anda</h2>
            <p class="mt-3 text-slate-600 leading-relaxed max-w-md mx-auto">Selamat datang di HotelKu. Masukkan email dan password untuk menikmati pengalaman menginap yang aman, nyaman, dan elegan.</p>
            <div class="mt-5 grid grid-cols-2 gap-3 text-left">
              <div class="rounded-2xl border border-slate-100 bg-slate-50/80 px-4 py-3 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Experience</p>
                <p class="mt-1 text-sm font-semibold text-slate-700">Luxury stay</p>
              </div>
              <div class="rounded-2xl border border-blue-100 bg-blue-50/80 px-4 py-3 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-blue-400">Protection</p>
                <p class="mt-1 text-sm font-semibold text-[#143a63]">Privasi terjaga</p>
              </div>
            </div>
          </div>


          <form @submit.prevent="handleLogin" class="space-y-6">
            <div>
              <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">Email</label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                  <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                  </svg>
                </div>
                <input
                  id="email"
                  v-model="form.email"
                  type="email"
                  required
                  class="block w-full pl-12 pr-4 py-3.5 border border-slate-200 rounded-2xl placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white transition-all duration-200"
                  placeholder="Masukkan email Anda"
                >
              </div>
            </div>

            <div>
              <label for="password" class="block text-sm font-semibold text-slate-700 mb-2">Password</label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                  <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                  </svg>
                </div>
                <input
                  id="password"
                  v-model="form.password"
                  :type="showPassword ? 'text' : 'password'"
                  required
                  class="block w-full pl-12 pr-12 py-3.5 border border-slate-200 rounded-2xl placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white transition-all duration-200"
                  placeholder="Masukkan password Anda"
                >
                <button
                  type="button"
                  @click="showPassword = !showPassword"
                  class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-blue-500 transition-colors duration-200"
                >
                  <svg v-if="showPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                  </svg>
                  <svg v-else class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                  </svg>
                </button>
              </div>
              <div class="mt-3 flex items-center justify-between gap-4">
                <label class="flex items-center text-sm text-slate-600">
                  <input v-model="showPassword" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-slate-300 rounded mr-2">
                  Tampilkan Password
                </label>
                <NuxtLink to="/forgot-password" class="text-sm font-medium text-blue-600 hover:text-blue-500 transition-colors duration-200">
                  Lupa Password?
                </NuxtLink>
              </div>
            </div>

            <div v-if="error" class="rounded-2xl bg-red-50 border border-red-200 p-4">
              <div class="flex items-start gap-3">
                <svg class="h-5 w-5 text-red-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-sm font-medium text-red-800">{{ error }}</p>
              </div>
            </div>

            <button
              type="submit"
              :disabled="loading"
              :class="[
                'group relative w-full flex justify-center items-center py-3.5 px-4 border border-transparent rounded-2xl text-sm font-semibold text-white transition-all duration-300',
                loading
                  ? 'bg-slate-400 cursor-not-allowed'
                  : 'bg-gradient-to-r from-slate-800 via-[#143a63] to-[#1d4d7d] hover:from-slate-900 hover:via-[#123556] hover:to-[#17466f] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 shadow-lg hover:shadow-xl hover:-translate-y-0.5'
              ]"
            >
              <span v-if="loading" class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></span>
              {{ loading ? 'Sedang Masuk...' : 'Masuk ke HotelKu' }}
            </button>
          </form>

          <div class="mt-8 space-y-3 text-center">
            <p class="text-sm text-slate-600">
              Belum punya akun?
              <NuxtLink to="/register" class="font-medium text-blue-600 hover:text-blue-500 transition-colors duration-200">
                Daftar Sekarang
              </NuxtLink>
            </p>
          </div>
        </div>

        <div class="mt-8 text-center">
          <p class="text-xs text-slate-500">© 2026 HotelKu Management System. Semua hak dilindungi.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useAuthStore } from '~/stores/auth'

definePageMeta({
  middleware: false
})

const authStore = useAuthStore()

const form = reactive({
  email: '',
  password: '',
})

const loading = ref(false)
const error = ref('')
const showPassword = ref(false)

const handleLogin = async () => {
  if (loading.value) return

  loading.value = true
  error.value = ''

  try {
    const result = await authStore.login(form.email, form.password)

    if (result.success) {
      if (authStore.isAdmin) await navigateTo('/admin/dashboard')
      else if (authStore.isReceptionist) await navigateTo('/receptionist/dashboard')
      else await navigateTo('/guest/dashboard')
    } else {
      error.value = result.message || 'Login failed'
      if (result.isVerified === false) {
        await navigateTo(`/verify-otp?email=${form.email}&type=registration&channel=gmail`)
      }
    }
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  if (authStore.isAuthenticated) {
    if (authStore.isAdmin) {
      navigateTo('/admin/dashboard')
    } else if (authStore.isReceptionist) {
      navigateTo('/receptionist/dashboard')
    } else {
      navigateTo('/guest/dashboard')
    }
  }
})
</script>

