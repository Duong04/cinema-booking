import api from '@/shared/api/api'
import type { Movie, MoviePayload } from '@/features/admin/types/movie.type'
import type { ApiResponse, PaginatedResponse } from '@/shared/types/apiResponse'

export const movieService = {
  getAllMovies: (params?: { page?: number; limit?: number, q?: string }) => api.get<PaginatedResponse<Movie[]>>('/movies', { params }),
  getMovieById: (id: string) => api.get<ApiResponse<Movie>>(`/movies/${id}`),
  createMovie: (payload: MoviePayload) => api.post<ApiResponse<MoviePayload>>('/movies', payload),
  updateMovie: (id: string, payload: MoviePayload) => api.put<ApiResponse<MoviePayload>>(`/movies/${id}`, payload),
  deleteMovie: (id: string) => api.delete(`/movies/${id}`),
}
