import type { PublicShowtime } from '@/features/client/types/showtime.type'
import type {
  BookingComboSelection,
  BookingSeat,
} from '@/features/client/composables/useBookingFlow'

export type PaymentProvider = 'vnpay' | 'momo' | 'zalopay' | 'cashier'

export interface ClientBookingItem {
  id?: string
  seat_label: string
  price?: string | number
  seat_type_name?: string
  movie_title?: string
  room_name?: string
}

export interface ClientBookingCombo {
  id: string
  name: string
  pivot?: {
    combo_name?: string
    quantity?: number
    unit_price?: string | number
    total_price?: string | number
  }
}

export interface ClientBooking {
  id: string
  booking_code: string
  total_amount: string | number
  status: 'pending' | 'confirmed' | 'cancelled' | 'expired' | 'refunded'
  showtime?: PublicShowtime
  items?: ClientBookingItem[]
  combos?: ClientBookingCombo[]
  payment?: ClientPayment
}

export interface ClientPayment {
  id: string
  booking_id: string
  provider: PaymentProvider
  transaction_code: string
  amount: string | number
  status: 'pending' | 'paid' | 'failed' | 'refunded' | 'partially_refunded'
  paid_at?: string | null
  booking?: ClientBooking
}

export interface BookingResultSummary {
  id: string
  movieTitle?: string
  cinemaName?: string
  roomName?: string
  showDate?: string
  startTime?: string
  seats: BookingSeat[]
  combos: BookingComboSelection[]
  seatTotal?: number
  comboTotal?: number
  totalPrice: number
  paymentMethod: string
}

export interface PaymentRouteState {
  payment?: ClientPayment
  bookingSummary?: BookingResultSummary
  qrContent?: string
}
