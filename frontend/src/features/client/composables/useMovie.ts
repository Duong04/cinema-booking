import { ref } from 'vue'
import { movieService } from '@/features/client/services/movie.service'
import type { PublicMovie } from '@/features/client/types/movie.type'

export function useMovie() {
  const topMovies = ref<PublicMovie[]>([])
  const nowPlayingMovies = ref<PublicMovie[]>([])
  const comingSoonMovies = ref<PublicMovie[]>([])
  const relatedMovies = ref<PublicMovie[]>([])
  const selectedMovie = ref<PublicMovie | null>(null)
  const loadingTopMovies = ref(false)
  const loadingNowPlayingMovies = ref(false)
  const loadingComingSoonMovies = ref(false)
  const loadingRelatedMovies = ref(false)
  const loadingMovieDetail = ref(false)

  async function fetchTopMovies() {
    loadingTopMovies.value = true
    try {
      const res = await movieService.getPublicMovies({
        limit: 5,
        sort: 'best_selling',
        status: 'now_showing',
      })

      if (res.data.length > 0) {
        topMovies.value = res.data
      }
    } catch (error) {
      console.error('Failed to load top movies:', error)
    } finally {
      loadingTopMovies.value = false
    }
  }

  async function fetchNowPlayingMovies() {
    loadingNowPlayingMovies.value = true
    try {
      const res = await movieService.getPublicMovies({
        limit: 4,
        status: 'now_showing',
      })

      if (res.data.length > 0) {
        nowPlayingMovies.value = res.data
      }
    } catch (error) {
      console.error('Failed to load now playing movies:', error)
    } finally {
      loadingNowPlayingMovies.value = false
    }
  }

  async function fetchComingSoonMovies() {
    loadingComingSoonMovies.value = true
    try {
      const res = await movieService.getPublicMovies({
        limit: 4,
        status: 'coming_soon',
      })

      if (res.data.length > 0) {
        comingSoonMovies.value = res.data
      }
    } catch (error) {
      console.error('Failed to load coming soon movies:', error)
    } finally {
      loadingComingSoonMovies.value = false
    }
  }

  async function fetchMovieDetail(slug: string) {
    loadingMovieDetail.value = true

    try {
      const res = await movieService.getPublicMovieBySlug(slug)
      selectedMovie.value = res.data
    } catch (error) {
      console.error('Failed to load movie detail:', error)
    } finally {
      loadingMovieDetail.value = false
    }
  }

  async function fetchRelatedMovies(currentMovieId?: string) {
    loadingRelatedMovies.value = true
    try {
      const res = await movieService.getPublicMovies({
        limit: 5,
      })
      const movies = res.data
        .filter((movie) => movie.id !== currentMovieId && movie.slug !== currentMovieId)
        .slice(0, 4)

      if (movies.length > 0) {
        relatedMovies.value = movies
      }
    } catch (error) {
      console.error('Failed to load related movies:', error)
    } finally {
      loadingRelatedMovies.value = false
    }
  }

  return {
    topMovies,
    nowPlayingMovies,
    comingSoonMovies,
    relatedMovies,
    selectedMovie,
    loadingTopMovies,
    loadingNowPlayingMovies,
    loadingComingSoonMovies,
    loadingRelatedMovies,
    loadingMovieDetail,
    fetchTopMovies,
    fetchNowPlayingMovies,
    fetchComingSoonMovies,
    fetchRelatedMovies,
    fetchMovieDetail,
  }
}
