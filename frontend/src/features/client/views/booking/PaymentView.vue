<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import type { HistoryState } from 'vue-router'
import { CheckCircle2, ChevronLeft, CreditCard, Loader2, QrCode } from 'lucide-vue-next'
import { paymentService } from '@/features/client/services/payment.service'
import { useBookingFlow } from '@/features/client/composables/useBookingFlow'
import {
  buildBookingSummaryFromBooking,
  paymentProviderLabel,
  savePaymentResult,
} from '@/features/client/utils/payment-summary'
import type {
  BookingResultSummary,
  ClientPayment,
  PaymentRouteState,
} from '@/features/client/types/booking-payment.type'

const route = useRoute()
const router = useRouter()
const { clearBooking, formatVND } = useBookingFlow()

const routeState = computed(() => window.history.state as PaymentRouteState | null)
const loadedPayment = ref<ClientPayment | null>(null)
const loading = ref(false)
const payment = computed(() => routeState.value?.payment ?? loadedPayment.value)
const bookingSummary = computed(() =>
  routeState.value?.bookingSummary
  ?? (payment.value?.booking ? buildBookingSummaryFromBooking(payment.value.booking, payment.value) : null),
)
const qrContent = computed(() => routeState.value?.qrContent ?? `PAY ${payment.value?.transaction_code ?? route.params.id}`)
const processing = ref(false)
const error = ref('')

const amount = computed(() => Number(payment.value?.amount ?? bookingSummary.value?.totalPrice ?? 0))
const providerLabel = computed(() => paymentProviderLabel(payment.value?.provider))
const discountAmount = computed(() => Number(bookingSummary.value?.discountAmount ?? 0))

function getErrorMessage(err: unknown, fallback: string) {
  return err instanceof Error ? err.message : fallback
}

function toHistoryState<T>(value: T): HistoryState {
  return value as unknown as HistoryState
}

async function loadPayment() {
  if (payment.value) return

  loading.value = true
  error.value = ''

  try {
    const response = await paymentService.getById(String(route.params.id))
    loadedPayment.value = response.data
  } catch (err: unknown) {
    error.value = getErrorMessage(err, 'Không thể tải thông tin thanh toán.')
  } finally {
    loading.value = false
  }
}

async function confirmPayment() {
  if (processing.value) return

  processing.value = true
  error.value = ''

  try {
    const response = await paymentService.confirm(String(route.params.id))
    const paidBooking = response.data.booking
    const summary: BookingResultSummary = bookingSummary.value
      ?? buildBookingSummaryFromBooking(paidBooking, response.data.payment)
    const resultSummary = {
      ...summary,
      id: paidBooking.booking_code ?? summary.id,
      paymentMethod: providerLabel.value,
      totalPrice: Number(paidBooking.total_amount ?? summary.totalPrice),
    }

    clearBooking()
    savePaymentResult(resultSummary)
    router.push({
      name: 'booking-payment-result',
      state: toHistoryState({
        booking: resultSummary,
      }),
    })
  } catch (err: unknown) {
    error.value = getErrorMessage(err, 'Thanh toán thất bại.')
  } finally {
    processing.value = false
  }
}

onMounted(loadPayment)
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

    <div v-if="loading" class="py-24 flex items-center justify-center text-gray-400 gap-3">
      <Loader2 class="w-6 h-6 animate-spin text-red-500" />
      Đang tải thông tin thanh toán...
    </div>

    <div v-else class="grid grid-cols-1 lg:grid-cols-[1fr_0.8fr] gap-8">
      <section class="bg-zinc-900 border border-white/5 rounded-3xl p-6 sm:p-8">
        <div class="flex items-center gap-3 mb-6">
          <div class="w-12 h-12 rounded-2xl bg-red-600/20 flex items-center justify-center text-red-500">
            <QrCode v-if="payment?.provider === 'vnpay'" class="w-6 h-6" />
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
          Bấm xác nhận để mô phỏng callback thanh toán thành công.
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
          <div v-if="discountAmount > 0" class="flex justify-between gap-4">
            <span class="text-gray-500">Giảm giá {{ bookingSummary?.promotionCode ? `(${bookingSummary.promotionCode})` : '' }}</span>
            <span class="text-emerald-400 font-bold text-right">-{{ formatVND(discountAmount) }}</span>
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
          {{ processing ? 'Đang xử lý...' : 'Xác nhận thanh toán thành công' }}
        </button>
      </aside>
    </div>
  </div>
</template>
