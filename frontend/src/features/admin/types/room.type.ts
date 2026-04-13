interface Cinema {
    id: string
    name: string
    address: string
}

export const ROOM_TYPES = ['2D', '3D', 'IMAX', '4DX', 'VIP'] as const
export type RoomType = typeof ROOM_TYPES[number]

export interface Room {
  id: string
  name: string
  type: RoomType
  cinema_id: string
  cinema: Cinema
  created_at?: string
  updated_at?: string
}
