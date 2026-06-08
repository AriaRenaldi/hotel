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
          <div class="w-16 h-16 bg-gradient-to-r from-amber-400 to-yellow-500 rounded-2xl flex items-center justify-center shadow-2xl shadow-amber-500/20 mb-5"><svg class="w-8 h-8 text-slate-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>
          <h1 class="text-4xl font-bold tracking-tight">Verifikasi Aman</h1>
          <p class="text-slate-300 mt-3 text-lg">Lapisan OTP membantu memastikan akses akun HotelKu hanya digunakan oleh Anda dengan proses cepat dan aman.</p>
        </div>

        <div class="space-y-8">
          <div class="flex items-start space-x-4"><div class="w-11 h-11 bg-white/10 rounded-2xl flex items-center justify-center flex-shrink-0 mt-1 border border-white/10"><svg class="w-5 h-5 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div><div><h3 class="text-lg font-semibold text-white">Verifikasi Cepat</h3><p class="text-slate-300 mt-1">Masukkan enam digit kode OTP untuk melanjutkan proses login atau aktivasi akun dengan lebih aman.</p></div></div>
          <div class="flex items-start space-x-4"><div class="w-11 h-11 bg-white/10 rounded-2xl flex items-center justify-center flex-shrink-0 mt-1 border border-white/10"><svg class="w-5 h-5 text-sky-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 8v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8"></path></svg></div><div><h3 class="text-lg font-semibold text-white">Kode Dikirim ke {{ otpChannelLabel }}</h3><p class="text-slate-300 mt-1">Setiap kode verifikasi kami kirimkan hanya ke jalur verifikasi yang Anda pilih.</p></div></div>
          <div class="flex items-start space-x-4"><div class="w-11 h-11 bg-white/10 rounded-2xl flex items-center justify-center flex-shrink-0 mt-1 border border-white/10"><svg class="w-5 h-5 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg></div><div><h3 class="text-lg font-semibold text-white">Perlindungan Privasi Tambahan</h3><p class="text-slate-300 mt-1">Verifikasi tambahan ini membantu menjaga keamanan akun dan data tamu dari akses yang tidak sah.</p></div></div>
        </div>
      </div>
    </div>

    <div class="flex-1 flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-20">
      <div class="mx-auto w-full max-w-md">
        <div class="lg:hidden flex justify-center mb-8"><div class="w-16 h-16 bg-gradient-to-r from-slate-800 to-[#143a63] rounded-2xl flex items-center justify-center shadow-lg"><svg class="w-8 h-8 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div></div>

        <div class="bg-white/90 backdrop-blur-xl rounded-[2rem] shadow-2xl border border-slate-200/80 p-8 sm:p-10">
          <div class="text-center mb-8"><h2 class="text-3xl font-bold text-slate-800">Verifikasi OTP</h2><p class="mt-3 text-slate-600 leading-relaxed">Masukkan kode OTP yang kami kirimkan via {{ otpChannelLabel }} untuk mengaktifkan akun HotelKu Anda dengan aman.</p></div>
          <div class="mb-6 rounded-2xl border border-blue-100 bg-gradient-to-r from-blue-50 to-slate-50 p-4"><p class="text-sm font-semibold text-[#143a63]">Kode verifikasi hanya untuk Anda</p><p class="mt-1 text-xs text-slate-600 leading-relaxed">Kami menjaga keamanan akun dan privasi tamu dengan lapisan verifikasi tambahan pada setiap akses penting.</p></div>
          <p class="mb-6 text-sm font-medium text-blue-700 text-center break-all">{{ recipientDisplay }}</p>
          <form @submit.prevent="handleVerifyOtp" class="space-y-6">
            <div><label for="otp" class="block text-sm font-semibold text-slate-700 mb-2">Kode OTP</label><input id="otp" v-model="otp" type="text" required maxlength="6" class="block w-full px-4 py-4 border border-slate-200 rounded-2xl placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white transition-all duration-200 text-center text-3xl tracking-[0.5em] font-semibold" placeholder="000000"></div>
            <div v-if="timer > 0" class="rounded-2xl bg-slate-50 border border-slate-200 p-4 text-center text-sm text-slate-600">Kirim ulang kode dalam {{ timer }} detik</div>
            <div v-if="error" class="rounded-2xl bg-red-50 border border-red-200 p-4 text-sm font-medium text-red-800">{{ error }}</div>
            <div v-if="success" class="rounded-2xl bg-emerald-50 border border-emerald-200 p-4 text-sm font-medium text-emerald-800">{{ success }}</div>
            <button type="submit" :disabled="loading || otp.length !== 6" :class="['group relative w-full flex justify-center items-center py-3.5 px-4 border border-transparent rounded-2xl text-sm font-semibold text-white transition-all duration-300', loading || otp.length !== 6 ? 'bg-slate-400 cursor-not-allowed' : 'bg-gradient-to-r from-slate-800 via-[#143a63] to-[#1d4d7d] hover:from-slate-900 hover:via-[#123556] hover:to-[#17466f] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 shadow-lg hover:shadow-xl hover:-translate-y-0.5']"><span v-if="loading" class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></span>{{ loading ? 'Memverifikasi...' : 'Verifikasi Sekarang' }}</button>
          </form>
          <div class="mt-6 text-center"><button @click="handleResendOtp" :disabled="timer > 0 || resendLoading" class="text-sm font-medium text-blue-600 hover:text-blue-500 disabled:text-slate-400 disabled:cursor-not-allowed transition-colors duration-200"><span v-if="resendLoading" class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-blue-600 mr-1"></span>Kirim Ulang Kode OTP</button></div>
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
const route = useRoute()
const email = ref(route.query.email || '')
const type = ref(route.query.type || 'registration')
const otpChannel = ref(route.query.channel || 'gmail')
const otpChannelLabel = computed(() => otpChannel.value === 'whatsapp' ? 'WhatsApp' : 'Gmail')
const recipientDisplay = computed(() => {
  const recipient = String(route.query.recipient || '')
  if (recipient) return recipient
  return otpChannel.value === 'whatsapp' ? 'Nomor WhatsApp terdaftar' : String(email.value || '')
})
const otp = ref('')
const loading = ref(false)
const resendLoading = ref(false)
const error = ref('')
const success = ref('')
const timer = ref(0)

