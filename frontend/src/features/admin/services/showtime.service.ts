import api from '@/shared/api/api'
import type { Showtime } from '@/features/admin/types/showtime.type'
import type { ApiResponse, PaginatedResponse } from '@/shared/types/apiResponse'

export const showtimeService = {
  getAllShowtimes: (params?: { page?: number; limit?: number, q?: string, movie_id?: string, room_id?: string, status?: string, show_date?: string}) => api.get<PaginatedResponse<Showtime[]>>('/showtimes', { params }),
  getShowtimeById: (id: string) => api.get<ApiResponse<Showtime>>(`/showtimes/${id}`),
  createShowtime: (payload: Showtime) => api.post<ApiResponse<Showtime>>('/showtimes', payload),
  updateShowtime: (id: string, payload: Showtime) => api.put<ApiResponse<Showtime>>(`/showtimes/${id}`, payload),
  deleteShowtime: (id: string) => api.delete(`/showtimes/${id}`),
}
