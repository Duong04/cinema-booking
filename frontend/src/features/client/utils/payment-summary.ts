import type {
  BookingResultSummary,
  ClientBooking,
  ClientPayment,
} from '@/features/client/types/booking-payment.type'

export function paymentProviderLabel(provider?: ClientPayment['provider']) {
  if (provider === 'momo') return 'MoMo'
  if (provider === 'zalopay') return 'ZaloPay'
  if (provider === 'cashier') return 'Cashier'
  return 'VNPay'
}

export function buildBookingSummaryFromBooking(
  booking: ClientBooking,
  payment?: ClientPayment,
): BookingResultSummary {
  const promotion = booking.promotions?.[0]
  const discountAmount = Number(promotion?.pivot?.discount_amount ?? 0)
  const totalPrice = Number(booking.total_amount)

  return {
    id: booking.booking_code,
    movieTitle: booking.showtime?.movie?.title,
    cinemaName: booking.showtime?.room?.cinema?.name,
    roomName: booking.showtime?.room?.name,
    showDate: booking.showtime?.show_date,
    startTime: booking.showtime?.start_time,
    seats: booking.items?.map((item) => ({
      id: item.id ?? item.seat_label,
      row: item.seat_label.slice(0, 1),
      number: item.seat_label.slice(1),
      label: item.seat_label,
      type: item.seat_type_name ?? '',
      price: Number(item.price ?? 0),
    })) ?? [],
    combos: [],
    discountAmount,
    promotionCode: promotion?.code,
    subtotal: discountAmount > 0 ? totalPrice + discountAmount : undefined,
    totalPrice,
    paymentMethod: paymentProviderLabel(payment?.provider ?? booking.payment?.provider),
  }
}

const resultStorageKey = 'cinema_payment_result'

export function savePaymentResult(summary: BookingResultSummary) {
  if (typeof sessionStorage === 'undefined') return
  sessionStorage.setItem(resultStorageKey, JSON.stringify(summary))
}

export function readPaymentResult(): BookingResultSummary | null {
  if (typeof sessionStorage === 'undefined') return null

  try {
    const raw = sessionStorage.getItem(resultStorageKey)
    return raw ? (JSON.parse(raw) as BookingResultSummary) : null
  } catch {
    sessionStorage.removeItem(resultStorageKey)
    return null
  }
}
