import api from '@/shared/api/api'
import type { Seat, CreateSeatPayload } from '@/features/admin/types/seat.type'
import type { ApiResponse } from '@/shared/types/apiResponse'

export const seatService = {
  getSeatByRoomId: (roomId: string) => 
    api.get<ApiResponse<Record<string, Seat[]>>>(`/rooms/${roomId}/seats`),
  
  createSeatByRoomId: (roomId: string, payload: CreateSeatPayload) => 
  api.post<ApiResponse<Seat>>(`/rooms/${roomId}/seats`, payload),
}