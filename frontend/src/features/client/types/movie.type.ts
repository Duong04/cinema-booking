import type { Genre } from '@/features/client/types/genre.type'

export type PublicMovieStatus = 'now_showing' | 'coming_soon'
export type PublicMovieSort =
  | 'created_at_desc'
  | 'best_selling'
  | 'top_rated'
  | 'release_date_desc'
  | 'duration_desc'
export type PublicMoviePeriod = '7d' | '30d' | 'all'

export interface PublicMovie {
  id: string
  title: string
  slug?: string
  duration_minutes: number
  poster_url: string
  banner_url?: string | null
  trailer_url: string
  description: string
  content: string
  release_date?: string | null
  rating?: string | number | null
  rating_count?: number | null
  rating_score?: number | null
  language?: string
  status: PublicMovieStatus
  sold_tickets_count?: number | null
  genres: Genre[]
}

export interface PublicMovieQuery {
  page?: number
  limit?: number
  q?: string
  status?: PublicMovieStatus
  genre_id?: string
  sort?: PublicMovieSort
  period?: PublicMoviePeriod
}
