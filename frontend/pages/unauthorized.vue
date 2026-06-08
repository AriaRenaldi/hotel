<template>
  <div class="min-h-screen bg-gradient-to-br from-red-50 via-orange-50 to-yellow-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 text-center">
      <!-- Icon -->
      <div class="w-24 h-24 bg-red-100 rounded-full flex items-center justify-center mx-auto">
        <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
        </svg>
      </div>

      <!-- Title -->
      <h1 class="text-3xl font-bold text-gray-900">Akses Ditolak</h1>
      <p class="text-gray-600 mt-2">Anda tidak memiliki izin untuk mengakses halaman ini.</p>

      <!-- Button -->
      <div class="mt-8">
        <button
          @click="goBack"
          class="w-full bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 transition font-semibold"
        >
          Kembali
        </button>
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

const goBack = () => {
  // Try to go back, or redirect to appropriate dashboard
  if (authStore.isAuthenticated) {
    if (authStore.isAdmin) {
      navigateTo('/admin/dashboard')
    } else if (authStore.isReceptionist) {
      navigateTo('/receptionist/dashboard')
    } else {
      navigateTo('/guest/dashboard')
    }
  } else {
    navigateTo('/login')
  }
}
</script>