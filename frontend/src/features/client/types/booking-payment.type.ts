import type { PublicShowtime } from '@/features/client/types/showtime.type'
import type {
  BookingComboSelection,
  BookingSeat,
} from '@/features/client/composables/useBookingFlow'

export type PaymentProvider = 'vnpay' | 'momo' | 'zalopay'

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

export interface ClientBookingPromotion {
  id: string
  code: string
  description?: string | null
  discount_type?: 'percentage' | 'fixed_amount'
  discount_value?: string | number
  pivot?: {
    discount_amount?: string | number
    used_at?: string | null
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
  promotions?: ClientBookingPromotion[]
  payment?: ClientPayment
}

export interface PromotionCheckPayload {
  code: string
  ticket_amount?: number
  combo_amount?: number
}

export interface PromotionCheckResult {
  promotion: {
    id: string
    code: string
    description?: string | null
    discount_type: 'percentage' | 'fixed_amount'
    discount_value: number
    applicable_to?: 'booking' | 'ticket' | 'combo' | null
    end_date?: string | null
  }
  ticket_amount: number
  combo_amount: number
  subtotal: number
  discount_amount: number
  total_amount: number
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
  subtotal?: number
  discountAmount?: number
  promotionCode?: string
  totalPrice: number
  paymentMethod: string
}

export interface PaymentRouteState {
  payment?: ClientPayment
  bookingSummary?: BookingResultSummary
  qrContent?: string
}

export interface PaymentResultRouteState {
  booking?: BookingResultSummary
}
