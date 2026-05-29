import api from '@/shared/api/api'
import type { User } from '../types/user.type'
import type { ApiResponse, PaginatedResponse } from '@/shared/types/apiResponse'

export const userService = {
  getAllUsers: (params?: { page?: number; limit?: number, q?: string, role_id?: string, ignore_role_id?: string, is_active?: 1 | 0 }) => api.get<PaginatedResponse<User[]>>('/users', { params }),
  getUserById: (id: string) => api.get<ApiResponse<User>>(`/users/${id}`),
  createUser: (payload: Partial<User> & { password?: string }) => api.post<ApiResponse<User>>('/users', payload),
  updateUser: (id: string, payload: Partial<User> & { password?: string }) => api.put<ApiResponse<User>>(`/users/${id}`, payload),
}
