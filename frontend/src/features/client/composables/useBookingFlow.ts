import { computed, ref } from 'vue'
import type { PublicShowtime } from '@/features/client/types/showtime.type'

export interface BookingSeat {
  id: string
  row: string
  number: string | number
  label: string
  type: string
  price: number
}

export interface BookingCombo {
  id: string
  name: string
  description?: string | null
  price: number
  image?: string | null
}

export interface BookingComboSelection {
  combo: BookingCombo
  quantity: number
}

interface BookingDraft {
  showtime: PublicShowtime | null
  seats: BookingSeat[]
  combos: BookingComboSelection[]
}

const storageKey = 'cinema_booking_flow'
const draft = ref<BookingDraft>({
  showtime: null,
  seats: [],
  combos: [],
})

function readDraft() {
  if (typeof sessionStorage === 'undefined') return

  try {
    const raw = sessionStorage.getItem(storageKey)
    if (!raw) return

    draft.value = JSON.parse(raw) as BookingDraft
  } catch {
    sessionStorage.removeItem(storageKey)
  }
}

function persistDraft() {
  if (typeof sessionStorage === 'undefined') return

  sessionStorage.setItem(storageKey, JSON.stringify(draft.value))
}

if (typeof sessionStorage !== 'undefined') readDraft()

export function useBookingFlow() {
  const seatTotal = computed(() => draft.value.seats.reduce((total, seat) => total + seat.price, 0))
  const comboTotal = computed(() =>
    draft.value.combos.reduce((total, item) => total + item.combo.price * item.quantity, 0),
  )
  const grandTotal = computed(() => seatTotal.value + comboTotal.value)

  function setShowtime(showtime: PublicShowtime) {
    draft.value = {
      showtime,
      seats: [],
      combos: [],
    }
    persistDraft()
  }

  function setSeats(seats: BookingSeat[]) {
    draft.value.seats = seats
    persistDraft()
  }

  function setCombos(combos: BookingComboSelection[]) {
    draft.value.combos = combos
    persistDraft()
  }

  function clearBooking() {
    draft.value = {
      showtime: null,
      seats: [],
      combos: [],
    }
    if (typeof sessionStorage !== 'undefined') {
      sessionStorage.removeItem(storageKey)
    }
  }

  function formatVND(value: number) {
    return value.toLocaleString('vi-VN') + 'đ'
  }

  return {
    draft,
    seatTotal,
    comboTotal,
    grandTotal,
    setShowtime,
    setSeats,
    setCombos,
    clearBooking,
    formatVND,
  }
}