const handleVerifyOtp = async () => {
  loading.value = true
  error.value = ''
  success.value = ''
  try {
    const result = await authStore.verifyOtp(email.value, otp.value)
    if (result.success) {
      success.value = result.message || 'OTP verified successfully'
      await navigateTo('/login')
    } else {
      error.value = result.message || 'OTP verification failed'
    }
  } catch (error) {
    console.error('Verify OTP error:', error)
    error.value = error.message || 'Terjadi kesalahan saat verifikasi OTP'
  } finally {
    loading.value = false
  }
}

const handleResendOtp = async () => {
  resendLoading.value = true
  error.value = ''
  success.value = ''
  try {
    const otpType = type.value === 'reset_password' ? 'reset_password' : 'registration'
    const result = await authStore.resendOtp(email.value, otpType, otpChannel.value)
    if (result.success) {
      success.value = result.message
      timer.value = 60
      startTimer()
    } else {
      error.value = result.message
    }
  } catch (error) {
    console.error('Resend OTP error:', error)
    error.value = error.message || 'Gagal mengirim ulang OTP'
  } finally {
    resendLoading.value = false
  }
}

const startTimer = () => {
  const interval = setInterval(() => {
    timer.value--
    if (timer.value <= 0) clearInterval(interval)
  }, 1000)
}

onMounted(() => {
  if (!email.value) navigateTo('/register')
  else {
    timer.value = 60
    startTimer()
  }
})
</script>
