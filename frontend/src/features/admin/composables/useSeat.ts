import { ref } from 'vue'
import { seatService } from '@/features/admin/services/seat.service'
import type { Seat, CreateSeatPayload } from '@/features/admin/types/seat.type'

export function useSeat(roomId: string) {
  const seatMap = ref<Record<string, Seat[]> | null>(null)
  const loading = ref(false)

  async function fetchSeats() {
    loading.value = true
    try {
      const res = await seatService.getSeatByRoomId(roomId)
      seatMap.value = res.data 
    } finally {
      loading.value = false
    }
  }

  async function createSeats(payload: CreateSeatPayload) {
    await seatService.createSeatByRoomId(roomId, payload)
    await fetchSeats()
  }

  return { seatMap, loading, fetchSeats, createSeats }
}