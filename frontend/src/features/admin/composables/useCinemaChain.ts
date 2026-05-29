import { ref, reactive, watch } from 'vue'
import { useDebounceFn } from '@vueuse/core'
import { cinemaChainService } from '@/features/admin/services/cinema-chain.service'
import type { CinemaChain } from '@/features/admin/types/cinema-chain.type'

export function useCinemaChain() {
  const data = ref<CinemaChain[]>([])
  const loading = ref(false)
  const filters = reactive({ search: '' })
  const pagination = reactive({
    page: 1,
    pageSize: 10,
    itemCount: 0,
    showSizePicker: true,
    pageSizes: [3, 5, 7],
    onChange: (page: number) => { pagination.page = page; fetchCinemaChains() },
    onUpdatePageSize: (pageSize: number) => { pagination.pageSize = pageSize; pagination.page = 1; fetchCinemaChains() },
  })

  async function fetchCinemaChains() {
    loading.value = true
    try {
      const res = await cinemaChainService.getAllCinemaChains({
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

  async function deleteCinemaChain(id: string) {
      await cinemaChainService.deleteCinemaChain(id)
      fetchCinemaChains()
    }

  const debouncedSearch = useDebounceFn(() => {
    pagination.page = 1
    fetchCinemaChains()
  }, 500)

  watch(() => filters.search, debouncedSearch)

  return { data, loading, filters, pagination, fetchCinemaChains, deleteCinemaChain }
}