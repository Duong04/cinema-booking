import { ref, reactive, watch } from 'vue'
import { useDebounceFn } from '@vueuse/core'
import { showtimeService } from '@/features/admin/services/showtime.service'
import type { Showtime } from '@/features/admin/types/showtime.type'

export function useShowtime() {
  const data = ref<Showtime[]>([])
  const loading = ref(false)
  const filters = reactive({ search: '' })
  const pagination = reactive({
    page: 1,
    pageSize: 5,
    itemCount: 0,
    showSizePicker: true,
    pageSizes: [3, 5, 7],
    onChange: (page: number) => { pagination.page = page; fetchShowtimes() },
    onUpdatePageSize: (pageSize: number) => { pagination.pageSize = pageSize; pagination.page = 1; fetchShowtimes() },
  })

  async function fetchShowtimes() {
    loading.value = true
    try {
      const res = await showtimeService.getAllShowtimes({
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

  async function deleteShowtime(id: string) {
      await showtimeService.deleteShowtime(id)
      fetchShowtimes()
    }

  const debouncedSearch = useDebounceFn(() => {
    pagination.page = 1
    fetchShowtimes()
  }, 500)

  watch(() => filters.search, debouncedSearch)

  return { data, loading, filters, pagination, fetchShowtimes, deleteShowtime }
}