<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
      <!-- Header -->
      <div class="text-center">
        <div class="w-16 h-16 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
          <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
          </svg>
        </div>
        <h2 class="text-3xl font-bold text-gray-900">Reset Password</h2>
        <p class="mt-2 text-sm text-gray-600">Masukkan kode OTP dan password baru</p>
        <p class="mt-1 text-sm text-blue-600 font-medium">{{ email }}</p>
      </div>

      <!-- Form -->
      <div class="bg-white py-8 px-6 shadow-xl rounded-2xl">
        <form @submit.prevent="handleResetPassword" class="space-y-6">
          <!-- OTP Input -->
          <div>
            <label for="otp" class="block text-sm font-medium text-gray-700">Kode OTP</label>
            <input
              id="otp"
              v-model="otp"
              type="text"
              required
              maxlength="6"
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-center text-2xl tracking-widest"
              placeholder="000000"
            >
          </div>

          <!-- New Password -->
          <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Password Baru</label>
            <input
              id="password"
              v-model="password"
              type="password"
              required
              minlength="6"
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
              placeholder="Minimal 6 karakter"
            >
          </div>

          <!-- Confirm Password -->
          <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Password Baru</label>
            <input
              id="password_confirmation"
              v-model="password_confirmation"
              type="password"
              required
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
              placeholder="Ulangi password baru"
            >
          </div>

          <!-- Error Message -->
          <div v-if="error" class="text-red-600 text-sm text-center">
            {{ error }}
          </div>

          <!-- Success Message -->
          <div v-if="success" class="text-green-600 text-sm text-center">
            {{ success }}
          </div>

          <!-- Submit Button -->
          <button
            type="submit"
            :disabled="loading || otp.length !== 6"
            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span v-if="loading" class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></span>
            {{ loading ? 'Mereset...' : 'Reset Password' }}
          </button>
        </form>

        <!-- Links -->
        <div class="mt-6 text-center space-y-2">
          <NuxtLink to="/login" class="text-sm text-blue-600 hover:text-blue-500">
            Kembali ke Login
          </NuxtLink>
          <div class="text-sm text-gray-600">
            Belum punya akun?
            <NuxtLink to="/register" class="text-blue-600 hover:text-blue-500 font-medium">
              Daftar Sekarang
            </NuxtLink>
          </div>
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
const route = useRoute()

const email = ref(route.query.email || '')
const otp = ref('')
const password = ref('')
const password_confirmation = ref('')
const loading = ref(false)
const error = ref('')
const success = ref('')

const handleResetPassword = async () => {
  loading.value = true
  error.value = ''
  success.value = ''

  // Validate password confirmation
  if (password.value !== password_confirmation.value) {
    error.value = 'Password dan konfirmasi password tidak cocok'
    loading.value = false
    return
  }

  const result = await authStore.resetPassword(email.value, otp.value, password.value)

  if (result.success) {
    success.value = result.message || 'Password reset successfully'
    setTimeout(async () => {
      await navigateTo('/login')
    }, 2000)
  } else {
    error.value = result.message || 'Password reset failed'
  }

  loading.value = false
}

onMounted(async () => {
  if (!email.value) {
    await navigateTo('/forgot-password')
    return
  }

  // Check if user has active OTP (optional - backend will validate anyway)
  try {
    // You could add an API call here to check OTP status if needed
  } catch (error) {
    error.value = 'Sesi reset password tidak valid. Silakan request ulang.'
    setTimeout(async () => {
      await navigateTo('/forgot-password')
    }, 3000)
  }
})
</script>