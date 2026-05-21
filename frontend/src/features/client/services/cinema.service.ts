import api from '@/shared/api/api'
import type { PaginatedResponse } from '@/shared/types/apiResponse'
import type { Cinema, CinemaQuery } from '@/features/client/types/cinema.type'

export const cinemaService = {
  getPublicCinemas: (params?: CinemaQuery) =>
    api.get<PaginatedResponse<Cinema[]>>('/public/cinemas', { params }),
}
