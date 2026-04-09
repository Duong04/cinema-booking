import api from '@/shared/api/api'
import type { Action } from '../types/action.type'
import type { ApiResponse, PaginatedResponse } from '@/shared/types/apiResponse'

export const actionService = {
  getAllActions: (params?: { page?: number; limit?: number, q?: string }) => api.get<PaginatedResponse<Action[]>>('/actions', { params }),
  getActionById: (id: string) => api.get<ApiResponse<Action>>(`/actions/${id}`),
  createAction: (payload: Action) => api.post<ApiResponse<Action>>('/actions', payload),
  updateAction: (id: string, payload: Action) => api.put<ApiResponse<Action>>(`/actions/${id}`, payload),
  deleteAction: (id: string) => api.delete(`/actions/${id}`),
}
