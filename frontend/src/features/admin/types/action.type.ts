import type { Permission } from '@/features/admin/types/permission.type'

export interface Action {
  id: string
  name: string
  key: string
  permissions?: Permission[]
  created_at?: string
  updated_at?: string
}

export interface ActionPayload {
  name: string
  key: string
  permissions?: Array<{
    permission_id: string
  }>
}
