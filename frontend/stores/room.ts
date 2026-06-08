import { defineStore } from 'pinia'

const API_BASE = 'http://127.0.0.1:8000/api'

interface RoomType {
  id: number
  name: string
  description: string
  price_per_night: number
  max_occupancy: number
  facilities: string[]
}

interface Room {
  id: number
  room_number: string
  room_type_id: number
  status: string
  room_type: RoomType
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

const hasFileField = (payload: Record<string, any> | null | undefined) =>
  !!payload && typeof payload === 'object' && Object.values(payload).some((value) => value instanceof File)

const toFormData = (payload: Record<string, any>) => {
  const formData = new FormData()

  Object.entries(payload).forEach(([key, value]) => {
    if (value === undefined || value === null || value === '') return
    formData.append(key, value as string | Blob)
  })

  return formData
}

export const useRoomStore = defineStore('room', {
  state: () => ({
    roomTypes: [] as RoomType[],
    rooms: [] as Room[],
    loading: false,
  }),

  actions: {
    async getRoomTypes(): Promise<ApiResponse> {
      try {
        const response = await $fetch(`${API_BASE}/hotel/room-types`) as ApiResponse

        if (response.success) {
          this.roomTypes = response.data
          return { success: true, message: 'Room types loaded', data: response.data }
        } else {
          return { success: false, message: response.message }
        }
      } catch (error: any) {
        return { success: false, message: error?.data?.message || 'Failed to load room types' }
      }
    },

    async getRoomTypeDetail(id: number): Promise<ApiResponse> {
      try {
        const response = await $fetch(`${API_BASE}/hotel/room-types/${id}`) as ApiResponse

        if (response.success) {
          return { success: true, message: 'Room type detail loaded', data: response.data }
        } else {
          return { success: false, message: response.message }
        }
      } catch (error: any) {
        return { success: false, message: error?.data?.message || 'Failed to load room type detail' }
      }
    },

    async getAllRoomTypes(token: string): Promise<ApiResponse> {
      try {
        const response = await $fetch(`${API_BASE}/admin/room-types`, {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        }) as ApiResponse

        if (response.success) {
          this.roomTypes = response.data
          return { success: true, message: 'Room types loaded', data: response.data }
        } else {
          return { success: false, message: response.message }
        }
      } catch (error: any) {
        return { success: false, message: error?.data?.message || 'Failed to load room types' }
      }
    },

    async createRoomType(roomTypeData: any, token: string): Promise<ApiResponse> {
      try {
        const useMultipart = roomTypeData instanceof FormData || hasFileField(roomTypeData)
        const body = roomTypeData instanceof FormData
          ? roomTypeData
          : useMultipart
            ? toFormData(roomTypeData)
            : roomTypeData

        const response = await $fetch(`${API_BASE}/admin/room-types`, {
          method: 'POST',
          headers: {
            Authorization: `Bearer ${token}`,
          },
          body,
        }) as ApiResponse

        if (response.success) {
          return { success: true, message: response.message, data: response.data }
        } else {
          return { success: false, message: response.message }
        }
      } catch (error: any) {
        return { success: false, message: error?.data?.message || 'Failed to create room type' }
      }
    },

    async updateRoomType(id: number, roomTypeData: any, token: string): Promise<ApiResponse> {
      try {
        const useMultipart = roomTypeData instanceof FormData || hasFileField(roomTypeData)
        const body = roomTypeData instanceof FormData
          ? roomTypeData
          : useMultipart
            ? toFormData(roomTypeData)
            : roomTypeData

        if (useMultipart && body instanceof FormData && !body.has('_method')) {
          body.append('_method', 'PUT')
        }

        const response = await $fetch(`${API_BASE}/admin/room-types/${id}`, {
          method: useMultipart ? 'POST' : 'PUT',
          headers: {
            Authorization: `Bearer ${token}`,
          },
          body,
        }) as ApiResponse

        if (response.success) {
          return { success: true, message: response.message, data: response.data }
        } else {
          return { success: false, message: response.message }
        }
      } catch (error: any) {
        return { success: false, message: error?.data?.message || 'Failed to update room type' }
      }
    },

    async deleteRoomType(id: number, token: string): Promise<ApiResponse> {
      try {
        const response = await $fetch(`${API_BASE}/admin/room-types/${id}`, {
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
        return { success: false, message: error?.data?.message || 'Failed to delete room type' }
      }
    },

    async getAllRooms(token: string): Promise<ApiResponse> {
      try {
        const response = await $fetch(`${API_BASE}/admin/rooms`, {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        }) as ApiResponse

        if (response.success) {
          this.rooms = response.data
          return { success: true, message: 'Rooms loaded', data: response.data }
        } else {
          return { success: false, message: response.message }
        }
      } catch (error: any) {
        return { success: false, message: error?.data?.message || 'Failed to load rooms' }
      }
    },

    async createRoom(roomData: any, token: string): Promise<ApiResponse> {
      try {
        const useMultipart = roomData instanceof FormData || hasFileField(roomData)
        const body = roomData instanceof FormData
          ? roomData
          : useMultipart
            ? toFormData(roomData)
            : roomData

        const response = await $fetch(`${API_BASE}/admin/rooms`, {
          method: 'POST',
          headers: {
            Authorization: `Bearer ${token}`,
          },
          body,
        }) as ApiResponse

        if (response.success) {
          return { success: true, message: response.message, data: response.data }
        } else {
          return { success: false, message: response.message }
        }
      } catch (error: any) {
        return { success: false, message: error?.data?.message || 'Failed to create room' }
      }
    },

    async updateRoom(id: number, roomData: any, token: string): Promise<ApiResponse> {
      try {
        const useMultipart = roomData instanceof FormData || hasFileField(roomData)
        const body = roomData instanceof FormData
          ? roomData
          : useMultipart
            ? toFormData(roomData)
            : roomData

        if (useMultipart && body instanceof FormData && !body.has('_method')) {
          body.append('_method', 'PUT')
        }

        const response = await $fetch(`${API_BASE}/admin/rooms/${id}`, {
          method: useMultipart ? 'POST' : 'PUT',
          headers: {
            Authorization: `Bearer ${token}`,
          },
          body,
        }) as ApiResponse

        if (response.success) {
          return { success: true, message: response.message, data: response.data }
        } else {
          return { success: false, message: response.message }
        }
      } catch (error: any) {
        return { success: false, message: error?.data?.message || 'Failed to update room' }
      }
    },

    async deleteRoom(id: number, token: string): Promise<ApiResponse> {
      try {
        const response = await $fetch(`${API_BASE}/admin/rooms/${id}`, {
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
        return { success: false, message: error?.data?.message || 'Failed to delete room' }
      }
    },

    async checkAvailability(checkIn: string, checkOut: string, roomTypeId: number | null, totalRooms: number): Promise<ApiResponse> {
      try {
        const response = await $fetch(`${API_BASE}/hotel/check-availability`, {
          method: 'POST',
          body: {
            check_in: checkIn,
            check_out: checkOut,
            room_type_id: roomTypeId,
            total_rooms: totalRooms,
          },
        }) as ApiResponse

        if (response.success) {
          return { success: true, message: response.message, data: response.data }
        } else {
          return { success: false, message: response.message, data: response.data }
        }
      } catch (error: any) {
        return {
          success: false,
          message: formatValidationMessage(error, 'Failed to check availability'),
          data: error?.data?.data
        }
      }
    },
  },
})
