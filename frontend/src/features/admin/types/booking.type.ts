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

export interface BookingItem {
  id: string
  seat_label?: string
  seat_type_name?: string
  movie_title?: string
  room_name?: string
  price?: number | string
}

export interface BookingCombo {
  id?: string
  name?: string
  combo_name?: string
  quantity?: number
  unit_price?: number | string
  total_price?: number | string
  pivot?: {
    combo_name?: string
    quantity?: number
    unit_price?: number | string
    total_price?: number | string
  }
}

export interface BookingPromotion {
  id?: string
  code?: string
  name?: string
  pivot?: {
    discount_amount?: number | string
    used_at?: string | null
  }
}

export interface BookingStatusLog {
  id?: string
  old_status?: BookingStatus | null
  new_status?: BookingStatus
  changed_at?: string
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
  items?: BookingItem[]
  combos?: BookingCombo[]
  promotions?: BookingPromotion[]
  status_logs?: BookingStatusLog[]
  statusLogs?: BookingStatusLog[]
  payment?: BookingPayment | null
}
