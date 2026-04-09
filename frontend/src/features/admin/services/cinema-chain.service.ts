import api from '@/shared/api/api'
import type { CinemaChain } from '../types/cinema-chain.type'
import type { ApiResponse, PaginatedResponse } from '@/shared/types/apiResponse'

export const cinemaChainService = {
  getAllCinemaChains: (params?: { page?: number; limit?: number, q?: string }) => api.get<PaginatedResponse<CinemaChain[]>>('/cinema-chains', { params }),
  getCinemaChainById: (id: string) => api.get<ApiResponse<CinemaChain>>(`/cinema-chains/${id}`),
  createCinemaChain: (payload: CinemaChain) => api.post<ApiResponse<CinemaChain>>('/cinema-chains', payload),
  updateCinemaChain: (id: string, payload: CinemaChain) => api.put<ApiResponse<CinemaChain>>(`/cinema-chains/${id}`, payload),
  deleteCinemaChain: (id: string) => api.delete(`/cinema-chains/${id}`),
}
