import { ref } from 'vue'
import { genreService } from '@/features/client/services/genre.service'
import type { Genre } from '@/features/client/types/genre.type'

const fallbackGenres: Genre[] = [
  { id: 'action', name: 'Action' },
  { id: 'comedy', name: 'Comedy' },
  { id: 'drama', name: 'Drama' },
  { id: 'sci-fi', name: 'Sci-Fi' },
  { id: 'horror', name: 'Horror' },
  { id: 'romance', name: 'Romance' },
]

export function useGenre() {
  const genres = ref<Genre[]>(fallbackGenres)
  const loadingGenres = ref(false)

  async function fetchGenres() {
    loadingGenres.value = true
    try {
      const res = await genreService.getPublicGenres({
        limit: 100,
      })

      if (res.data.length > 0) {
        genres.value = res.data
      }
    } catch (error) {
      console.error('Failed to load genres:', error)
    } finally {
      loadingGenres.value = false
    }
  }

  return {
    genres,
    loadingGenres,
    fetchGenres,
  }
}
