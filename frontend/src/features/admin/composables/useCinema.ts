import { ref, reactive, watch } from 'vue'
import { useDebounceFn } from '@vueuse/core'
import { cinemaService } from '@/features/admin/services/cinema.service'
import type { Cinema } from '@/features/admin/types/cinema.type'

export function useCinema() {
  const data = ref<Cinema[]>([])
  const loading = ref(false)
  const filters = reactive({ search: '' })
  const pagination = reactive({
    page: 1,
    pageSize: 10,
    itemCount: 0,
    showSizePicker: true,
    pageSizes: [3, 5, 7],
    onChange: (page: number) => { pagination.page = page; fetchCinemas() },
    onUpdatePageSize: (pageSize: number) => { pagination.pageSize = pageSize; pagination.page = 1; fetchCinemas() },
  })

  async function fetchCinemas() {
    loading.value = true
    try {
      const res = await cinemaService.getAllCinemas({
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

  async function deleteCinema(id: string) {
      await cinemaService.deleteCinema(id)
      fetchCinemas()
    }

  const debouncedSearch = useDebounceFn(() => {
    pagination.page = 1
    fetchCinemas()
  }, 500)

  watch(() => filters.search, debouncedSearch)

  return { data, loading, filters, pagination, fetchCinemas, deleteCinema }
}