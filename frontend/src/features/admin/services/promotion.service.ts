import api from '@/shared/api/api'
import type { ApiResponse, PaginatedResponse } from '@/shared/types/apiResponse'
import type {
  Promotion,
  PromotionApplicableTo,
  PromotionPayload,
  PromotionStatus,
} from '@/features/admin/types/promotion.type'

export const promotionService = {
  getAllPromotions: (params?: {
    page?: number
    limit?: number
    q?: string
    status?: PromotionStatus
    applicable_to?: PromotionApplicableTo
  }) => api.get<PaginatedResponse<Promotion[]>>('/promotions', { params }),
  getPromotionById: (id: string) => api.get<ApiResponse<Promotion>>(`/promotions/${id}`),
  createPromotion: (payload: PromotionPayload) =>
    api.post<ApiResponse<Promotion>>('/promotions', payload),
  updatePromotion: (id: string, payload: PromotionPayload) =>
    api.put<ApiResponse<Promotion>>(`/promotions/${id}`, payload),
  deletePromotion: (id: string) => api.delete(`/promotions/${id}`),
}
