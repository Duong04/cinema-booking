import api from '@/shared/api/api'
import type { Room } from '@/features/admin/types/room.type'
import type { ApiResponse, PaginatedResponse } from '@/shared/types/apiResponse'

export const roomService = {
  getAllRooms: (params?: { page?: number; limit?: number, q?: string, cinema_id?: string }) => api.get<PaginatedResponse<Room[]>>('/rooms', { params }),
  getRoomById: (id: string) => api.get<ApiResponse<Room>>(`/rooms/${id}`),
  createRoom: (payload: Room) => api.post<ApiResponse<Room>>('/rooms', payload),
  updateRoom: (id: string, payload: Room) => api.put<ApiResponse<Room>>(`/rooms/${id}`, payload),
  deleteRoom: (id: string) => api.delete(`/rooms/${id}`),
}
