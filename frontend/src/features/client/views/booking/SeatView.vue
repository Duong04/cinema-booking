<script setup lang="ts">
import { computed, onMounted, onUnmounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { ChevronLeft, Clock, Loader2 } from 'lucide-vue-next'
import { useLanguageStore } from '@/stores/language'
import { useAuthStore } from '@/features/shared/stores/auth.store'
import { showtimeService } from '@/features/client/services/showtime.service'
import { seatHoldService } from '@/features/client/services/seat-hold.service'
import {
  seatRealtimeService,
  type SeatStatusChangedEvent,
} from '@/features/client/services/seat-realtime.service'
import { useBookingFlow, type BookingSeat } from '@/features/client/composables/useBookingFlow'
import type { PublicShowtime, PublicShowtimeSeat } from '@/features/client/types/showtime.type'
import BookingStepper from './components/BookingStepper.vue'

const route = useRoute()
const router = useRouter()
const languageStore = useLanguageStore()
const authStore = useAuthStore()
const { draft, setShowtime, setSeats, formatVND } = useBookingFlow()

const timeLeft = ref(600)
const selectedSeats = ref<BookingSeat[]>([...draft.value.seats])
const showtime = ref<PublicShowtime | null>(draft.value.showtime)
const seats = ref<PublicShowtimeSeat[]>([])
const loading = ref(false)
const holding = ref(false)
const holdError = ref('')
let timer: ReturnType<typeof setInterval> | null = null
let seatChannelName: string | null = null

const movie = computed(() => showtime.value?.movie)
const priceBySeatType = computed(() => {
  const map = new Map<string, number>()
  showtime.value?.prices?.forEach((price) => {
    if (price.seat_type_id) map.set(price.seat_type_id, Number(price.price ?? 0))
  })
  return map
})
const groupedSeats = computed(() => {
  const groups = new Map<string, PublicShowtimeSeat[]>()
  seats.value.forEach((seat) => {
    if (!groups.has(seat.row_label)) groups.set(seat.row_label, [])
    groups.get(seat.row_label)?.push(seat)
  })

  return Array.from(groups.entries()).map(([row, rowSeats]) => ({
    row,
    seats: rowSeats.sort((a, b) => Number(a.seat_number) - Number(b.seat_number)),
  }))
})
const seatTypeLegends = computed(() => {
  const map = new Map<string, { id: string; name: string; swatchClass: string }>()

  seats.value.forEach((seat) => {
    const id = seat.seat_type_id
    if (!id || map.has(id)) return

    const name = seat.seat_type?.name ?? 'Standard'
    map.set(id, {
      id,
      name,
      swatchClass: seatTypeTone(name).swatch,
    })
  })

  return Array.from(map.values())
})
const totalPrice = computed(() => selectedSeats.value.reduce((total, seat) => total + seat.price, 0))

function formatTime(seconds: number) {
  const mins = Math.floor(seconds / 60)
  const secs = seconds % 60
  return `${mins}:${secs.toString().padStart(2, '0')}`
}

function getSeatPrice(seat: PublicShowtimeSeat) {
  return priceBySeatType.value.get(seat.seat_type_id) ?? Number(showtime.value?.base_price ?? 0)
}

function isSelected(seatId: string) {
  return selectedSeats.value.some((seat) => seat.id === seatId)
}

function isDisabled(seat: PublicShowtimeSeat) {
  return seat.status === 'booked' || seat.status === 'held'
}

function seatStatusLabel(seat: PublicShowtimeSeat) {
  if (seat.status === 'booked') return 'Booked'
  if (seat.status === 'held') return 'Held'
  return seat.seat_type?.name ?? 'Available'
}

function seatTypeTone(name?: string) {
  const normalized = name?.toLowerCase() ?? ''

  if (normalized.includes('imax')) {
    return {
      seat: 'bg-sky-500/5 border-sky-500/20 text-sky-500/50 hover:border-sky-500 hover:text-sky-400',
      swatch: 'border-sky-500/50 bg-sky-500/10',
    }
  }

  if (normalized.includes('vip')) {
    return {
      seat: 'bg-yellow-500/5 border-yellow-500/20 text-yellow-500/40 hover:border-yellow-500 hover:text-yellow-500',
      swatch: 'border-yellow-500/50 bg-yellow-500/10',
    }
  }

  if (normalized.includes('sweetbox') || normalized.includes('sweet box')) {
    return {
      seat: 'bg-purple-500/5 border-purple-500/25 text-purple-400/60 hover:border-purple-500 hover:text-purple-300',
      swatch: 'border-purple-500/50 bg-purple-500/10',
    }
  }

  if (normalized.includes('couple') || normalized.includes('đôi')) {
    return {
      seat: 'bg-pink-500/5 border-pink-500/20 text-pink-500/50 hover:border-pink-500 hover:text-pink-400',
      swatch: 'border-pink-500/50 bg-pink-500/10',
    }
  }

  return {
    seat: 'bg-white/5 border-white/10 text-gray-600 hover:border-red-500 hover:text-red-500',
    swatch: 'border-white/10 bg-white/5',
  }
}

function seatTypeClass(seat: PublicShowtimeSeat) {
  return seatTypeTone(seat.seat_type?.name).seat
}

function toggleSeat(seat: PublicShowtimeSeat) {
  if (isDisabled(seat)) return
  holdError.value = ''

  const index = selectedSeats.value.findIndex((item) => item.id === seat.id)
  if (index >= 0) {
    selectedSeats.value.splice(index, 1)
    return
  }

  selectedSeats.value.push({
    id: seat.id,
    row: seat.row_label,
    number: seat.seat_number,
    label: seat.label,
    type: seat.seat_type?.name ?? 'Standard',
    price: getSeatPrice(seat),
  })
}

async function confirmSeats() {
  if (selectedSeats.value.length === 0 || !showtime.value || holding.value) return

  if (!authStore.isLoggedIn) {
    setSeats(selectedSeats.value)
    router.push({ name: 'login', query: { redirect: route.fullPath } })
    return
  }

  holding.value = true
  holdError.value = ''

  try {
    await seatHoldService.hold({
      showtime_id: showtime.value.id,
      seat_ids: selectedSeats.value.map((seat) => seat.id),
    })

    setSeats(selectedSeats.value)
    router.push({ name: 'booking-combo' })
  } catch (err) {
    holdError.value = err instanceof Error
      ? err.message
      : 'Không thể giữ ghế. Vui lòng chọn lại.'
    await fetchSeatOverview()
  } finally {
    holding.value = false
  }
}

function applySeatStatusChange(event: SeatStatusChangedEvent) {
  if (event.showtime_id !== String(route.params.showtimeId)) return

  const changedSeatIds = new Set(event.seat_ids)

  seats.value = seats.value.map((seat) => {
    if (!changedSeatIds.has(seat.id)) return seat

    return {
      ...seat,
      status: event.status,
    }
  })

  if (event.status !== 'available') {
    selectedSeats.value = selectedSeats.value.filter((seat) => !changedSeatIds.has(seat.id))
    setSeats(selectedSeats.value)
  }
}

async function fetchSeatOverview() {
  const showtimeId = String(route.params.showtimeId)
  loading.value = true
  try {
    const res = await showtimeService.getPublicSeatOverview(showtimeId)
    showtime.value = res.data.showtime
    seats.value = res.data.seats
    setShowtime(res.data.showtime)
    selectedSeats.value = selectedSeats.value.filter((selected) =>
      res.data.seats.some((seat) => seat.id === selected.id && seat.status === 'available'),
    )
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  const showtimeId = String(route.params.showtimeId)

  fetchSeatOverview()
  seatChannelName = seatRealtimeService.subscribe(showtimeId, applySeatStatusChange)

  timer = setInterval(() => {
    if (timeLeft.value > 0) {
      timeLeft.value--
    } else {
      router.push('/')
    }
  }, 1000)
})

onUnmounted(() => {
  if (timer) clearInterval(timer)
  if (seatChannelName) seatRealtimeService.leave(seatChannelName)
})
</script>

<template>
  <div class="pt-24 pb-40 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <BookingStepper :current-step="2" />

    <div class="flex items-center justify-between mb-12">
      <div class="flex items-center gap-4">
        <button @click="router.back()" class="p-2 bg-white/5 rounded-full text-white hover:bg-white/10">
          <ChevronLeft class="w-6 h-6" />
        </button>
        <div>
          <h1 class="text-2xl font-black text-white uppercase italic">{{ languageStore.t('booking.select_seats') }}</h1>
          <p class="text-gray-500 text-sm">
            {{ movie?.title }} • {{ showtime?.start_time?.split(' ')[1]?.slice(0, 5) }} • {{ showtime?.room?.name }}
          </p>
        </div>
      </div>

      <div class="flex items-center gap-3 px-4 py-2 bg-red-600/10 border border-red-600/20 rounded-xl">
        <Clock class="w-4 h-4 text-red-500 animate-pulse" />
        <span class="text-sm font-black text-red-500 font-mono">{{ formatTime(timeLeft) }}</span>
      </div>
    </div>

    <div v-if="loading" class="py-24 flex items-center justify-center text-gray-400 gap-3">
      <Loader2 class="w-6 h-6 animate-spin text-red-500" />
      {{ languageStore.language === 'en' ? 'Loading seats...' : 'Đang tải sơ đồ ghế...' }}
    </div>

    <div v-else class="flex flex-col items-center">
      <div class="w-full max-w-3xl mb-20 relative">
        <div class="h-1.5 w-full bg-red-600 rounded-t-[100%] shadow-[0_-10px_40px_rgba(220,38,38,0.6)]" />
        <div class="absolute -top-8 left-1/2 -translate-x-1/2 w-full h-24 bg-gradient-to-b from-red-600/20 to-transparent blur-2xl opacity-50" />
        <p class="text-center text-[10px] text-gray-500 uppercase tracking-[1em] mt-6 font-black">SCREEN</p>
      </div>

      <div class="flex flex-wrap justify-center gap-8 mb-12 px-4 py-3 bg-white/5 rounded-2xl border border-white/10 backdrop-blur-md">
        <div class="flex items-center gap-2"><div class="w-5 h-5 rounded-md bg-white/5 border border-white/10" /><span class="text-xs font-bold text-gray-400">Available</span></div>
        <div class="flex items-center gap-2"><div class="w-5 h-5 rounded-md bg-red-600" /><span class="text-xs font-bold text-gray-400">Selected</span></div>
        <div class="flex items-center gap-2"><div class="w-5 h-5 rounded-md bg-zinc-800" /><span class="text-xs font-bold text-gray-400">Booked/Held</span></div>
        <div v-for="type in seatTypeLegends" :key="type.id" class="flex items-center gap-2">
          <div :class="['w-5 h-5 rounded-md border-2', type.swatchClass]" />
          <span class="text-xs font-bold text-gray-400">{{ type.name }}</span>
        </div>
      </div>

      <div v-if="groupedSeats.length === 0" class="py-16 text-center text-gray-500 font-bold">
        {{ languageStore.language === 'en' ? 'No seats found for this room.' : 'Phòng này chưa có sơ đồ ghế.' }}
      </div>

      <div v-else class="overflow-x-auto w-full pb-8 scrollbar-hide">
        <div class="min-w-max flex flex-col items-center gap-4 px-8">
          <div v-for="group in groupedSeats" :key="group.row" class="flex gap-4 items-center">
            <span class="w-6 text-[10px] font-black text-gray-600 text-center">{{ group.row }}</span>
            <div class="flex gap-3">
              <button
                v-for="seat in group.seats"
                :key="seat.id"
                @click="toggleSeat(seat)"
                :disabled="isDisabled(seat)"
                :class="[
                  'w-8 h-8 md:w-9 md:h-9 rounded-lg border transition-all duration-300 flex items-center justify-center text-[10px] font-bold relative group',
                  isDisabled(seat)
                    ? 'bg-zinc-800 border-transparent text-zinc-600 cursor-not-allowed'
                    : isSelected(seat.id)
                      ? 'bg-red-600 border-red-500 text-white shadow-[0_0_20px_rgba(220,38,38,0.6)] scale-110 z-10'
                      : seatTypeClass(seat),
                ]"
              >
                {{ seat.seat_number }}
                <div class="absolute -top-14 left-1/2 -translate-x-1/2 px-3 py-1.5 bg-zinc-900 text-white text-[9px] rounded-lg opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap border border-white/10 z-20 flex flex-col items-center shadow-2xl">
                  <span class="font-black text-gray-400 uppercase tracking-widest mb-0.5">Seat {{ seat.label }}</span>
                  <span class="text-gray-400">{{ seatStatusLabel(seat) }}</span>
                  <span class="text-red-500 font-black">{{ formatVND(getSeatPrice(seat)) }}</span>
                </div>
              </button>
            </div>
            <span class="w-6 text-[10px] font-black text-gray-600 text-center">{{ group.row }}</span>
          </div>
        </div>
      </div>

      <div class="fixed bottom-0 left-0 right-0 bg-zinc-950/80 backdrop-blur-2xl border-t border-white/10 p-6 z-50">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
          <div class="hidden sm:block">
            <p class="text-gray-500 text-[10px] uppercase font-black tracking-widest mb-1">Seats Selected</p>
            <p class="text-white font-black text-lg">{{ selectedSeats.length ? selectedSeats.map((seat) => seat.label).join(', ') : 'None' }}</p>
            <p v-if="holdError" class="text-red-400 text-xs font-bold mt-2">{{ holdError }}</p>
          </div>

          <div class="flex items-center gap-8">
            <div class="text-right">
              <p class="text-gray-500 text-[10px] uppercase font-black tracking-widest mb-1">{{ languageStore.t('common.total') }}</p>
              <p class="text-red-500 font-black text-3xl tracking-tighter">{{ formatVND(totalPrice) }}</p>
            </div>
            <button
              :disabled="selectedSeats.length === 0 || holding"
              @click="confirmSeats"
              :class="[
                'px-12 py-4 rounded-2xl font-black transition-all transform active:scale-95',
                selectedSeats.length && !holding
                  ? 'bg-red-600 text-white shadow-[0_10px_30px_rgba(220,38,38,0.3)] hover:bg-red-700 hover:scale-105'
                  : 'bg-zinc-800 text-gray-500 cursor-not-allowed',
              ]"
            >
              <span class="inline-flex items-center gap-2">
                <Loader2 v-if="holding" class="w-5 h-5 animate-spin" />
                {{ holding ? 'Đang giữ ghế...' : languageStore.t('common.confirm') }}
              </span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.scrollbar-hide::-webkit-scrollbar {
  display: none;
}
.scrollbar-hide {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>
