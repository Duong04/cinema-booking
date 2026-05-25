import type { Action } from '@/features/admin/types/action.type'
import type { Permission } from '@/features/admin/types/permission.type'

export interface RolePermission extends Permission {
  actions: Action[]
}

export interface Role {
  id: string
  name: string
  description?: string
  user_count?: number
  users?: Array<{
    id: string
    name: string
    avatar?: string | null
  }>
  permissions?: RolePermission[]
  created_at?: string
  updated_at?: string
}

export interface RolePayload {
  name: string
  description?: string
  permissions?: Array<{
    id: string
    actions: Array<{
      id: string
    }>
  }>
}
