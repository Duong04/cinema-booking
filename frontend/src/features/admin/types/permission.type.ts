import type { Action } from '@/features/admin/types/action.type'

export interface Permission {
  id: string
  name: string
  key: string
  actions?: Action[]
  created_at?: string
  updated_at?: string
}

export interface PermissionPayload {
  name: string
  key: string
  actions?: Array<{
    action_id: string
  }>
}
