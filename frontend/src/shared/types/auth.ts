export interface User {
  id: string
  name: string
  email: string
  role: UserRole
  membership?: Membership | null
  tickets_purchased_count?: number
  phone?: string | null
  avatar?: string | null
  date_of_birth?: string | null
  gender?: 'male' | 'female' | 'other' | null
  is_active?: boolean
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
    membership?: Membership | null,
    tickets_purchased_count?: number,
    phone?: string | null,
    avatar?: string | null,
    date_of_birth?: string | null,
    gender?: 'male' | 'female' | 'other' | null,
    is_active?: boolean,
    email_verified_at?: string | null,
    created_at?: string,
    updated_at?: string
  },
  message?: string,
  success?: boolean
}

export interface Membership {
  id: string
  user_id: string
  tier: 'bronze' | 'silver' | 'gold' | 'platinum'
  points: number
}

export interface UserRole {
  id: string
  name: string
  description?: string
  permissions: Permision[]
}

export interface PermissionAction {
  id: string
  key: string
  name: string
}

export interface Permision {
  id: string
  key: string
  name: string
  actions?: PermissionAction[]
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

export interface UpdateProfilePayload {
  name: string
  email: string
  phone?: string | null
  avatar?: string | null
  date_of_birth?: string | null
  gender?: 'male' | 'female' | 'other' | null
}

export interface ChangePasswordPayload {
  current_password: string
  password: string
  password_confirmation: string
}

export interface ValidationError {
  message: string
  errors: Record<string, string[]>
}

export enum EnumUserRole {
  ADMIN = 'super admin',
  CUSTOMER = 'customer',
}
