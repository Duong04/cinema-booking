<script setup lang="ts">
import { computed, ref } from 'vue'
import { useRouter } from 'vue-router'
import type { HistoryState } from 'vue-router'
import { ChevronLeft, CreditCard, Loader2, ShieldCheck, Ticket } from 'lucide-vue-next'
import { useLanguageStore } from '@/stores/language'
import { useBookingFlow } from '@/features/client/composables/useBookingFlow'
import { bookingService } from '@/features/client/services/booking.service'
import { paymentService } from '@/features/client/services/payment.service'
import type { PaymentProvider } from '@/features/client/types/booking-payment.type'
import BookingStepper from './components/BookingStepper.vue'

const router = useRouter()
const languageStore = useLanguageStore()
const { draft, seatTotal, comboTotal, grandTotal, formatVND } = useBookingFlow()

const selectedPayment = ref<PaymentProvider>('vnpay')
const paying = ref(false)
const error = ref('')

const showtime = computed(() => draft.value.showtime)
const movie = computed(() => showtime.value?.movie)
const cinema = computed(() => showtime.value?.room?.cinema)
const seatsLabel = computed(() => draft.value.seats.map((seat) => seat.label).join(', '))
const isReady = computed(() => Boolean(showtime.value && draft.value.seats.length))

const paymentMethods: Array<{ id: PaymentProvider; label: string; description: string }> = [
  { id: 'vnpay', label: 'VNPay', description: 'Thanh toán qua cổng VNPay' },
  { id: 'momo', label: 'MoMo', description: 'Thanh toán bằng ví MoMo' },
  { id: 'zalopay', label: 'ZaloPay', description: 'Thanh toán bằng ví ZaloPay' },
  { id: 'cashier', label: 'Cashier', description: 'Thanh toán tại quầy' },
]

function timeLabel(value?: string) {
  return value?.split(' ')[1]?.slice(0, 5) ?? ''
}

function dateLabel(value?: string) {
  return value?.split(' ')[0] ?? ''
}

function paymentLabel() {
  return paymentMethods.find((method) => method.id === selectedPayment.value)?.label ?? 'Payment'
}

function getErrorMessage(err: unknown, fallback: string) {
  return err instanceof Error ? err.message : fallback
}

function toHistoryState<T>(value: T): HistoryState {
  return value as unknown as HistoryState
}

async function pay() {
  if (!isReady.value || paying.value) return

  paying.value = true
  error.value = ''

  try {
    const bookingResponse = await bookingService.create({
      showtime_id: showtime.value!.id,
      seat_ids: draft.value.seats.map((seat) => seat.id),
      combos: draft.value.combos.map((item) => ({
        combo_id: item.combo.id,
        quantity: item.quantity,
      })),
    })

    const createdBooking = bookingResponse.data
    const paymentResponse = await paymentService.create({
      booking_id: createdBooking.id,
      provider: selectedPayment.value,
    })

    const bookingSummary = {
      id: createdBooking.booking_code,
      movieTitle: movie.value?.title,
      cinemaName: cinema.value?.name,
      roomName: showtime.value?.room?.name,
      showDate: showtime.value?.show_date,
      startTime: showtime.value?.start_time,
      seats: draft.value.seats,
      combos: draft.value.combos,
      seatTotal: seatTotal.value,
      comboTotal: comboTotal.value,
      totalPrice: Number(createdBooking.total_amount ?? grandTotal.value),
      paymentMethod: paymentLabel(),
    }

    router.push({
      path: paymentResponse.data.payment_url,
      state: toHistoryState({
        bookingSummary,
        payment: paymentResponse.data.payment,
        qrContent: paymentResponse.data.qr_content,
      }),
    })
  } catch (err: unknown) {
    error.value = getErrorMessage(err, 'Không thể tạo thanh toán.')
  } finally {
    paying.value = false
  }
}
</script>

