import { ref, reactive, watch } from 'vue'
import { useDebounceFn } from '@vueuse/core'
import { genreService } from '@/features/admin/services/genre.service'
import type { Genre } from '@/features/admin/types/genre.type'

export function useGenre() {
  const data = ref<Genre[]>([])
  const loading = ref(false)
  const filters = reactive({ search: '' })
  const pagination = reactive({
    page: 1,
    pageSize: 5,
    itemCount: 0,
    showSizePicker: true,
    pageSizes: [3, 5, 7],
    onChange: (page: number) => { pagination.page = page; fetchGenres() },
    onUpdatePageSize: (pageSize: number) => { pagination.pageSize = pageSize; pagination.page = 1; fetchGenres() },
  })

  async function fetchGenres() {
    loading.value = true
    try {
      const res = await genreService.getAllGenres({
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

  async function deleteGenre(id: string) {
      await genreService.deleteGenre(id)
      fetchGenres()
    }

  const debouncedSearch = useDebounceFn(() => {
    pagination.page = 1
    fetchGenres()
  }, 500)

  watch(() => filters.search, debouncedSearch)

  return { data, loading, filters, pagination, fetchGenres, deleteGenre }
}