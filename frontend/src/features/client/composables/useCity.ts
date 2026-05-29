import { ref } from 'vue'
import { cityService } from '@/features/client/services/city.service'
import type { City } from '@/features/client/types/city.type'

export function useCity() {
  const cities = ref<City[]>([])
  const loadingCities = ref(false)

  async function fetchCities() {
    loadingCities.value = true
    try {
      const res = await cityService.getPublicCities({
        limit: 100,
      })

      cities.value = res.data
    } catch (error) {
      console.error('Failed to load cities:', error)
    } finally {
      loadingCities.value = false
    }
  }

  return {
    cities,
    loadingCities,
    fetchCities,
  }
}
