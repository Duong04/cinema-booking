<script setup lang="ts">
import { computed, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import type { HistoryState } from 'vue-router'
import { CheckCircle2, ChevronLeft, CreditCard, Loader2, QrCode } from 'lucide-vue-next'
import { paymentService } from '@/features/client/services/payment.service'
import { useBookingFlow } from '@/features/client/composables/useBookingFlow'
import type {
  BookingResultSummary,
  PaymentRouteState,
} from '@/features/client/types/booking-payment.type'

const route = useRoute()
const router = useRouter()
const { clearBooking, formatVND } = useBookingFlow()

const routeState = computed(() => window.history.state as PaymentRouteState | null)
const payment = computed(() => routeState.value?.payment)
const bookingSummary = computed(() => routeState.value?.bookingSummary)
const qrContent = computed(() => routeState.value?.qrContent ?? `PAY ${payment.value?.transaction_code ?? route.params.id}`)
const processing = ref(false)
const error = ref('')

const amount = computed(() => Number(payment.value?.amount ?? bookingSummary.value?.totalPrice ?? 0))
const providerLabel = computed(() => {
  const provider = payment.value?.provider
  if (provider === 'momo') return 'MoMo'
  if (provider === 'zalopay') return 'ZaloPay'
  if (provider === 'cashier') return 'Cashier'
  return 'VNPay'
})

const isCashier = computed(() => payment.value?.provider === 'cashier')

function getErrorMessage(err: unknown, fallback: string) {
  return err instanceof Error ? err.message : fallback
}

function toHistoryState<T>(value: T): HistoryState {
  return value as unknown as HistoryState
}

async function confirmPayment() {
  if (processing.value) return

  processing.value = true
  error.value = ''

  try {
    const response = await paymentService.confirm(String(route.params.id))
    const paidBooking = response.data.booking
    const summary: BookingResultSummary = bookingSummary.value ?? {
      id: paidBooking.booking_code,
      movieTitle: paidBooking.showtime?.movie?.title,
      cinemaName: paidBooking.showtime?.room?.cinema?.name,
      roomName: paidBooking.showtime?.room?.name,
      seats: paidBooking.items?.map((item) => ({
        id: item.id ?? item.seat_label,
        row: item.seat_label.slice(0, 1),
        number: item.seat_label.slice(1),
        label: item.seat_label,
        type: item.seat_type_name ?? '',
        price: Number(item.price ?? 0),
      })) ?? [],
      combos: [],
      totalPrice: Number(paidBooking.total_amount),
      paymentMethod: providerLabel.value,
    }

    clearBooking()
    router.push({
      path: '/booking/payment-result',
      state: toHistoryState({
        booking: {
          ...summary,
          id: paidBooking.booking_code ?? summary.id,
          paymentMethod: providerLabel.value,
          totalPrice: Number(paidBooking.total_amount ?? summary.totalPrice),
        },
      }),
    })
  } catch (err: unknown) {
    error.value = getErrorMessage(err, 'Thanh toán thất bại.')
  } finally {
    processing.value = false
  }
}
</script>

<template>
  <div class="pt-24 pb-20 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center gap-4 mb-8">
      <button @click="router.back()" class="p-2 bg-white/5 rounded-full text-white hover:bg-white/10">
        <ChevronLeft class="w-6 h-6" />
      </button>
      <div>
        <h1 class="text-3xl font-black text-white uppercase tracking-tight italic">Cổng thanh toán</h1>
        <p class="text-gray-500 text-sm">Hoàn tất giao dịch để xác nhận đặt vé.</p>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-[1fr_0.8fr] gap-8">
      <section class="bg-zinc-900 border border-white/5 rounded-3xl p-6 sm:p-8">
        <div class="flex items-center gap-3 mb-6">
          <div class="w-12 h-12 rounded-2xl bg-red-600/20 flex items-center justify-center text-red-500">
            <QrCode v-if="payment?.provider === 'vnpay' || payment?.provider === 'cashier'" class="w-6 h-6" />
            <CreditCard v-else class="w-6 h-6" />
          </div>
          <div>
            <p class="text-white font-black text-xl">{{ providerLabel }}</p>
            <p class="text-gray-500 text-sm">Mã giao dịch: {{ payment?.transaction_code ?? route.params.id }}</p>
          </div>
        </div>

        <div class="aspect-square max-w-sm mx-auto bg-white rounded-3xl p-8 flex flex-col items-center justify-center text-zinc-950">
          <QrCode class="w-36 h-36 mb-6" />
          <p class="text-center text-sm font-black break-all">{{ qrContent }}</p>
        </div>

        <p class="text-center text-gray-500 text-sm mt-6">
          {{ isCashier ? 'Đưa mã này cho thu ngân để xác nhận thanh toán.' : 'Bấm xác nhận để mô phỏng callback thanh toán thành công.' }}
        </p>
      </section>

      <aside class="bg-zinc-900 border border-white/5 rounded-3xl p-6 sm:p-8 h-fit">
        <h2 class="text-white font-black text-xl uppercase mb-6">Thông tin thanh toán</h2>

        <div class="space-y-4 text-sm">
          <div class="flex justify-between gap-4">
            <span class="text-gray-500">Booking</span>
            <span class="text-white font-bold text-right">{{ bookingSummary?.id ?? payment?.booking?.booking_code }}</span>
          </div>
          <div class="flex justify-between gap-4">
            <span class="text-gray-500">Phim</span>
            <span class="text-white font-bold text-right">{{ bookingSummary?.movieTitle ?? payment?.booking?.showtime?.movie?.title }}</span>
          </div>
          <div class="border-t border-white/10 pt-4 flex justify-between items-end">
            <span class="text-gray-500">Tổng tiền</span>
            <span class="text-red-500 font-black text-3xl">{{ formatVND(amount) }}</span>
          </div>
        </div>

        <p v-if="error" class="mt-6 text-sm text-red-400 bg-red-500/10 border border-red-500/20 rounded-2xl p-3">
          {{ error }}
        </p>

        <button
          @click="confirmPayment"
          :disabled="processing"
          class="w-full mt-8 py-4 bg-red-600 text-white rounded-2xl font-black hover:bg-red-700 transition-all disabled:opacity-70 flex items-center justify-center gap-2"
        >
          <Loader2 v-if="processing" class="w-5 h-5 animate-spin" />
          <CheckCircle2 v-else class="w-5 h-5" />
          {{ processing ? 'Đang xử lý...' : isCashier ? 'Xác nhận thu ngân đã thanh toán' : 'Xác nhận thanh toán thành công' }}
        </button>
      </aside>
    </div>
  </div>
</template>
