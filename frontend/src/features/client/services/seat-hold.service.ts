import api from '@/shared/api/api'
import type { ApiResponse } from '@/shared/types/apiResponse'

export interface HoldSeatsPayload {
  showtime_id: string
  seat_ids: string[]
}

export const seatHoldService = {
  hold: (payload: HoldSeatsPayload) =>
    api.post<ApiResponse<null>>('/seat-holds/hold', payload),

  release: (payload: { showtime_id: string }) =>
    api.post<ApiResponse<null>>('/seat-holds/release', payload),
}
