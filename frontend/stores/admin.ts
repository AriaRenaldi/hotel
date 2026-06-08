import { defineStore } from 'pinia'

const API_BASE = 'http://127.0.0.1:8000/api'

interface User {
  id: number
  username: string
  email: string
  full_name: string
  role: string
  phone: string
  address: string
  is_verified: boolean
  photo?: string | null
  photo_url?: string | null
}

interface ApiResponse {
  success: boolean
  message: string
  data?: any
}

export const useAdminStore = defineStore('admin', {
  state: () => ({
    users: [] as User[],
    stats: {
      totalUsers: 0,
      totalBookings: 0,
      totalRooms: 0,
      totalRevenue: 0,
    },
    loading: false,
  }),

  actions: {
    async getStats(token: string): Promise<ApiResponse> {
      try {
        const response = await $fetch(`${API_BASE}/admin/stats`, {
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

    async getAllUsers(token: string): Promise<ApiResponse> {
      try {
        const response = await $fetch(`${API_BASE}/admin/users`, {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        }) as ApiResponse

        if (response.success) {
          this.users = response.data
          return { success: true, message: 'Users loaded', data: response.data }
        } else {
          return { success: false, message: response.message }
        }
      } catch (error: any) {
        return { success: false, message: error?.data?.message || 'Failed to load users' }
      }
    },

    async createReceptionist(userData: any, token: string): Promise<ApiResponse> {
      try {
        const response = await $fetch(`${API_BASE}/admin/users/receptionists`, {
          method: 'POST',
          headers: {
            Authorization: `Bearer ${token}`,
          },
          body: userData,
        }) as ApiResponse

        if (response.success) {
          return { success: true, message: response.message, data: response.data }
        } else {
          return { success: false, message: response.message }
        }
      } catch (error: any) {
        return { success: false, message: error?.data?.message || 'Failed to create receptionist' }
      }
    },

    async updateUser(id: number, userData: any, token: string): Promise<ApiResponse> {
      try {
        const response = await $fetch(`${API_BASE}/admin/users/${id}`, {
          method: 'PUT',
          headers: {
            Authorization: `Bearer ${token}`,
          },
          body: userData,
        }) as ApiResponse

        if (response.success) {
          return { success: true, message: response.message, data: response.data }
        } else {
          return { success: false, message: response.message }
        }
      } catch (error: any) {
        return { success: false, message: error?.data?.message || 'Failed to update user' }
      }
    },

    async deleteUser(id: number, token: string): Promise<ApiResponse> {
      try {
        const response = await $fetch(`${API_BASE}/admin/users/${id}`, {
          method: 'DELETE',
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
        return { success: false, message: error?.data?.message || 'Failed to delete user' }
      }
    },

    async approveUser(id: number, token: string): Promise<ApiResponse> {
      try {
        const response = await $fetch(`${API_BASE}/admin/users/${id}/approve`, {
          method: 'PATCH',
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
        return { success: false, message: error?.data?.message || 'Failed to approve user' }
      }
    },

    async rejectUser(id: number, token: string): Promise<ApiResponse> {
      try {
        const response = await $fetch(`${API_BASE}/admin/users/${id}/reject`, {
          method: 'PATCH',
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
        return { success: false, message: error?.data?.message || 'Failed to reject user' }
      }
    },

    async downloadRevenueReport(startDate: string, endDate: string, token: string): Promise<ApiResponse> {
      try {
        const response = await $fetch(`${API_BASE}/admin/reports/revenue?start_date=${startDate}&end_date=${endDate}`, {
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

    async getLateCheckoutPenaltyReport(startDate: string, endDate: string, token: string, page = 1, perPage = 6, receptionistId = ''): Promise<ApiResponse> {
      try {
        const params = new URLSearchParams({
          start_date: startDate,
          end_date: endDate,
          page: String(page),
          per_page: String(perPage),
        })
        if (receptionistId) {
          params.set('receptionist_id', String(receptionistId))
        }

        const response = await $fetch(`${API_BASE}/admin/reports/late-checkout-penalties?${params.toString()}`, {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        }) as ApiResponse

        if (response.success) {
          return { success: true, message: response.message, data: response.data }
        }
        return { success: false, message: response.message }
      } catch (error: any) {
        return { success: false, message: error?.data?.message || 'Failed to load late checkout penalty report' }
      }
    },

    async downloadLateCheckoutPenaltyReportExcel(startDate: string, endDate: string, token: string, receptionistId = ''): Promise<ApiResponse> {
      try {
        const params = new URLSearchParams({
          start_date: startDate,
          end_date: endDate,
        })
        if (receptionistId) {
          params.set('receptionist_id', String(receptionistId))
        }

        const response = await $fetch(`${API_BASE}/admin/reports/late-checkout-penalties/export/excel?${params.toString()}`, {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        }) as ApiResponse

        if (response.success) {
          return { success: true, message: response.message, data: response.data }
        }
        return { success: false, message: response.message }
      } catch (error: any) {
        return { success: false, message: error?.data?.message || 'Failed to download late checkout penalty report' }
      }
    },
  },
})
