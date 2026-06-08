import { defineStore } from 'pinia'

// API base URL - you can change this to match your backend
const API_BASE = 'http://127.0.0.1:8000/api'

interface User {
  id: number
  username: string
  email: string
  full_name: string
  role: string
  phone: string
  address: string
  photo?: string | null
  photo_url?: string | null
}

interface AuthState {
  user: User | null
  token: string | null
  isAuthenticated: boolean
}

interface ApiResponse {
  success: boolean
  message: string
  data?: any
}

const formatValidationMessage = (error: any, fallback: string): string => {
  const validationErrors = error?.data?.errors

  if (validationErrors && typeof validationErrors === 'object') {
    const messages = Object.values(validationErrors)
      .flat()
      .map((message) => String(message).trim())
      .filter(Boolean)

    const uniqueMessages = [...new Set(messages)]
    if (uniqueMessages.length > 0) {
      if (uniqueMessages.length === 1) return uniqueMessages[0]
      return uniqueMessages.join(' dan ')
    }
  }

  return error?.data?.message || fallback
}

export const useAuthStore = defineStore('auth', {
  state: (): AuthState => ({
    user: null,
    token: null,
    isAuthenticated: false,
  }),

  getters: {
    isGuest: (state) => state.user?.role === 'guest',
    isAdmin: (state) => state.user?.role === 'admin',
    isReceptionist: (state) => state.user?.role === 'receptionist',
  },

  actions: {
    async register(userData: Record<string, any>): Promise<ApiResponse> {
      try {
        const response = await $fetch(`${API_BASE}/auth/register`, {
          method: 'POST',
          body: userData,
        }) as ApiResponse

        if (response.success) {
          return { success: true, message: response.message, data: response.data }
        } else {
          return { success: false, message: response.message }
        }
      } catch (error: any) {
        return { success: false, message: formatValidationMessage(error, 'Registration failed') }
      }
    },

    async verifyOtp(email: string, otpCode: string): Promise<ApiResponse> {
      try {
        const response = await $fetch(`${API_BASE}/auth/verify-otp`, {
          method: 'POST',
          body: { email, otp_code: otpCode },
        }) as ApiResponse

        if (response.success) {
          return { success: true, message: response.message }
        } else {
          return { success: false, message: response.message }
        }
      } catch (error: any) {
        return { success: false, message: error?.data?.message || 'OTP verification failed' }
      }
    },

    async resendOtp(email: string, type: 'registration' | 'reset_password', otpChannel: 'gmail' | 'whatsapp'): Promise<ApiResponse> {
      try {
        const response = await $fetch(`${API_BASE}/auth/resend-otp`, {
          method: 'POST',
          body: { email, type, otp_channel: otpChannel },
        }) as ApiResponse

        if (response.success) {
          return { success: true, message: response.message, data: response.data }
        } else {
          return { success: false, message: response.message }
        }
      } catch (error: any) {
        return { success: false, message: error?.data?.message || 'Resend OTP failed' }
      }
    },

    async login(email: string, password: string): Promise<ApiResponse & { isVerified?: boolean }> {
      try {
        const response = await $fetch(`${API_BASE}/auth/login`, {
          method: 'POST',
          body: { email, password },
          throw: false,
          timeout: 12000,
        }) as ApiResponse

        if (response.success) {
          this.user = response.data?.user ?? null
          this.token = response.data?.token ?? null
          this.isAuthenticated = Boolean(this.user && this.token)

          if (typeof window !== 'undefined' && this.user && this.token) {
            localStorage.setItem('auth_token', this.token)
            localStorage.setItem('auth_user', JSON.stringify(this.user))
          }

          return { success: true, message: response.message, data: response.data }
        } else {
          return { success: false, message: response.message, isVerified: response.data?.is_verified }
        }
      } catch (error: any) {
        const rawMessage = error?.data?.message || error?.message || 'Login failed'
        const message = /timeout|timed out/i.test(String(rawMessage))
          ? 'Server login tidak merespons. Cek backend Laravel dan koneksi database.'
          : rawMessage
        const isVerified = error?.data?.data?.is_verified || error?.data?.is_verified
        return { success: false, message, isVerified }
      }
    },

    async forgotPassword(identifier: string, otpChannel: 'gmail' | 'whatsapp'): Promise<ApiResponse> {
      try {
        const body = otpChannel === 'whatsapp'
          ? { phone: identifier, otp_channel: otpChannel }
          : { email: identifier, otp_channel: otpChannel }

        const response = await $fetch(`${API_BASE}/auth/forgot-password`, {
          method: 'POST',
          body,
        }) as ApiResponse

        if (response.success) {
          return { success: true, message: response.message, data: response.data }
        } else {
          return { success: false, message: response.message }
        }
      } catch (error: any) {
        return { success: false, message: error?.data?.message || 'Forgot password failed' }
      }
    },

    async resetPassword(email: string, otpCode: string, newPassword: string): Promise<ApiResponse> {
      try {
        const response = await $fetch(`${API_BASE}/auth/reset-password`, {
          method: 'POST',
          body: { email, otp_code: otpCode, password: newPassword },
        }) as ApiResponse

        if (response.success) {
          return { success: true, message: response.message }
        } else {
          return { success: false, message: response.message }
        }
      } catch (error: any) {
        return { success: false, message: error?.data?.message || 'Reset password failed' }
      }
    },

    async logout(callApi = true): Promise<void> {
      try {
        if (callApi && this.token) {
          await $fetch(`${API_BASE}/logout`, {
            method: 'POST',
            headers: {
              Authorization: `Bearer ${this.token}`,
            },
            throw: false,
          })
        }
      } catch (_error) {
        // Ignore logout API errors (e.g. expired token) and clear local session anyway.
      }

      // Clear local state
      this.user = null
      this.token = null
      this.isAuthenticated = false

      // Clear localStorage (only on client side)
      if (typeof window !== 'undefined') {
        localStorage.removeItem('auth_token')
        localStorage.removeItem('auth_user')
      }

      // Note: navigateTo should be called from component, not store
    },

    async fetchProfile(): Promise<void> {
      if (!this.token) return

      try {
        const response = await $fetch(`${API_BASE}/profile`, {
          headers: {
            Authorization: `Bearer ${this.token}`,
          },
        }) as ApiResponse

        if (response.success) {
          this.user = response.data?.user || response.data
          if (typeof window !== 'undefined') {
            localStorage.setItem('auth_user', JSON.stringify(this.user))
          }
          this.isAuthenticated = true
        }
      } catch (_error) {
        // If token invalid, clear local session without calling logout endpoint.
        await this.logout(false)
      }
    },

    // Initialize auth state from localStorage
    initAuth(): void {
      if (typeof window !== 'undefined') {
        const token = localStorage.getItem('auth_token')
        const userStr = localStorage.getItem('auth_user')

        if (token && userStr) {
          this.token = token
          try {
            this.user = JSON.parse(userStr)
            this.isAuthenticated = true
          } catch (e) {
            // Invalid JSON, clear storage
            localStorage.removeItem('auth_token')
            localStorage.removeItem('auth_user')
          }
        }
      }
    },
  },
})
