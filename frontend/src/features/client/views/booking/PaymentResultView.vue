<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { CheckCircle, Home, Loader2, Ticket } from 'lucide-vue-next'
import { useBookingFlow } from '@/features/client/composables/useBookingFlow'
import { buildBookingSummaryFromBooking, readPaymentResult, savePaymentResult } from '@/features/client/utils/payment-summary'
import { paymentService } from '@/features/client/services/payment.service'
import type {
  BookingResultSummary,
  PaymentResultRouteState,
} from '@/features/client/types/booking-payment.type'

const router = useRouter()
const route = useRoute()
const { clearBooking, formatVND } = useBookingFlow()
const routeState = computed(() => window.history.state as PaymentResultRouteState | null)
const storedBooking = readPaymentResult()
const loadedBooking = ref<BookingResultSummary | null>(null)
const hasPaymentFallback = computed(() => typeof route.query.payment_id === 'string')
const loading = ref(hasPaymentFallback.value && !routeState.value?.booking && !storedBooking)
const error = ref('')
const booking = computed<BookingResultSummary | null>(() => routeState.value?.booking ?? storedBooking ?? loadedBooking.value)

function getErrorMessage(err: unknown, fallback: string) {
  return err instanceof Error ? err.message : fallback
}

async function loadPaymentResult() {
  if (booking.value) {
    loading.value = false
    return
  }

  const paymentId = typeof route.query.payment_id === 'string' ? route.query.payment_id : ''
  if (!paymentId) {
    loading.value = false
    return
  }

  loading.value = true
  error.value = ''

  try {
    const response = await paymentService.getById(paymentId)
    const payment = response.data

    if (payment.booking) {
      const summary = buildBookingSummaryFromBooking(payment.booking, payment)
      loadedBooking.value = summary
      savePaymentResult(summary)
    }
  } catch (err: unknown) {
    error.value = getErrorMessage(err, 'Không thể tải thông tin vé.')
  } finally {
    loading.value = false
  }
}

function finishBooking(path: string) {
  clearBooking()
  router.push(path)
}

onMounted(loadPaymentResult)
</script>

<template>
  <div class="pt-32 pb-20 max-w-2xl mx-auto px-4">
    <div v-if="booking" class="bg-zinc-900 rounded-3xl border border-white/5 p-8 sm:p-12 text-center shadow-2xl">
      <div class="w-20 h-20 bg-emerald-500/20 rounded-full flex items-center justify-center mx-auto mb-8">
        <CheckCircle class="w-11 h-11 text-emerald-500" />
      </div>
      <h1 class="text-3xl sm:text-4xl font-black text-white mb-4 uppercase tracking-tight">
        Thanh toán thành công
      </h1>
      <p class="text-gray-400 mb-10">Vé demo của bạn đã được ghi nhận.</p>

      <div class="bg-black/40 rounded-3xl p-6 mb-10 text-left space-y-4 border border-white/5">
        <div class="flex justify-between gap-4">
          <span class="text-gray-500 text-sm">Mã đặt vé</span>
          <span class="text-white font-bold text-right">{{ booking.id }}</span>
        </div>
        <div class="flex justify-between gap-4">
          <span class="text-gray-500 text-sm">Phim</span>
          <span class="text-white font-bold text-right">{{ booking.movieTitle }}</span>
        </div>
        <div class="flex justify-between gap-4">
          <span class="text-gray-500 text-sm">Ghế</span>
          <span class="text-white font-bold text-right">{{ booking.seats.map((seat: { label: string }) => seat.label).join(', ') }}</span>
        </div>
        <div class="flex justify-between gap-4">
          <span class="text-gray-500 text-sm">Thanh toán</span>
          <span class="text-white font-bold text-right">{{ booking.paymentMethod }}</span>
        </div>
        <div v-if="Number(booking.discountAmount ?? 0) > 0" class="flex justify-between gap-4">
          <span class="text-gray-500 text-sm">Giảm giá {{ booking.promotionCode ? `(${booking.promotionCode})` : '' }}</span>
          <span class="text-emerald-400 font-bold text-right">-{{ formatVND(Number(booking.discountAmount)) }}</span>
        </div>
        <div class="flex justify-between gap-4 pt-4 border-t border-white/10">
          <span class="text-gray-500 text-sm">Tổng tiền</span>
          <span class="text-red-500 font-black text-right">{{ formatVND(booking.totalPrice) }}</span>
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <button
          @click="finishBooking('/profile')"
          class="flex items-center justify-center gap-2 px-8 py-4 bg-white text-black rounded-2xl font-black hover:bg-gray-200 transition-all"
        >
          <Ticket class="w-5 h-5" />
          Xem vé
        </button>
        <button
          @click="finishBooking('/')"
          class="flex items-center justify-center gap-2 px-8 py-4 bg-white/5 border border-white/10 text-white rounded-2xl font-black hover:bg-white/10 transition-all"
        >
          <Home class="w-5 h-5" />
          Về trang chủ
        </button>
      </div>
    </div>

    <div v-else-if="loading" class="bg-zinc-900 rounded-3xl border border-white/5 p-10 text-center">
      <Loader2 class="w-12 h-12 text-red-500 mx-auto mb-4 animate-spin" />
      <h1 class="text-2xl font-black text-white mb-2">Dang tai thong tin ve</h1>
      <p class="text-gray-500">Vui long cho trong giay lat.</p>
    </div>

    <div v-else class="bg-zinc-900 rounded-3xl border border-white/5 p-10 text-center">
      <Ticket class="w-12 h-12 text-red-500 mx-auto mb-4" />
      <h1 class="text-2xl font-black text-white mb-2">Không tìm thấy giao dịch</h1>
      <p class="text-gray-500 mb-8">Bạn hãy thực hiện đặt vé lại từ đầu.</p>
      <button @click="finishBooking('/movies')" class="px-8 py-3 bg-red-600 text-white rounded-xl font-black hover:bg-red-700">
        Chọn phim
      </button>
    </div>
  </div>
</template>
