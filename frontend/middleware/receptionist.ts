import { useAuthStore } from '~/stores/auth'

export default defineNuxtRouteMiddleware(async () => {
  const authStore = useAuthStore()

  if (process.client && !authStore.isAuthenticated) {
    authStore.initAuth()
    if (authStore.token && !authStore.user) {
      await authStore.fetchProfile()
    }
  }

  if (!authStore.isAuthenticated) {
    return navigateTo('/login')
  }

  if (!authStore.isReceptionist) {
    return navigateTo('/unauthorized')
  }
})
