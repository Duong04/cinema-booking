import type { SeatType } from "./seat-type.type"

export interface Seat {
  id: string
  room_id: string
  seat_type_id: string
  row_label: string
  seat_number: string
  seat_type: SeatType
  created_at?: string
  updated_at?: string
}

export interface SeatRow {
  label: string
  seats_per_row: number
  seat_type_id: string
}

export interface CreateSeatPayload {
  rows: SeatRow[]
}