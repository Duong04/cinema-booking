import api from '@/shared/api/api'
import type { ApiResponse } from '@/shared/types/apiResponse'
import type {
  PromotionCheckPayload,
  PromotionCheckResult,
} from '@/features/client/types/booking-payment.type'

export const promotionService = {
  check: (payload: PromotionCheckPayload) =>
    api.post<ApiResponse<PromotionCheckResult>>('/promotions/check', payload),
}
