import { ref, reactive, watch } from 'vue'
import { useDebounceFn } from '@vueuse/core'
import { cityService } from '@/features/admin/services/city.service'
import type { City } from '@/features/admin/types/city.type'

export function useCity() {
  const data = ref<City[]>([])
  const loading = ref(false)
  const filters = reactive({ search: '' })
  const pagination = reactive({
    page: 1,
    pageSize: 5,
    itemCount: 0,
    showSizePicker: true,
    pageSizes: [3, 5, 7],
    onChange: (page: number) => { pagination.page = page; fetchCities() },
    onUpdatePageSize: (pageSize: number) => { pagination.pageSize = pageSize; pagination.page = 1; fetchCities() },
  })

  async function fetchCities() {
    loading.value = true
    try {
      const res = await cityService.getAllCities({
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

  async function deleteCity(id: string) {
      await cityService.deleteCity(id)
      fetchCities()
    }

  const debouncedSearch = useDebounceFn(() => {
    pagination.page = 1
    fetchCities()
  }, 500)

  watch(() => filters.search, debouncedSearch)

  return { data, loading, filters, pagination, fetchCities, deleteCity }
}