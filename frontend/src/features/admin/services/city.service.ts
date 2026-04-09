import api from '@/shared/api/api'
import type { City } from '../types/city.type'
import type { ApiResponse, PaginatedResponse } from '@/shared/types/apiResponse'

export const cityService = {
  getAllCities: (params?: { page?: number; limit?: number, q?: string }) => api.get<PaginatedResponse<City[]>>('/cities', { params }),
  getCityById: (id: string) => api.get<ApiResponse<City>>(`/cities/${id}`),
  createCity: (payload: City) => api.post<ApiResponse<City>>('/cities', payload),
  updateCity: (id: string, payload: City) => api.put<ApiResponse<City>>(`/cities/${id}`, payload),
  deleteCity: (id: string) => api.delete(`/cities/${id}`),
}
