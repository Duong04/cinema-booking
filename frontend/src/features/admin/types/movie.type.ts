import type { Genre } from "./genre.type"

export interface Movie {
    id: string
    title: string,
    slug?: string
    duration_minutes: number
    poster_url: string
    trailer_url: string
    description: string
    content: string
    release_date: string
    rating: number
    language?: string
    status: Status
    created_at?: string
    updated_at?: string
    deleted_at?: string
    genres: Genre[]
}

export type MoviePayload = {
  title: string
  duration_minutes: number | null
  poster_url: string
  trailer_url: string
  description: string
  content: string
  release_date: string
  rating: number
  language: string
  status: Status
  genres: string[] 
}

export const STATUS = ['coming_soon', 'now_showing', 'ended', 'cancelled'] as const
export type Status = typeof STATUS[number]
