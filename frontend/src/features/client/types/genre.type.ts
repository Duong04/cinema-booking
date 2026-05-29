export interface Genre {
  id: string
  name: string
  created_at?: string
  updated_at?: string
}

export interface GenreQuery {
  page?: number
  limit?: number
  q?: string
}
