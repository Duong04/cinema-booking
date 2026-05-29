import api from '@/shared/api/api'
import type { PaginatedResponse } from '@/shared/types/apiResponse'
import type { Genre, GenreQuery } from '@/features/client/types/genre.type'

export const genreService = {
  getPublicGenres: (params?: GenreQuery) =>
    api.get<PaginatedResponse<Genre[]>>('/public/genres', { params }),
}
