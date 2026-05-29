export type BookingStatus = 'pending' | 'confirmed' | 'cancelled' | 'refunded' | 'expired'
export type PaymentStatus = 'pending' | 'paid' | 'failed' | 'refunded'

export interface BookingUser {
  id: string
  name: string
  email?: string
  avatar?: string | null
}

export interface BookingPayment {
  id: string
  provider: string
  transaction_code?: string | null
  amount: number | string
  status: PaymentStatus
  paid_at?: string | null
  refunded_amount?: number | string | null
  refund_status?: string | null
}

export interface Booking {
  id: string
  user_id: string
  showtime_id: string
  booking_code: string
  total_amount: number | string
  status: BookingStatus
  cancellation_reason?: string | null
  cancelled_at?: string | null
  expired_at?: string | null
  confirmed_at?: string | null
  created_at?: string
  updated_at?: string
  user?: BookingUser | null
  showtime?: {
    id: string
    show_date?: string
    start_time?: string
    movie?: {
      id: string
      title: string
    } | null
    room?: {
      id: string
      name: string
      cinema?: {
        id: string
        name: string
      } | null
    } | null
  } | null
  items?: Array<{
    id: string
    seat_label?: string
    seat_type_name?: string
    price?: number | string
  }>
  combos?: Array<unknown>
  payment?: BookingPayment | null
}
