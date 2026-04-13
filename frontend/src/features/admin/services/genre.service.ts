import api from '@/shared/api/api'
import type { Genre } from '@/features/admin/types/genre.type'
import type { ApiResponse, PaginatedResponse } from '@/shared/types/apiResponse'

export const genreService = {
  getAllGenres: (params?: { page?: number; limit?: number, q?: string }) => api.get<PaginatedResponse<Genre[]>>('/genres', { params }),
  getGenreById: (id: string) => api.get<ApiResponse<Genre>>(`/genres/${id}`),
  createGenre: (payload: Genre) => api.post<ApiResponse<Genre>>('/genres', payload),
  updateGenre: (id: string, payload: Genre) => api.put<ApiResponse<Genre>>(`/genres/${id}`, payload),
  deleteGenre: (id: string) => api.delete(`/genres/${id}`),
}
