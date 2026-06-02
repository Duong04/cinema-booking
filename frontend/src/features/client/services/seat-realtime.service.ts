import Echo from 'laravel-echo'
import Pusher from 'pusher-js'
import type { PublicShowtimeSeatStatus } from '@/features/client/types/showtime.type'

declare global {
  interface Window {
    Pusher: typeof Pusher
  }
}

export interface SeatStatusChangedEvent {
  showtime_id: string
  seat_ids: string[]
  status: PublicShowtimeSeatStatus
  user_id?: string | null
  expired_at?: string | null
}

window.Pusher = Pusher

const reverbPort = Number(import.meta.env.VITE_REVERB_PORT ?? 8080)
const reverbScheme = import.meta.env.VITE_REVERB_SCHEME ?? 'http'

const echo = new Echo({
  broadcaster: 'reverb',
  key: import.meta.env.VITE_REVERB_APP_KEY,
  wsHost: import.meta.env.VITE_REVERB_HOST ?? window.location.hostname,
  wsPort: reverbPort,
  wssPort: reverbPort,
  forceTLS: reverbScheme === 'https',
  enabledTransports: reverbScheme === 'https' ? ['wss'] : ['ws'],
})

export const seatRealtimeService = {
  subscribe(showtimeId: string, callback: (event: SeatStatusChangedEvent) => void) {
    const channelName = `showtime.${showtimeId}.seats`

    echo.channel(channelName).listen('.seat.status.changed', callback)

    return channelName
  },

  leave(channelName: string) {
    echo.leave(channelName)
  },
}
