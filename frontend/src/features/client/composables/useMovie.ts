import { ref } from 'vue'
import { MOVIES } from '@/data/mockData'
import { movieService } from '@/features/client/services/movie.service'
import type { ClientMovie, PublicMovie } from '@/features/client/types/movie.type'

const mockTopMovies = [...MOVIES].sort((a, b) => b.rating - a.rating).slice(0, 5)
const mockNowPlayingMovies = MOVIES.filter((movie) => movie.status === 'now-playing').slice(0, 4)
const mockComingSoonMovies = MOVIES.filter((movie) => movie.status === 'coming-soon').slice(0, 4)

const mapPublicMovieToClientMovie = (movie: PublicMovie): ClientMovie => {
  const genres = movie.genres.map((genre) => genre.name)
  const ageRating = String(movie.rating ?? 'P')

  return {
    id: movie.slug ?? movie.id,
    title: movie.title,
    titleVi: movie.title,
    poster: movie.poster_url,
    backdrop: movie.poster_url,
    rating: movie.sold_tickets_count ?? 0,
    duration: movie.duration_minutes,
    genres: genres.length ? genres : ['Movie'],
    genresVi: genres.length ? genres : ['Phim'],
    releaseDate: movie.release_date,
    description: movie.description,
    descriptionVi: movie.description,
    director: '',
    cast: [],
    trailerUrl: movie.trailer_url,
    ageRating,
    status: movie.status === 'now_showing' ? 'now-playing' : 'coming-soon',
    soldTicketsCount: movie.sold_tickets_count ?? 0,
  }
}

export function useMovie() {
  const topMovies = ref<ClientMovie[]>(mockTopMovies)
  const nowPlayingMovies = ref<ClientMovie[]>(mockNowPlayingMovies)
  const comingSoonMovies = ref<ClientMovie[]>(mockComingSoonMovies)
  const relatedMovies = ref<ClientMovie[]>(MOVIES.slice(0, 4))
  const selectedMovie = ref<ClientMovie | null>(null)
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
        topMovies.value = res.data.map(mapPublicMovieToClientMovie)
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
        nowPlayingMovies.value = res.data.map(mapPublicMovieToClientMovie)
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
        comingSoonMovies.value = res.data.map(mapPublicMovieToClientMovie)
      }
    } catch (error) {
      console.error('Failed to load coming soon movies:', error)
    } finally {
      loadingComingSoonMovies.value = false
    }
  }

  async function fetchMovieDetail(slug: string) {
    loadingMovieDetail.value = true
    selectedMovie.value = MOVIES.find((movie) => movie.id === slug) ?? null

    try {
      const res = await movieService.getPublicMovieBySlug(slug)
      selectedMovie.value = mapPublicMovieToClientMovie(res.data)
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
        .map(mapPublicMovieToClientMovie)
        .filter((movie) => movie.id !== currentMovieId)
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
