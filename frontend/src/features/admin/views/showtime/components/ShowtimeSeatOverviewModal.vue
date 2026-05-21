<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import { useMessage } from 'naive-ui'
import { showtimeService } from '@/features/admin/services/showtime.service'
import type {
  Showtime,
  ShowtimeSeatOverview,
  ShowtimeSeatOverviewItem,
  ShowtimeSeatStatus,
} from '@/features/admin/types/showtime.type'

const props = defineProps<{
  showtime: Showtime | null
}>()

const showModal = defineModel<boolean>('show')
const message = useMessage()
const loading = ref(false)
const overview = ref<ShowtimeSeatOverview | null>(null)

const statusConfig: Record<ShowtimeSeatStatus, { label: string; class: string }> = {
  available: {
    label: 'Còn trống',
    class: 'border-emerald-200 bg-emerald-50 text-emerald-700',
  },
  held: {
    label: 'Đang giữ',
    class: 'border-amber-200 bg-amber-50 text-amber-700',
  },
  booked: {
    label: 'Đã mua',
    class: 'border-red-200 bg-red-50 text-red-700',
  },
}

const groupedSeats = computed(() => {
  const groups = new Map<string, ShowtimeSeatOverviewItem[]>()

  overview.value?.seats.forEach((seat) => {
    if (!groups.has(seat.row_label)) groups.set(seat.row_label, [])
    groups.get(seat.row_label)?.push(seat)
  })

  return Array.from(groups.entries()).map(([row, seats]) => ({
    row,
    seats: seats.sort((a, b) => Number(a.seat_number) - Number(b.seat_number)),
  }))
})

function seatTooltip(seat: ShowtimeSeatOverviewItem) {
  if (seat.status === 'booked') {
    return `${seat.label} - ${seat.booking?.booking_code ?? 'Đã mua'}`
  }

  if (seat.status === 'held') {
    return `${seat.label} - đang giữ bởi ${seat.hold?.user?.name ?? 'khách'}`
  }

  return `${seat.label} - còn trống`
}

async function fetchOverview() {
  if (!props.showtime?.id) return

  loading.value = true
  try {
    const res = await showtimeService.getSeatOverview(props.showtime.id)
    overview.value = res.data
  } catch {
    message.error('Không tải được tình trạng ghế của suất chiếu')
  } finally {
    loading.value = false
  }
}

watch(showModal, (visible) => {
  if (visible) fetchOverview()
  else overview.value = null
})
</script>

<template>
  <n-modal
    v-model:show="showModal"
    preset="dialog"
    :title="`Tình trạng ghế - ${showtime?.movie?.title ?? ''}`"
    :show-icon="false"
    style="width: 860px"
  >
    <n-spin :show="loading">
      <div class="space-y-5">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
          <div class="rounded-lg border border-gray-200 p-4">
            <p class="text-xs text-gray-500 mb-1">Tổng ghế</p>
            <p class="text-2xl font-bold">{{ overview?.summary.total ?? 0 }}</p>
          </div>
          <div class="rounded-lg border border-red-200 bg-red-50 p-4">
            <p class="text-xs text-red-500 mb-1">Đã mua</p>
            <p class="text-2xl font-bold text-red-700">{{ overview?.summary.booked ?? 0 }}</p>
          </div>
          <div class="rounded-lg border border-amber-200 bg-amber-50 p-4">
            <p class="text-xs text-amber-600 mb-1">Đang giữ</p>
            <p class="text-2xl font-bold text-amber-700">{{ overview?.summary.held ?? 0 }}</p>
          </div>
          <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-4">
            <p class="text-xs text-emerald-600 mb-1">Còn trống</p>
            <p class="text-2xl font-bold text-emerald-700">{{ overview?.summary.available ?? 0 }}</p>
          </div>
        </div>

        <div class="rounded-lg bg-gray-900 px-4 py-3 text-center text-xs font-bold uppercase tracking-[0.5em] text-white">
          Màn hình
        </div>

        <div class="max-h-[420px] overflow-auto rounded-lg border border-gray-200 p-4">
          <div v-if="groupedSeats.length === 0 && !loading" class="py-10 text-center text-gray-500">
            Phòng này chưa có sơ đồ ghế.
          </div>

          <div v-for="group in groupedSeats" :key="group.row" class="mb-3 flex items-center gap-3">
            <div class="w-8 text-center text-xs font-bold text-gray-500">{{ group.row }}</div>
            <div class="flex flex-wrap gap-2">
              <n-tooltip v-for="seat in group.seats" :key="seat.id" trigger="hover">
                <template #trigger>
                  <div
                    :class="[
                      'flex h-9 w-9 items-center justify-center rounded-md border text-xs font-bold',
                      statusConfig[seat.status].class,
                    ]"
                  >
                    {{ seat.seat_number }}
                  </div>
                </template>
                {{ seatTooltip(seat) }}
              </n-tooltip>
            </div>
            <div class="w-8 text-center text-xs font-bold text-gray-500">{{ group.row }}</div>
          </div>
        </div>

        <div class="flex flex-wrap gap-4 text-xs text-gray-500">
          <div v-for="(config, status) in statusConfig" :key="status" class="flex items-center gap-2">
            <span :class="['inline-block h-4 w-4 rounded border', config.class]" />
            {{ config.label }}
          </div>
        </div>
      </div>
    </n-spin>
  </n-modal>
</template>
