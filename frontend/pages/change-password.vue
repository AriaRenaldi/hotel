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
        <h2 class="text-3xl font-bold text-gray-900">Ubah Password</h2>
        <p class="mt-2 text-sm text-gray-600">Masukkan password lama dan password baru</p>
      </div>

      <!-- Form -->
      <div class="bg-white py-8 px-6 shadow-xl rounded-2xl">
        <form @submit.prevent="handleChangePassword" class="space-y-6">
          <!-- Current Password -->
          <div>
            <label for="current_password" class="block text-sm font-medium text-gray-700">Password Lama</label>
            <input
              id="current_password"
              v-model="form.current_password"
              type="password"
              required
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
              placeholder="Masukkan password lama"
            >
          </div>

          <!-- New Password -->
          <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Password Baru</label>
            <input
              id="password"
              v-model="form.password"
              type="password"
              required
              minlength="8"
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
              placeholder="Minimal 8 karakter"
            >
          </div>

          <!-- Confirm Password -->
          <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Password Baru</label>
            <input
              id="password_confirmation"
              v-model="form.password_confirmation"
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
            :disabled="loading"
            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span v-if="loading" class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></span>
            {{ loading ? 'Mengubah...' : 'Ubah Password' }}
          </button>
        </form>

        <!-- Links -->
        <div class="mt-6 text-center">
          <NuxtLink to="/profile" class="text-sm text-blue-600 hover:text-blue-500">
            Kembali ke Profil
          </NuxtLink>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useAuthStore } from '~/stores/auth'

definePageMeta({
  middleware: 'auth'
})

const authStore = useAuthStore()

const form = reactive({
  current_password: '',
  password: '',
  password_confirmation: '',
})

const loading = ref(false)
const error = ref('')
const success = ref('')

const handleChangePassword = async () => {
  loading.value = true
  error.value = ''
  success.value = ''

  // Validate password confirmation
  if (form.password !== form.password_confirmation) {
    error.value = 'Password baru dan konfirmasi password tidak cocok'
    loading.value = false
    return
  }

  try {
    const response = await $fetch('http://localhost:8000/api/profile/change-password', {
      method: 'POST',
      headers: {
        Authorization: `Bearer ${authStore.token}`,
      },
      body: {
        current_password: form.current_password,
        new_password: form.password,
        new_password_confirmation: form.password_confirmation,
      },
    })

    if (response.success) {
      success.value = response.message || 'Password changed successfully'
      // Clear form
      form.current_password = ''
      form.password = ''
      form.password_confirmation = ''
    } else {
      error.value = response.message || 'Failed to change password'
    }
  } catch (err) {
    error.value = err.data?.message || 'Gagal mengubah password'
  }

  loading.value = false
}

onMounted(() => {
  // Check if authenticated
  if (!authStore.isAuthenticated) {
    navigateTo('/login')
  }
})
</script>