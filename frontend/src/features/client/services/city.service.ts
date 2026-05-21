import api from '@/shared/api/api'
import type { PaginatedResponse } from '@/shared/types/apiResponse'
import type { City, CityQuery } from '@/features/client/types/city.type'

export const cityService = {
  getPublicCities: (params?: CityQuery) =>
    api.get<PaginatedResponse<City[]>>('/public/cities', { params }),
}
