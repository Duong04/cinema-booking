import api from '@/shared/api/api'
import type { Role, RolePayload } from '@/features/admin/types/role.type'
import type { ApiResponse, PaginatedResponse } from '@/shared/types/apiResponse'

export const roleService = {
  getAllRoles: (params?: { page?: number; limit?: number, q?: string }) => api.get<PaginatedResponse<Role[]>>('/roles', { params }),
  getRoleById: (id: string) => api.get<ApiResponse<Role>>(`/roles/${id}`),
  createRole: (payload: RolePayload) => api.post<ApiResponse<Role>>('/roles', payload),
  updateRole: (id: string, payload: RolePayload) => api.put<ApiResponse<Role>>(`/roles/${id}`, payload),
  deleteRole: (id: string) => api.delete(`/roles/${id}`),
}
