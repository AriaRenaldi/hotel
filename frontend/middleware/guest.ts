import { useAuthStore } from '~/stores/auth'

export default defineNuxtRouteMiddleware(async () => {
  const authStore = useAuthStore()

  if (process.client) {
    if (!authStore.isAuthenticated) {
      authStore.initAuth()
    }

    // Always revalidate token on protected guest routes to avoid stale local session.
    if (authStore.token) {
      await authStore.fetchProfile()
    }
  }

  if (!authStore.isAuthenticated) {
    return navigateTo('/login')
  }

  if (!authStore.isGuest) {
    return navigateTo('/unauthorized')
  }
})
