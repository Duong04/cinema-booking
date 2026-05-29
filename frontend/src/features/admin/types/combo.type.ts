import type { Cinema } from './cinema.type'

export type ComboStatus = 'active' | 'inactive'

export interface Combo {
  id: string
  name: string
  description?: string | null
  price: number
  status: ComboStatus
  image?: string | null
  cinema_id: string
  cinema?: Pick<Cinema, 'id' | 'name' | 'address'>
  created_at?: string
  updated_at?: string
  deleted_at?: string
}

export interface ComboPayload {
  name: string
  description?: string
  price: number
  status: ComboStatus
  image?: string
  cinema_id: string
}
