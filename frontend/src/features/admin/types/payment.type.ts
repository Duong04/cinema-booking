import type { Booking, PaymentStatus } from './booking.type'

export type PaymentProvider = 'vnpay' | 'momo' | 'zalopay' | 'cashier'

export interface Payment {
  id: string
  booking_id: string
  provider: PaymentProvider | string
  transaction_code?: string | null
  amount: number | string
  status: PaymentStatus
  paid_at?: string | null
  idempotency_key?: string | null
  refunded_amount?: number | string | null
  refund_status?: string | null
  created_at?: string
  updated_at?: string
  booking?: Booking | null
}
