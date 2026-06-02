import api from '@/shared/api/api'
import type { ApiResponse, PaginatedResponse } from '@/shared/types/apiResponse'
import type { Booking, BookingStatus } from '@/features/admin/types/booking.type'

export const bookingService = {
  getAllBookings: (params?: {
    page?: number
    limit?: number
    q?: string
    status?: BookingStatus
    from_date?: string
    to_date?: string
  }) => api.get<PaginatedResponse<Booking[]>>('/bookings', { params }),

  getBookingById: (id: string) => api.get<ApiResponse<Booking>>(`/bookings/${id}`),

  updateBooking: (id: string, payload: Partial<Booking>) =>
    api.put<ApiResponse<Booking>>(`/bookings/${id}`, payload),

  cancelBooking: (id: string, payload?: { cancellation_reason?: string | null }) =>
    api.put<ApiResponse<Booking>>(`/bookings/${id}/cancel`, payload ?? {}),
}
