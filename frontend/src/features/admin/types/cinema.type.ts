import type { City } from './city.type'
import type { CinemaChain } from './cinema-chain.type'

export interface Cinema {
  id: string
  name: string
  address: string
  city_id: string
  cinema_chain_id: string
  created_at?: string
  updated_at?: string
  deleted_at?: string
  city: City
  cinema_chain: CinemaChain
}
