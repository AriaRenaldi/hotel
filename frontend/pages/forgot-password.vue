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
            <svg class="w-8 h-8 text-slate-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
          </div>
          <h1 class="text-4xl font-bold tracking-tight">Pemulihan Akses</h1>
          <p class="text-slate-300 mt-3 text-lg">Kami membantu Anda kembali ke akun HotelKu dengan proses reset password yang aman dan tetap menjaga kerahasiaan data.</p>
        </div>

        <div class="space-y-8">
          <div class="flex items-start space-x-4"><div class="w-11 h-11 bg-white/10 rounded-2xl flex items-center justify-center flex-shrink-0 mt-1 border border-white/10"><svg class="w-5 h-5 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div><div><h3 class="text-lg font-semibold text-white">Proses Cepat dan Tertata</h3><p class="text-slate-300 mt-1">Permintaan reset dikirim secara instan agar Anda bisa kembali mengakses akun tanpa hambatan yang berlebihan.</p></div></div>
          <div class="flex items-start space-x-4"><div class="w-11 h-11 bg-white/10 rounded-2xl flex items-center justify-center flex-shrink-0 mt-1 border border-white/10"><svg class="w-5 h-5 text-sky-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8m-18 8h18a2 2 0 002-2V8a2 2 0 00-2-2H3a2 2 0 00-2 2v6a2 2 0 002 2z"></path></svg></div><div><h3 class="text-lg font-semibold text-white">Instruksi Dikirim ke Email Anda</h3><p class="text-slate-300 mt-1">Kami hanya mengirimkan informasi pemulihan ke alamat email yang terdaftar agar proses tetap aman.</p></div></div>
          <div class="flex items-start space-x-4"><div class="w-11 h-11 bg-white/10 rounded-2xl flex items-center justify-center flex-shrink-0 mt-1 border border-white/10"><svg class="w-5 h-5 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg></div><div><h3 class="text-lg font-semibold text-white">Keamanan Tetap Terjaga</h3><p class="text-slate-300 mt-1">Kami menjaga kerahasiaan informasi akun dan memastikan proses pemulihan berjalan dengan lapisan verifikasi yang aman.</p></div></div>
        </div>
      </div>
    </div>

    <div class="flex-1 flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-20">
      <div class="mx-auto w-full max-w-md">
        <div class="lg:hidden flex justify-center mb-8"><div class="w-16 h-16 bg-gradient-to-r from-slate-800 to-[#143a63] rounded-2xl flex items-center justify-center shadow-lg"><svg class="w-8 h-8 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg></div></div>

        <div class="bg-white/90 backdrop-blur-xl rounded-[2rem] shadow-2xl border border-slate-200/80 p-8 sm:p-10 relative overflow-hidden">
          <div class="absolute inset-x-0 top-0 h-1.5 bg-gradient-to-r from-amber-400 via-blue-500 to-slate-800"></div>
          <div class="text-center mb-8 relative">
            <div class="inline-flex items-center gap-2 rounded-full border border-blue-100 bg-blue-50 px-4 py-2 text-xs font-semibold uppercase tracking-[0.25em] text-[#143a63] shadow-sm">
              <span class="inline-block h-2 w-2 rounded-full bg-sky-400 animate-pulse"></span>
              Account Recovery
            </div>
            <h2 class="mt-5 text-3xl font-bold text-slate-800 sm:text-4xl">Lupa Password</h2>
            <p class="mt-3 text-slate-600 leading-relaxed max-w-md mx-auto">Tenang, akses akun HotelKu Anda tetap aman. Pilih metode OTP lalu masukkan email atau nomor WhatsApp yang sesuai akun.</p>
            <div class="mt-5 grid grid-cols-2 gap-3 text-left">
              <div class="rounded-2xl border border-slate-100 bg-slate-50/80 px-4 py-3 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Support</p>
                <p class="mt-1 text-sm font-semibold text-slate-700">Reset lebih cepat</p>
              </div>
              <div class="rounded-2xl border border-blue-100 bg-blue-50/80 px-4 py-3 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-blue-400">Security</p>
                <p class="mt-1 text-sm font-semibold text-[#143a63]">Akses terlindungi</p>
              </div>
            </div>
          </div>

          <form @submit.prevent="handleForgotPassword" class="space-y-6">
            <div>
              <p class="block text-sm font-semibold text-slate-700 mb-2">Metode Verifikasi OTP</p>
              <div class="grid grid-cols-2 gap-3">
                <label class="flex items-center gap-2 rounded-xl border border-slate-200 px-3.5 py-3 cursor-pointer">
                  <input v-model="otpChannel" type="radio" value="gmail" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-slate-300">
                  <span class="text-sm font-medium text-slate-700">Gmail</span>
                </label>
                <label class="flex items-center gap-2 rounded-xl border border-slate-200 px-3.5 py-3 cursor-pointer">
                  <input v-model="otpChannel" type="radio" value="whatsapp" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-slate-300">
                  <span class="text-sm font-medium text-slate-700">WhatsApp</span>
                </label>
              </div>
            </div>
            <div>
              <label for="identifier" class="block text-sm font-semibold text-slate-700 mb-2">{{ identifierLabel }}</label>
              <input
                id="identifier"
                v-model="identifier"
                :type="identifierInputType"
                required
                class="block w-full px-4 py-3.5 border border-slate-200 rounded-2xl placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white transition-all duration-200"
                :placeholder="identifierPlaceholder"
              >
            </div>
            <div v-if="error" class="rounded-2xl bg-red-50 border border-red-200 p-4 text-sm font-medium text-red-800">{{ error }}</div>
            <div v-if="success" class="rounded-2xl bg-emerald-50 border border-emerald-200 p-4 text-sm font-medium text-emerald-800">{{ success }}</div>
            <button type="submit" :disabled="loading" :class="['group relative w-full flex justify-center items-center py-3.5 px-4 border border-transparent rounded-2xl text-sm font-semibold text-white transition-all duration-300', loading ? 'bg-slate-400 cursor-not-allowed' : 'bg-gradient-to-r from-slate-800 via-[#143a63] to-[#1d4d7d] hover:from-slate-900 hover:via-[#123556] hover:to-[#17466f] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 shadow-lg hover:shadow-xl hover:-translate-y-0.5']"><span v-if="loading" class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></span>{{ loading ? 'Mengirim...' : 'Kirim Kode Reset' }}</button>
          </form>
          <div class="mt-8 space-y-3 text-center"><NuxtLink to="/login" class="text-sm font-medium text-blue-600 hover:text-blue-500 transition-colors duration-200">Kembali ke Login</NuxtLink><p class="text-sm text-slate-600">Belum punya akun? <NuxtLink to="/register" class="font-medium text-blue-600 hover:text-blue-500 transition-colors duration-200">Daftar Sekarang</NuxtLink></p></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useAuthStore } from '~/stores/auth'

definePageMeta({ middleware: false })

const authStore = useAuthStore()
const identifier = ref('')
const otpChannel = ref('gmail')
const loading = ref(false)
const error = ref('')
const success = ref('')
const identifierLabel = computed(() => otpChannel.value === 'whatsapp' ? 'Nomor Telepon / WhatsApp' : 'Email')
const identifierInputType = computed(() => otpChannel.value === 'whatsapp' ? 'tel' : 'email')
const identifierPlaceholder = computed(() => otpChannel.value === 'whatsapp' ? 'Masukkan nomor telepon / WhatsApp Anda' : 'Masukkan email Anda')

const handleForgotPassword = async () => {
  loading.value = true
  error.value = ''
  success.value = ''
  const result = await authStore.forgotPassword(identifier.value, otpChannel.value)
  if (result.success) {
    success.value = result.message || 'OTP sent successfully'
    const nextEmail = result.data?.email || ''
    setTimeout(async () => { await navigateTo(`/reset-password?email=${encodeURIComponent(nextEmail)}`) }, 2000)
  } else {
    error.value = result.message || 'Failed to send OTP'
  }
  loading.value = false
}
</script>

