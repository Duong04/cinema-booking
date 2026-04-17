import api from '@/shared/api/api'
import type { SeatType } from '@/features/admin/types/seat-type.type'
import type { ApiResponse, PaginatedResponse } from '@/shared/types/apiResponse'

export const seatTypeService = {
  getAllSeatTypes: (params?: { page?: number; limit?: number, q?: string }) => api.get<PaginatedResponse<SeatType[]>>('/seat-types', { params }),
  getSeatTypeById: (id: string) => api.get<ApiResponse<SeatType>>(`/seat-types/${id}`),
  createSeatType: (payload: SeatType) => api.post<ApiResponse<SeatType>>('/seat-types', payload),
  updateSeatType: (id: string, payload: SeatType) => api.put<ApiResponse<SeatType>>(`/seat-types/${id}`, payload),
  deleteSeatType: (id: string) => api.delete(`/seat-types/${id}`),
}
