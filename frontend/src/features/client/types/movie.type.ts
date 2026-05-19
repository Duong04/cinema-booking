import type { Movie as DisplayMovie } from '@/data/mockData'

export type PublicMovieStatus = 'now_showing' | 'coming_soon'
export type PublicMovieSort = 'created_at_desc' | 'best_selling'
export type PublicMoviePeriod = '7d' | '30d' | 'all'

export interface PublicMovieGenre {
  id: string
  name: string
}

export interface PublicMovie {
  id: string
  title: string
  slug?: string
  duration_minutes: number
  poster_url: string
  trailer_url: string
  description: string
  content: string
  release_date: string
  rating?: string | number | null
  language?: string
  status: PublicMovieStatus
  sold_tickets_count?: number | null
  genres: PublicMovieGenre[]
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

export type ClientMovie = DisplayMovie & {
  soldTicketsCount?: number
}
