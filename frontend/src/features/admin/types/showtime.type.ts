import type { Movie } from "./movie.type"
import type { Room } from "./room.type"
import type { SeatType } from "./seat-type.type"

export const STATUS = ['scheduled', 'ongoing', 'completed', 'cancelled'] as const
export type Status = typeof STATUS[number]

export interface Prices {
  id: string
  showtime_id: string
  seat_type_id: string
  price: number
  seat_type: SeatType
}

export interface Showtime {
  id: string
  movie_id: string
  room_id: string
  show_date: string
  start_time: string
  end_time: string
  base_price: number
  status: Status
  cancelled_reason?: string
  cancelled_by?: string
  cancelled_at?: string
  created_at?: string
  updated_at?: string
  deleted_at?: string
  movie: Movie
  room: Room
  prices: Prices[]
}

export type ShowtimeSeatStatus = 'available' | 'held' | 'booked'

export interface ShowtimeSeatOverviewItem {
  id: string
  room_id: string
  seat_type_id: string
  row_label: string
  seat_number: string | number
  label: string
  status: ShowtimeSeatStatus
  seat_type?: SeatType
  booking?: {
    id: string
    booking_code?: string
    status?: string
    user?: {
      id: string
      name: string
      email?: string
    } | null
  } | null
  hold?: {
    id: string
    expired_at: string
    user?: {
      id: string
      name: string
      email?: string
    } | null
  } | null
}

export interface ShowtimeSeatOverview {
  showtime: Showtime
  summary: {
    total: number
    booked: number
    held: number
    available: number
  }
  seats: ShowtimeSeatOverviewItem[]
}
