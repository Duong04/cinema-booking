export interface User {
  id: number
  name: string
  email: string
  role: UserRole
  avatar?: string
  email_verified_at?: string | null
  created_at?: string
  updated_at?: string
}

export type UserRole = 'admin' | 'client'

export interface LoginPayload {
  email: string
  password: string
  remember?: boolean
}

export interface LoginResponse {
  user: User
  message?: string
}

export interface RegisterPayload {
  name: string
  email: string
  password: string
  password_confirmation: string   
}

export interface RegisterResponse {
  user: User
  message?: string
}

export interface ValidationError {
  message: string
  errors: Record<string, string[]>  
}