import { defineStore } from 'pinia'

const API_BASE = 'http://127.0.0.1:8000/api'

interface Booking {
  id: number
  booking_number: string
  check_in_date: string
  check_out_date: string
  total_amount: number
  booking_status: string
  room_type: {
    id: number
    name: string
    price_per_night: number
  }
  rooms_count: number
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

export const useBookingStore = defineStore('booking', {
  state: () => ({
    bookings: [] as Booking[],
    facilities: [] as any[],
    loading: false,
  }),

  actions: {
    async getMyBookings(token?: string): Promise<ApiResponse> {
      try {
        if (!token) {
          return { success: false, message: 'Unauthorized: token tidak tersedia, silakan login kembali.' }
        }

        const response = await $fetch(`${API_BASE}/guest/bookings/my-bookings`, {
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
        if (error?.status === 401) {
          return { success: false, message: 'Unauthorized: sesi login habis, silakan login kembali.' }
        }
        return { success: false, message: formatValidationMessage(error, 'Failed to load bookings') }
      }
    },

    async createBooking(bookingData: any, token: string): Promise<ApiResponse> {
      try {
        if (!token) {
          return { success: false, message: 'Unauthorized: token tidak tersedia, silakan login kembali.' }
        }

        const response = await $fetch(`${API_BASE}/guest/bookings`, {
          method: 'POST',
          headers: {
            Authorization: `Bearer ${token}`,
          },
          body: bookingData,
        }) as ApiResponse

        if (response.success) {
          return { success: true, message: response.message, data: response.data }
        } else {
          return { success: false, message: response.message }
        }
      } catch (error: any) {
        if (error?.status === 401) {
          return { success: false, message: 'Unauthorized: sesi login habis, silakan login kembali.' }
        }
        return { success: false, message: formatValidationMessage(error, 'Failed to create booking') }
      }
    },

    async getFacilities(): Promise<ApiResponse> {
      try {
        const response = await $fetch(`${API_BASE}/hotel/facilities`) as ApiResponse

        if (response.success) {
          this.facilities = response.data
          return { success: true, message: 'Facilities loaded', data: response.data }
        } else {
          return { success: false, message: response.message }
        }
      } catch (error: any) {
        return { success: false, message: error?.data?.message || 'Failed to load facilities' }
      }
    },

    async checkBooking(bookingNumber: string, email: string): Promise<ApiResponse> {
      try {
        const response = await $fetch(`${API_BASE}/hotel/check-booking`, {
          method: 'POST',
          body: { booking_number: bookingNumber, email },
        }) as ApiResponse

        if (response.success) {
          return { success: true, message: response.message, data: response.data }
        } else {
          return { success: false, message: response.message }
        }
      } catch (error: any) {
        return { success: false, message: error?.data?.message || 'Failed to check booking' }
      }
    },

    async getBookingDetail(bookingId: string, token: string): Promise<ApiResponse> {
      try {
        const fetchDetail = async (identifier: string) => {
          return await $fetch(`${API_BASE}/guest/bookings/${encodeURIComponent(identifier)}`, {
            headers: {
              Authorization: `Bearer ${token}`,
            },
          }) as ApiResponse
        }

        let response: ApiResponse
        try {
          response = await fetchDetail(bookingId)
        } catch (error: any) {
          const isNotFound = error?.status === 404 || error?.data?.message?.toLowerCase?.().includes('tidak ditemukan')
          if (!isNotFound) {
            throw error
          }

          // Fallback: resolve by booking number from my bookings, then re-fetch by id
          const myBookings = await this.getMyBookings(token)
          if (myBookings.success && Array.isArray(myBookings.data)) {
            const found = myBookings.data.find((b: any) => String(b.booking_number) === String(bookingId))
            if (found?.id) {
              response = await fetchDetail(String(found.id))
            } else {
              throw error
            }
          } else {
            throw error
          }
        }

        if (response.success) {
          return { success: true, message: 'Booking detail loaded', data: response.data }
        } else {
          return { success: false, message: response.message }
        }
      } catch (error: any) {
        return { success: false, message: error?.data?.message || 'Failed to load booking detail' }
      }
    },

    async getPrintableBooking(bookingId: string, token?: string): Promise<ApiResponse> {
      try {
        const hasToken = Boolean(token)
        const fetchPrintable = async (identifier: string) => {
          if (hasToken) {
            return await $fetch(`${API_BASE}/guest/bookings/${encodeURIComponent(identifier)}/print`, {
              headers: {
                Authorization: `Bearer ${token}`,
              },
            }) as ApiResponse
          }

          return await $fetch(`${API_BASE}/hotel/bookings/${encodeURIComponent(identifier)}/print`) as ApiResponse
        }

        let response: ApiResponse
        try {
          response = await fetchPrintable(bookingId)
        } catch (error: any) {
          const isNotFound = error?.status === 404 || error?.data?.message?.toLowerCase?.().includes('tidak ditemukan')
          if (!isNotFound) {
            throw error
          }

          // Fallback: resolve booking number to id
          if (hasToken) {
            const myBookings = await this.getMyBookings(token as string)
            if (myBookings.success && Array.isArray(myBookings.data)) {
              const found = myBookings.data.find((b: any) => String(b.booking_number) === String(bookingId))
              if (found?.id) {
                response = await fetchPrintable(String(found.id))
              } else {
                throw error
              }
            } else {
              throw error
            }
          } else {
            throw error
          }
        }

        if (response.success) {
          return { success: true, message: 'Printable booking loaded', data: response.data }
        } else {
          return { success: false, message: response.message }
        }
      } catch (error: any) {
        return { success: false, message: error?.data?.message || 'Failed to load printable booking' }
      }
    },

    async uploadPaymentProof(bookingId: string, file: File, paidAmount: number, token: string): Promise<ApiResponse> {
      try {
        const formData = new FormData()
        formData.append('payment_proof', file)
        formData.append('paid_amount', String(paidAmount))

        const response = await $fetch(`${API_BASE}/guest/bookings/${bookingId}/upload-proof`, {
          method: 'POST',
          headers: {
            Authorization: `Bearer ${token}`,
          },
          body: formData,
        }) as ApiResponse

        if (response.success) {
          return { success: true, message: response.message, data: response.data }
        } else {
          return { success: false, message: response.message }
        }
      } catch (error: any) {
        return { success: false, message: error?.data?.message || 'Failed to upload payment proof' }
      }
    },

    async uploadCheckInReceipt(bookingId: string, file: File, token: string): Promise<ApiResponse> {
      try {
        const formData = new FormData()
        formData.append('checkin_receipt', file)

        const response = await $fetch(`${API_BASE}/guest/bookings/${bookingId}/upload-checkin-receipt`, {
          method: 'POST',
          headers: {
            Authorization: `Bearer ${token}`,
          },
          body: formData,
        }) as ApiResponse

        if (response.success) {
          return { success: true, message: response.message, data: response.data }
        }

        return { success: false, message: response.message, data: response.data }
      } catch (error: any) {
        return { success: false, message: formatValidationMessage(error, 'Failed to upload check-in receipt'), data: error?.data?.data }
      }
    },

    async cancelBooking(bookingId: string, token: string): Promise<ApiResponse> {
      try {
        const response = await $fetch(`${API_BASE}/guest/bookings/${bookingId}`, {
          method: 'DELETE',
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
        return { success: false, message: error?.data?.message || 'Failed to cancel booking' }
      }
    },

    async guestCheckOut(bookingId: string, token: string): Promise<ApiResponse> {
      try {
        const response = await $fetch(`${API_BASE}/guest/bookings/${bookingId}/check-out`, {
          method: 'POST',
          headers: {
            Authorization: `Bearer ${token}`,
          },
        }) as ApiResponse

        if (response.success) {
          return { success: true, message: response.message, data: response.data }
        }

        return { success: false, message: response.message }
      } catch (error: any) {
        return { success: false, message: error?.data?.message || 'Gagal melakukan check-out' }
      }
    },

    async getLateCheckoutPenaltyDetail(bookingId: string, token: string): Promise<ApiResponse> {
      try {
        const response = await $fetch(`${API_BASE}/guest/bookings/${bookingId}/late-checkout-penalty`, {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        }) as ApiResponse

        if (response.success) {
          return { success: true, message: 'Detail denda checkout loaded', data: response.data }
        }

        return { success: false, message: response.message }
      } catch (error: any) {
        return { success: false, message: error?.data?.message || 'Gagal mengambil detail denda checkout' }
      }
    },

    async payLateCheckoutPenalty(
      bookingId: string,
      payload: { payment_method: 'qris' | 'cash', paid_amount: number, payment_proof?: File | null },
      token: string
    ): Promise<ApiResponse> {
      try {
        const formData = new FormData()
        formData.append('payment_method', payload.payment_method)
        formData.append('paid_amount', String(payload.paid_amount))
        if (payload.payment_proof) {
          formData.append('payment_proof', payload.payment_proof)
        }

        const response = await $fetch(`${API_BASE}/guest/bookings/${bookingId}/late-checkout-penalty/pay`, {
          method: 'POST',
          headers: {
            Authorization: `Bearer ${token}`,
          },
          body: formData,
        }) as ApiResponse

        if (response.success) {
          return { success: true, message: response.message, data: response.data }
        }

        return { success: false, message: response.message, data: response.data }
      } catch (error: any) {
        return { success: false, message: formatValidationMessage(error, 'Gagal membayar denda checkout'), data: error?.data?.data }
      }
    },
  },
})
