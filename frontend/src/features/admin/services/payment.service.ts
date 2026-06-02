import api from '@/shared/api/api'
import type { PaginatedResponse } from '@/shared/types/apiResponse'
import type { Payment, PaymentProvider } from '@/features/admin/types/payment.type'
import type { PaymentStatus } from '@/features/admin/types/booking.type'

export const paymentService = {
  getAllPayments: (params?: {
    page?: number
    limit?: number
    q?: string
    status?: PaymentStatus
    provider?: PaymentProvider
    from_date?: string
    to_date?: string
  }) => api.get<PaginatedResponse<Payment[]>>('/payments', { params }),
}
