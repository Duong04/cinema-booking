export type PromotionDiscountType = 'percentage' | 'fixed_amount'
export type PromotionApplicableTo = 'booking' | 'ticket' | 'combo'
export type PromotionStatus = 'active' | 'paused' | 'expired'

export interface Promotion {
  id: string
  code: string
  description?: string | null
  discount_type: PromotionDiscountType
  discount_value: number | string
  start_date: string
  end_date: string
  usage_limit?: number | null
  per_user_limit?: number | null
  applicable_to: PromotionApplicableTo
  status: PromotionStatus
  created_at?: string
  updated_at?: string
}

export interface PromotionPayload {
  code: string
  description?: string | null
  discount_type: PromotionDiscountType
  discount_value: number
  start_date: string
  end_date: string
  usage_limit?: number | null
  per_user_limit?: number | null
  applicable_to: PromotionApplicableTo
  status: PromotionStatus
}
