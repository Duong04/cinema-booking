import { ref, reactive, watch } from 'vue'
import { useDebounceFn } from '@vueuse/core'
import { movieService } from '@/features/admin/services/movie.service'
import type { Movie } from '@/features/admin/types/movie.type'

export function useMovie() {
  const data = ref<Movie[]>([])
  const loading = ref(false)
  const filters = reactive({ search: '' })
  const pagination = reactive({
    page: 1,
    pageSize: 5,
    itemCount: 0,
    showSizePicker: true,
    pageSizes: [3, 5, 7],
    onChange: (page: number) => { pagination.page = page; fetchMovies() },
    onUpdatePageSize: (pageSize: number) => { pagination.pageSize = pageSize; pagination.page = 1; fetchMovies() },
  })

  async function fetchMovies() {
    loading.value = true
    try {
      const res = await movieService.getAllMovies({
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

  async function deleteMovie(id: string) {
      await movieService.deleteMovie(id)
      fetchMovies()
    }

  const debouncedSearch = useDebounceFn(() => {
    pagination.page = 1
    fetchMovies()
  }, 500)

  watch(() => filters.search, debouncedSearch)

  return { data, loading, filters, pagination, fetchMovies, deleteMovie }
}