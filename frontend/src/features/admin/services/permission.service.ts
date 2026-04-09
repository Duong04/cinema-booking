import api from '@/shared/api/api'
import type { Permission } from '../types/permission.type'
import type { ApiResponse, PaginatedResponse } from '@/shared/types/apiResponse'

export const permissionService = {
  getAllPermissions: (params?: { page?: number; limit?: number, q?: string }) => api.get<PaginatedResponse<Permission[]>>('/permissions', { params }),
  getPermissionById: (id: string) => api.get<ApiResponse<Permission>>(`/permissions/${id}`),
  createPermission: (payload: Permission) => api.post<ApiResponse<Permission>>('/permissions', payload),
  updatePermission: (id: string, payload: Permission) => api.put<ApiResponse<Permission>>(`/permissions/${id}`, payload),
  deletePermission: (id: string) => api.delete(`/permissions/${id}`),
}
