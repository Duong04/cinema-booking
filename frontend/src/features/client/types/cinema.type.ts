import type { City } from '@/features/client/types/city.type'

export interface CinemaChain {
  id: string
  name: string
  logo?: string | null
}

export interface Cinema {
  id: string
  name: string
  address: string
  city_id: string
  cinema_chain_id: string
  city?: City | null
  cinema_chain?: CinemaChain | null
  cinemaChain?: CinemaChain | null
  created_at?: string
  updated_at?: string
}

export interface CinemaQuery {
  page?: number
  limit?: number
  q?: string
  city_id?: string
  cinema_chain_id?: string
}
