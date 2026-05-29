import api from '@/shared/api/api'
import type { ApiResponse, PaginatedResponse } from '@/shared/types/apiResponse'
import type {
  PublicShowtime,
  PublicShowtimeSeatOverview,
  ShowtimeQuery,
} from '@/features/client/types/showtime.type'

export const showtimeService = {
  getPublicShowtimes: (params?: ShowtimeQuery) =>
    api.get<PaginatedResponse<PublicShowtime[]>>('/public/showtimes', { params }),
  getPublicShowtimeById: (id: string) =>
    api.get<ApiResponse<PublicShowtime>>(`/public/showtimes/${id}`),
  getPublicSeatOverview: (id: string) =>
    api.get<ApiResponse<PublicShowtimeSeatOverview>>(`/public/showtimes/${id}/seat-overview`),
}
