export interface User {
  id: string
  name: string
  email: string
  role: UserRole
  avatar?: string
  email_verified_at?: string | null
  created_at?: string
  updated_at?: string
}

export interface UserProfile {
  data: {
    id: string,
    name: string,
    email: string,
    role: UserRole,
    avatar?: string,
    email_verified_at?: string | null,
    created_at?: string,
    updated_at?: string
  },
  message?: string,
  success?: boolean
}

export interface UserRole {
  id: string
  name: string
  description?: string
  permissions: Permision[]
}

export interface Permision {
  id: string
  key: string
  name: string
}

export interface LoginPayload {
  email: string
  password: string
  remember?: boolean
}

export interface LoginResponse {
  data: User
  message?: string
}

export interface RegisterPayload {
  name: string
  email: string
  password: string
  password_confirmation: string
}

export interface RegisterResponse {
  data: User
  message?: string
}

export interface ValidationError {
  message: string
  errors: Record<string, string[]>
}

export enum EnumUserRole {
  ADMIN = 'super admin',
  CUSTOMER = 'customer',
}