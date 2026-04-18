import api from '@/shared/api/api'
import type { Seat, CreateSeatPayload, UpdateSeatPayload } from '@/features/admin/types/seat.type'
import type { ApiResponse } from '@/shared/types/apiResponse'

export const seatService = {
  getSeatByRoomId: (roomId: string) =>
    api.get<ApiResponse<Record<string, Seat[]>>>(`/rooms/${roomId}/seats`),

  createSeatByRoomId: (roomId: string, payload: CreateSeatPayload) =>
    api.post<ApiResponse<Seat>>(`/rooms/${roomId}/seats`, payload),

  updateRow: (roomId: string, rowLabel: string, payload: UpdateSeatPayload) =>
    api.put<ApiResponse<Seat>>(`/rooms/${roomId}/seats/${rowLabel}`, payload),

  deleteRow: (roomId: string, rowLabel: string) =>
    api.delete<ApiResponse<Seat>>(`/rooms/${roomId}/seats/${rowLabel}`),
}
