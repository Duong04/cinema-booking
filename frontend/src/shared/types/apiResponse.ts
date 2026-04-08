export interface Meta {
  total: number
  per_page: number
  current_page: number
  last_page: number
  current_page_url: string
  first_page_url: string
  last_page_url: string
  next_page_url: string
  prev_page_url: string
}

export interface ApiResponse<T> {
  success: boolean
  message: string
  data: T
}

export interface PaginatedResponse<T> extends ApiResponse<T> {
  meta: Meta
}