import api from '@/shared/api/api'
import type { Cinema } from '@/features/admin/types/cinema.type'
import type { ApiResponse, PaginatedResponse } from '@/shared/types/apiResponse'

export const cinemaService = {
  getAllCinemas: (params?: { page?: number; limit?: number, q?: string }) => api.get<PaginatedResponse<Cinema[]>>('/cinemas', { params }),
  getCinemaById: (id: string) => api.get<ApiResponse<Cinema>>(`/cinemas/${id}`),
  createCinema: (payload: Cinema) => api.post<ApiResponse<Cinema>>('/cinemas', payload),
  updateCinema: (id: string, payload: Cinema) => api.put<ApiResponse<Cinema>>(`/cinemas/${id}`, payload),
  deleteCinema: (id: string) => api.delete(`/cinemas/${id}`),
}
