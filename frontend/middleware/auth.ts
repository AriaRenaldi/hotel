import { useAuthStore } from '~/stores/auth'

export default defineNuxtRouteMiddleware(async (to) => {
  const authStore = useAuthStore()
  const authPages = ['/login', '/register', '/forgot-password', '/reset-password', '/verify-otp']

  if (process.client && !authStore.isAuthenticated) {
    authStore.initAuth()
    if (authStore.token && !authStore.user) {
      await authStore.fetchProfile()
    }
  }

  // If not authenticated and trying to access protected routes
  if (!authStore.isAuthenticated && !authPages.includes(to.path)) {
    return navigateTo('/login')
  }

  // If authenticated and trying to access auth pages
  if (authStore.isAuthenticated && authPages.includes(to.path)) {
    // Redirect based on role
    if (authStore.isAdmin) {
      return navigateTo('/admin/dashboard')
    } else if (authStore.isReceptionist) {
      return navigateTo('/receptionist/dashboard')
    } else {
      return navigateTo('/guest/dashboard')
    }
  }
})
