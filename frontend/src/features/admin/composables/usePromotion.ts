import { reactive, ref, watch } from 'vue'
import { useDebounceFn } from '@vueuse/core'
import { promotionService } from '@/features/admin/services/promotion.service'
import type {
  Promotion,
  PromotionApplicableTo,
  PromotionStatus,
} from '@/features/admin/types/promotion.type'

export function usePromotion() {
  const data = ref<Promotion[]>([])
  const loading = ref(false)
  const filters = reactive({
    search: '',
    status: null as PromotionStatus | null,
    applicableTo: null as PromotionApplicableTo | null,
  })

  const pagination = reactive({
    page: 1,
    pageSize: 10,
    itemCount: 0,
    showSizePicker: true,
    pageSizes: [5, 10, 15],
    onChange: (page: number) => {
      pagination.page = page
      fetchPromotions()
    },
    onUpdatePageSize: (pageSize: number) => {
      pagination.pageSize = pageSize
      pagination.page = 1
      fetchPromotions()
    },
  })

  async function fetchPromotions() {
    loading.value = true
    try {
      const res = await promotionService.getAllPromotions({
        page: pagination.page,
        limit: pagination.pageSize,
        q: filters.search || undefined,
        status: filters.status || undefined,
        applicable_to: filters.applicableTo || undefined,
      })
      data.value = res.data
      pagination.itemCount = res.meta.total
    } finally {
      loading.value = false
    }
  }

  async function deletePromotion(id: string) {
    await promotionService.deletePromotion(id)
    fetchPromotions()
  }

  const debouncedSearch = useDebounceFn(() => {
    pagination.page = 1
    fetchPromotions()
  }, 500)

  watch(() => filters.search, debouncedSearch)
  watch(() => [filters.status, filters.applicableTo], () => {
    pagination.page = 1
    fetchPromotions()
  })

  return { data, loading, filters, pagination, fetchPromotions, deletePromotion }
}
