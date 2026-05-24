import api from '@/shared/api/api'
import type { ApiResponse } from '@/shared/types/apiResponse'
import type {
  ClientBooking,
  ClientPayment,
  PaymentProvider,
} from '@/features/client/types/booking-payment.type'

export type { PaymentProvider }

export interface PaymentResponse {
  payment: ClientPayment
  payment_url: string
  qr_content: string
}

export const paymentService = {
  create: (payload: { booking_id: string; provider: PaymentProvider }) =>
    api.post<ApiResponse<PaymentResponse>>('/payments', payload),

  confirm: (paymentId: string) =>
    api.post<ApiResponse<{ payment: ClientPayment; booking: ClientBooking }>>(`/payments/${paymentId}/confirm`),
}
