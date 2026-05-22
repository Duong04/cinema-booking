import type { Role } from "./role.type"

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
  created_at?: string
  updated_at?: string
}

export type Gender = 'male' | 'female' | 'other'