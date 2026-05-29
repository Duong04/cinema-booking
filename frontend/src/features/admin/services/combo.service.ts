import api from '@/shared/api/api'
import type { ApiResponse, PaginatedResponse } from '@/shared/types/apiResponse'
import type { ComboStatus, Combo, ComboPayload } from '@/features/admin/types/combo.type'

export const comboService = {
  getAllCombos: (params?: { page?: number; limit?: number; q?: string; cinema_id?: string; status?: ComboStatus }) =>
    api.get<PaginatedResponse<Combo[]>>('/combos', { params }),
  getComboById: (id: string) => api.get<ApiResponse<Combo>>(`/combos/${id}`),
  createCombo: (payload: ComboPayload) => api.post<ApiResponse<Combo>>('/combos', payload),
  updateCombo: (id: string, payload: ComboPayload) => api.put<ApiResponse<Combo>>(`/combos/${id}`, payload),
  deleteCombo: (id: string) => api.delete(`/combos/${id}`),
}
