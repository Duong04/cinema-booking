import type { PublicMovie } from '@/features/client/types/movie.type'
import type { Cinema } from '@/features/client/types/cinema.type'

export interface ShowtimeSeatType {
  id: string
  name: string
  base_multiplier?: string | number
}

export interface ShowtimePrice {
  id: string
  showtime_id?: string
  seat_type_id?: string
  price?: string | number
  seat_type?: ShowtimeSeatType
  seatType?: ShowtimeSeatType
}

export interface ShowtimeRoom {
  id: string
  name: string
  type?: string
  cinema?: Cinema | null
}

export interface PublicShowtime {
  id: string
  movie_id: string
  room_id: string
  show_date: string
  start_time: string
  end_time: string
  base_price: string | number
  status: 'scheduled' | 'ongoing'
  movie?: PublicMovie
  room?: ShowtimeRoom
  prices?: ShowtimePrice[]
}

export interface ShowtimeQuery {
  page?: number
  limit?: number
  movie_id?: string
  cinema_id?: string
  city_id?: string
  cinema_chain_id?: string
  show_date?: string
  from_date?: string
  to_date?: string
  status?: 'scheduled' | 'ongoing'
}

export type PublicShowtimeSeatStatus = 'available' | 'held' | 'booked'

export interface PublicShowtimeSeat {
  id: string
  room_id: string
  seat_type_id: string
  row_label: string
  seat_number: string | number
  label: string
  status: PublicShowtimeSeatStatus
  seat_type?: ShowtimeSeatType
}

export interface PublicShowtimeSeatOverview {
  showtime: PublicShowtime
  summary: {
    total: number
    booked: number
    held: number
    available: number
  }
  seats: PublicShowtimeSeat[]
}
