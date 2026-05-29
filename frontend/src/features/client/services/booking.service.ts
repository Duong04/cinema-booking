import api from '@/shared/api/api'
import type { ApiResponse } from '@/shared/types/apiResponse'
import type { ClientBooking } from '@/features/client/types/booking-payment.type'

export interface CreateBookingPayload {
  showtime_id: string
  seat_ids: string[]
  combos?: Array<{
    combo_id: string
    quantity: number
  }>
  promotion_code?: string
}

export const bookingService = {
  create: (payload: CreateBookingPayload) =>
    api.post<ApiResponse<ClientBooking>>('/bookings', payload),
}
