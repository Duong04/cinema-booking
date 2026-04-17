import { ref, reactive, watch } from 'vue'
import { useDebounceFn } from '@vueuse/core'
import { seatTypeService } from '@/features/admin/services/seat-type.service'
import type { SeatType } from '@/features/admin/types/seat-type.type'

export function useSeatType() {
  const data = ref<SeatType[]>([])
  const loading = ref(false)
  const filters = reactive({ search: '' })
  const pagination = reactive({
    page: 1,
    pageSize: 5,
    itemCount: 0,
    showSizePicker: true,
    pageSizes: [3, 5, 7],
    onChange: (page: number) => { pagination.page = page; fetchSeatTypes() },
    onUpdatePageSize: (pageSize: number) => { pagination.pageSize = pageSize; pagination.page = 1; fetchSeatTypes() },
  })

  async function fetchSeatTypes() {
    loading.value = true
    try {
      const res = await seatTypeService.getAllSeatTypes({
        page: pagination.page,
        limit: pagination.pageSize,
        q: filters.search || undefined,
      })
      data.value = res.data
      pagination.itemCount = res.meta.total
    } finally {
      loading.value = false
    }
  }

  async function deleteSeatType(id: string) {
      await seatTypeService.deleteSeatType(id)
      fetchSeatTypes()
    }

  const debouncedSearch = useDebounceFn(() => {
    pagination.page = 1
    fetchSeatTypes()
  }, 500)

  watch(() => filters.search, debouncedSearch)

  return { data, loading, filters, pagination, fetchSeatTypes, deleteSeatType }
}