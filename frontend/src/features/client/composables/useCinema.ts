import { ref } from 'vue'
import { cinemaService } from '@/features/client/services/cinema.service'
import type { Cinema, CinemaQuery } from '@/features/client/types/cinema.type'
import type { Meta } from '@/shared/types/apiResponse'

export function useCinema() {
  const cinemas = ref<Cinema[]>([])
  const cinemasMeta = ref<Meta | null>(null)
  const loadingCinemas = ref(false)

  async function fetchCinemas(params?: CinemaQuery) {
    loadingCinemas.value = true
    try {
      const res = await cinemaService.getPublicCinemas({
        limit: 9,
        ...params,
      })

      cinemas.value = res.data
      cinemasMeta.value = res.meta
    } catch (error) {
      console.error('Failed to load cinemas:', error)
    } finally {
      loadingCinemas.value = false
    }
  }

  return {
    cinemas,
    cinemasMeta,
    loadingCinemas,
    fetchCinemas,
  }
}
