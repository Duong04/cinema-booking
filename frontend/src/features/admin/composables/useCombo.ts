import { reactive, ref, watch } from 'vue'
import { useDebounceFn } from '@vueuse/core'
import { comboService } from '@/features/admin/services/combo.service'
import type { ComboStatus, Combo } from '@/features/admin/types/combo.type'

export function useCombo() {
  const data = ref<Combo[]>([])
  const loading = ref(false)
  const filters = reactive({
    search: '',
    cinemaId: null as string | null,
    status: null as ComboStatus | null,
  })

  const pagination = reactive({
    page: 1,
    pageSize: 10,
    itemCount: 0,
    showSizePicker: true,
    pageSizes: [5, 10, 15],
    onChange: (page: number) => {
      pagination.page = page
      fetchCombos()
    },
    onUpdatePageSize: (pageSize: number) => {
      pagination.pageSize = pageSize
      pagination.page = 1
      fetchCombos()
    },
  })

  async function fetchCombos() {
    loading.value = true
    try {
      const res = await comboService.getAllCombos({
        page: pagination.page,
        limit: pagination.pageSize,
        q: filters.search || undefined,
        cinema_id: filters.cinemaId || undefined,
        status: filters.status || undefined,
      })
      data.value = res.data
      pagination.itemCount = res.meta.total
    } finally {
      loading.value = false
    }
  }

  async function deleteCombo(id: string) {
    await comboService.deleteCombo(id)
    fetchCombos()
  }

  const debouncedSearch = useDebounceFn(() => {
    pagination.page = 1
    fetchCombos()
  }, 500)

  watch(() => filters.search, debouncedSearch)
  watch(() => [filters.cinemaId, filters.status], () => {
    pagination.page = 1
    fetchCombos()
  })

  return { data, loading, filters, pagination, fetchCombos, deleteCombo }
}
