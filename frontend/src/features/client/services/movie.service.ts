import api from '@/shared/api/api'
import type { ApiResponse, PaginatedResponse } from '@/shared/types/apiResponse'
import type { PublicMovie, PublicMovieQuery } from '@/features/client/types/movie.type'

export const movieService = {
  getPublicMovies: (params?: PublicMovieQuery) =>
    api.get<PaginatedResponse<PublicMovie[]>>('/public/movies', { params }),
  getPublicMovieBySlug: (slug: string) =>
    api.get<ApiResponse<PublicMovie>>(`/public/movies/${slug}`),
}
