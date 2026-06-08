import { useAuthStore } from '~/stores/auth'

export default defineNuxtPlugin(async () => {
  const authStore = useAuthStore()

  // Initialize auth state from localStorage
  authStore.initAuth()

  // Validate stored token on app startup. If invalid, store will auto-logout.
  if (authStore.token) {
    await authStore.fetchProfile()
  }
})
