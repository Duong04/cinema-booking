import api from '@/shared/api/api'
import type { PaginatedResponse } from '@/shared/types/apiResponse'
import type { PublicCombo } from '@/features/client/types/combo.type'

export const comboService = {
  getActiveCombos: (params?: { cinema_id?: string; limit?: number }) =>
    api.get<PaginatedResponse<PublicCombo[]>>('/public/combos', { params }),
}
