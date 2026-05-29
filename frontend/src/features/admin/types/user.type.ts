import type { Role } from "./role.type"

export interface Membership {
  id: string
  user_id: string
  tier: 'bronze' | 'silver' | 'gold' | 'platinum'
  points: number
}

export interface User {
  id: string
  name: string
  email: string
  phone?: string
  avatar?: string
  gender?: Gender
  date_of_birth?: string
  is_active: boolean
  role_id: string
  role: Role
  membership?: Membership | null
  tickets_purchased_count?: number
  created_at?: string
  updated_at?: string
}

export type Gender = 'male' | 'female' | 'other'
