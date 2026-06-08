import { defineStore } from 'pinia'

const API_BASE = 'http://127.0.0.1:8000/api'

interface Booking {
  id: number
  booking_number: string
  guest_name: string
  check_in_date: string
  check_out_date: string
  booking_status: string
  total_amount: number
  payment_method: string
  payment_status: string
}

interface ApiResponse {
  success: boolean
  message: string
  data?: any
}

export const useReceptionistStore = defineStore('receptionist', {
  state: () => ({
    bookings: [] as Booking[],
    todayBookings: [] as Booking[],
    stats: {
      totalBookings: 0,
      checkedInToday: 0,
      checkedOutToday: 0,
      pendingPayments: 0,
    },
    loading: false,
  }),

  actions: {
    async getStats(token: string): Promise<ApiResponse> {
      try {
        const response = await $fetch(`${API_BASE}/receptionist/stats`, {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        }) as ApiResponse

        if (response.success) {
          this.stats = response.data
          return { success: true, message: 'Stats loaded', data: response.data }
        } else {
          return { success: false, message: response.message }
        }
      } catch (error: any) {
        return { success: false, message: error?.data?.message || 'Failed to load stats' }
      }
    },

    async getAllBookings(token: string, filters?: Record<string, string>): Promise<ApiResponse> {
      try {
        const params = filters ? '?' + new URLSearchParams(filters).toString() : ''
        const response = await $fetch(`${API_BASE}/receptionist/bookings${params}`, {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        }) as ApiResponse

        if (response.success) {
          this.bookings = response.data
          return { success: true, message: 'Bookings loaded', data: response.data }
        } else {
          return { success: false, message: response.message }
        }
      } catch (error: any) {
        return { success: false, message: error?.data?.message || 'Failed to load bookings' }
      }
    },

    async getTodayBookings(token: string): Promise<ApiResponse> {
      try {
        const response = await $fetch(`${API_BASE}/receptionist/bookings/today`, {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        }) as ApiResponse

        if (response.success) {
          this.todayBookings = response.data
          return { success: true, message: 'Today bookings loaded', data: response.data }
        } else {
          return { success: false, message: response.message }
        }
      } catch (error: any) {
        return { success: false, message: error?.data?.message || 'Failed to load today bookings' }
      }
    },

    async updateBookingStatus(bookingId: number, status: any, token: string): Promise<ApiResponse> {
      try {
        const body = typeof status === 'string' ? { booking_status: status } : status
        const response = await $fetch(`${API_BASE}/receptionist/bookings/${bookingId}/status`, {
          method: 'PUT',
          headers: {
            Authorization: `Bearer ${token}`,
          },
          body,
        }) as ApiResponse

        if (response.success) {
          return { success: true, message: response.message }
        } else {
          return { success: false, message: response.message }
        }
      } catch (error: any) {
        return { success: false, message: error?.data?.message || 'Failed to update booking status' }
      }
    },

    async verifyPayment(bookingId: number, status: any, token: string): Promise<ApiResponse> {
      try {
        const body = typeof status === 'string' ? { status } : status
        const response = await $fetch(`${API_BASE}/receptionist/bookings/${bookingId}/verify-payment`, {
          method: 'POST',
          headers: {
            Authorization: `Bearer ${token}`,
          },
          body,
        }) as ApiResponse

        if (response.success) {
          return { success: true, message: response.message }
        } else {
          return { success: false, message: response.message }
        }
      } catch (error: any) {
        return { success: false, message: error?.data?.message || 'Failed to verify payment' }
      }
    },

    async processCheckIn(bookingId: number, token: string): Promise<ApiResponse> {
      try {
        const response = await $fetch(`${API_BASE}/receptionist/bookings/${bookingId}/check-in`, {
          method: 'POST',
          headers: {
            Authorization: `Bearer ${token}`,
          },
        }) as ApiResponse

        if (response.success) {
          return { success: true, message: response.message }
        } else {
          return { success: false, message: response.message }
        }
      } catch (error: any) {
        return { success: false, message: error?.data?.message || 'Failed to process check-in' }
      }
    },

    async processCheckOut(_bookingId: number, _token: string): Promise<ApiResponse> {
      return {
        success: false,
        message: 'Check-out hanya dapat dilakukan oleh guest melalui akun guest.'
      }
    },

    async downloadDailyReport(date: string, token: string): Promise<ApiResponse> {
      try {
        const response = await $fetch(`${API_BASE}/receptionist/reports/daily?date=${date}`, {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        }) as ApiResponse

        if (response.success) {
          return { success: true, message: response.message, data: response.data }
        } else {
          return { success: false, message: response.message }
        }
      } catch (error: any) {
        return { success: false, message: error?.data?.message || 'Failed to download report' }
      }
    },

    async getCheckInsReport(date: string, token: string): Promise<ApiResponse> {
      try {
        const response = await $fetch(`${API_BASE}/receptionist/reports/check-ins?date=${date}`, {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        }) as ApiResponse

        if (response.success) {
          return { success: true, message: response.message, data: response.data }
        } else {
          return { success: false, message: response.message }
        }
      } catch (error: any) {
        return { success: false, message: error?.data?.message || 'Failed to load check-ins report' }
      }
    },

    async getCheckOutsReport(date: string, token: string): Promise<ApiResponse> {
      try {
        const response = await $fetch(`${API_BASE}/receptionist/reports/check-outs?date=${date}`, {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        }) as ApiResponse

        if (response.success) {
          return { success: true, message: response.message, data: response.data }
        } else {
          return { success: false, message: response.message }
        }
      } catch (error: any) {
        return { success: false, message: error?.data?.message || 'Failed to load check-outs report' }
      }
    },

    async getLateCheckoutPenaltyVerifications(status: string, token: string, page = 1, perPage = 6): Promise<ApiResponse> {
      try {
        const params = new URLSearchParams({
          status: String(status),
          page: String(page),
          per_page: String(perPage),
        })

        const response = await $fetch(`${API_BASE}/receptionist/late-checkout-penalties/verifications?${params.toString()}`, {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        }) as ApiResponse

        if (response.success) {
          return { success: true, message: response.message, data: response.data }
        }
        return { success: false, message: response.message }
      } catch (error: any) {
        return { success: false, message: error?.data?.message || 'Failed to load penalty verifications' }
      }
    },

    async verifyLateCheckoutPenaltyPayment(paymentId: number, payload: any, token: string): Promise<ApiResponse> {
      try {
        const response = await $fetch(`${API_BASE}/receptionist/late-checkout-penalties/${paymentId}/verify`, {
          method: 'POST',
          headers: {
            Authorization: `Bearer ${token}`,
          },
          body: payload,
        }) as ApiResponse

        if (response.success) {
          return { success: true, message: response.message, data: response.data }
        }
        return { success: false, message: response.message }
      } catch (error: any) {
        return { success: false, message: error?.data?.message || 'Failed to verify penalty payment' }
      }
    },

    async getLateCheckoutPenaltyReport(date: string, token: string, page = 1, perPage = 6): Promise<ApiResponse> {
      try {
        const params = new URLSearchParams({
          date,
          page: String(page),
          per_page: String(perPage),
        })

        const response = await $fetch(`${API_BASE}/receptionist/reports/late-checkout-penalties?${params.toString()}`, {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        }) as ApiResponse

        if (response.success) {
          return { success: true, message: response.message, data: response.data }
        }
        return { success: false, message: response.message }
      } catch (error: any) {
        return { success: false, message: error?.data?.message || 'Failed to load penalty report' }
      }
    },
  },
})