<template>
  <div class="pt-24 pb-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <BookingStepper :current-step="4" />

    <div class="flex items-center gap-4 mb-10">
      <button @click="router.back()" class="p-2 bg-white/5 rounded-full text-white hover:bg-white/10">
        <ChevronLeft class="w-6 h-6" />
      </button>
      <div>
        <h1 class="text-3xl font-black text-white uppercase tracking-tight italic">
          {{ languageStore.language === 'en' ? 'Checkout' : 'Xác nhận đặt vé' }}
        </h1>
        <p class="text-gray-500 text-sm">
          {{ languageStore.language === 'en' ? 'Choose a payment method to complete your booking.' : 'Chọn phương thức thanh toán để hoàn tất đặt vé.' }}
        </p>
      </div>
    </div>

    <div v-if="!isReady" class="bg-zinc-900 border border-white/5 rounded-3xl p-10 text-center">
      <Ticket class="w-12 h-12 text-red-500 mx-auto mb-4" />
      <h2 class="text-white font-black text-2xl mb-2">Chưa có thông tin đặt vé</h2>
      <p class="text-gray-500 mb-8">Bạn hãy chọn suất chiếu và ghế trước khi thanh toán.</p>
      <router-link to="/movies" class="inline-flex px-8 py-3 bg-red-600 text-white rounded-xl font-black hover:bg-red-700">
        Chọn phim
      </router-link>
    </div>

    <div v-else class="grid grid-cols-1 lg:grid-cols-[1.4fr_0.8fr] gap-8">
      <section class="space-y-6">
        <div class="bg-zinc-900 border border-white/5 rounded-3xl p-6">
          <h2 class="text-white font-black text-xl uppercase mb-6">Thông tin vé</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="p-4 bg-black/30 rounded-2xl">
              <p class="text-gray-500 text-xs uppercase font-black mb-1">Phim</p>
              <p class="text-white font-bold">{{ movie?.title }}</p>
            </div>
            <div class="p-4 bg-black/30 rounded-2xl">
              <p class="text-gray-500 text-xs uppercase font-black mb-1">Rạp</p>
              <p class="text-white font-bold">{{ cinema?.name }}</p>
            </div>
            <div class="p-4 bg-black/30 rounded-2xl">
              <p class="text-gray-500 text-xs uppercase font-black mb-1">Suất chiếu</p>
              <p class="text-white font-bold">{{ dateLabel(showtime?.show_date) }} • {{ timeLabel(showtime?.start_time) }}</p>
            </div>
            <div class="p-4 bg-black/30 rounded-2xl">
              <p class="text-gray-500 text-xs uppercase font-black mb-1">Phòng / Ghế</p>
              <p class="text-white font-bold">{{ showtime?.room?.name }} • {{ seatsLabel }}</p>
            </div>
          </div>
        </div>

        <div class="bg-zinc-900 border border-white/5 rounded-3xl p-6">
          <h2 class="text-white font-black text-xl uppercase mb-6">Phương thức thanh toán</h2>
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <button
              v-for="method in paymentMethods"
              :key="method.id"
              @click="selectedPayment = method.id"
              :class="[
                'p-4 rounded-2xl border text-left transition-all',
                selectedPayment === method.id
                  ? 'bg-red-600/20 border-red-600 text-white'
                  : 'bg-white/5 border-white/10 text-gray-400 hover:bg-white/10',
              ]"
            >
              <CreditCard class="w-5 h-5 mb-3 text-red-500" />
              <p class="font-black text-white">{{ method.label }}</p>
              <p class="text-xs text-gray-500 mt-1">{{ method.description }}</p>
            </button>
          </div>
        </div>
      </section>

      <aside class="bg-zinc-900 border border-white/5 rounded-3xl p-6 h-fit">
        <h2 class="text-white font-black text-xl uppercase mb-6">Tổng thanh toán</h2>
        <div class="space-y-4">
          <div class="flex justify-between text-sm">
            <span class="text-gray-500">Ghế</span>
            <span class="text-white font-bold">{{ formatVND(seatTotal) }}</span>
          </div>
          <div class="flex justify-between text-sm">
            <span class="text-gray-500">Bắp nước</span>
            <span class="text-white font-bold">{{ formatVND(comboTotal) }}</span>
          </div>
          <div class="border-t border-white/10 pt-4 flex justify-between items-end">
            <span class="text-gray-500 text-sm">Tổng cộng</span>
            <span class="text-red-500 font-black text-3xl tracking-tighter">{{ formatVND(grandTotal) }}</span>
          </div>
        </div>

        <p v-if="error" class="mt-6 text-sm text-red-400 bg-red-500/10 border border-red-500/20 rounded-2xl p-3">
          {{ error }}
        </p>

        <button
          @click="pay"
          :disabled="paying"
          class="w-full mt-8 py-4 bg-red-600 text-white rounded-2xl font-black hover:bg-red-700 transition-all disabled:opacity-70 flex items-center justify-center gap-2"
        >
          <Loader2 v-if="paying" class="w-5 h-5 animate-spin" />
          <ShieldCheck v-else class="w-5 h-5" />
          {{ paying ? 'Đang xử lý...' : 'Tiếp tục thanh toán' }}
        </button>
      </aside>
    </div>
  </div>
</template>
