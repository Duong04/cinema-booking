<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import { ChevronLeft, Loader2, Minus, Plus, ShoppingBag } from 'lucide-vue-next'
import { useLanguageStore } from '@/stores/language'
import { useBookingFlow } from '@/features/client/composables/useBookingFlow'
import { comboService } from '@/features/client/services/combo.service'
import type { PublicCombo } from '@/features/client/types/combo.type'
import BookingStepper from './components/BookingStepper.vue'

const router = useRouter()
const languageStore = useLanguageStore()
const { draft, seatTotal, setCombos, formatVND } = useBookingFlow()

const combos = ref<PublicCombo[]>([])
const loading = ref(false)
const selectedCombos = ref<Record<string, number>>(
  Object.fromEntries(draft.value.combos.map((item) => [item.combo.id, item.quantity])),
)

const cinemaId = computed(() => draft.value.showtime?.room?.cinema?.id)
const comboSelections = computed(() =>
  combos.value
    .map((combo) => ({
      combo: {
        id: combo.id,
        name: combo.name,
        description: combo.description,
        price: Number(combo.price ?? 0),
        image: combo.image,
      },
      quantity: selectedCombos.value[combo.id] ?? 0,
    }))
    .filter((item) => item.quantity > 0),
)
const selectedComboTotal = computed(() =>
  comboSelections.value.reduce((total, item) => total + item.combo.price * item.quantity, 0),
)
const checkoutTotal = computed(() => seatTotal.value + selectedComboTotal.value)

function changeCombo(comboId: string, delta: number) {
  const next = Math.max(0, (selectedCombos.value[comboId] ?? 0) + delta)
  if (next === 0) {
    delete selectedCombos.value[comboId]
  } else {
    selectedCombos.value[comboId] = next
  }
}

function comboQuantity(comboId: string) {
  return selectedCombos.value[comboId] ?? 0
}

function confirmCombos() {
  setCombos(comboSelections.value)
  router.push({ name: 'booking-checkout' })
}

async function fetchCombos() {
  loading.value = true
  try {
    const res = await comboService.getActiveCombos({
      cinema_id: cinemaId.value,
      limit: 100,
    })
    combos.value = res.data.filter((combo) => combo.status === 'active')
    selectedCombos.value = Object.fromEntries(
      Object.entries(selectedCombos.value).filter(([comboId]) =>
        combos.value.some((combo) => combo.id === comboId),
      ),
    )
  } finally {
    loading.value = false
  }
}

onMounted(fetchCombos)
</script>

<template>
  <div class="pt-24 pb-32 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <BookingStepper :current-step="3" />

    <div class="flex items-center gap-4 mb-12">
      <button @click="router.back()" class="p-2 bg-white/5 rounded-full text-white hover:bg-white/10">
        <ChevronLeft class="w-6 h-6" />
      </button>
      <div>
        <h1 class="text-2xl font-black text-white uppercase italic">
          {{ languageStore.language === 'en' ? 'Select Combos' : 'Chọn bắp nước' }}
        </h1>
        <p class="text-gray-500 text-sm">
          {{ languageStore.language === 'en' ? 'Only active combos are available.' : 'Chỉ hiển thị combo đang hoạt động.' }}
        </p>
      </div>
    </div>

    <div v-if="loading" class="py-24 flex items-center justify-center text-gray-400 gap-3">
      <Loader2 class="w-6 h-6 animate-spin text-red-500" />
      {{ languageStore.language === 'en' ? 'Loading combos...' : 'Đang tải combo...' }}
    </div>

    <div v-else-if="combos.length === 0" class="bg-zinc-900/50 border border-dashed border-white/10 rounded-3xl p-10 text-center">
      <ShoppingBag class="w-12 h-12 text-red-500 mx-auto mb-4" />
      <h2 class="text-white font-black text-2xl mb-2">
        {{ languageStore.language === 'en' ? 'No active combos' : 'Chưa có combo đang hoạt động' }}
      </h2>
      <p class="text-gray-500 mb-8">
        {{ languageStore.language === 'en' ? 'You can continue checkout with seats only.' : 'Bạn vẫn có thể tiếp tục thanh toán chỉ với vé.' }}
      </p>
      <button
        @click="confirmCombos"
        class="px-8 py-3 bg-red-600 text-white rounded-xl font-black hover:bg-red-700"
      >
        {{ languageStore.t('booking.checkout') }}
      </button>
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
      <div
        v-for="combo in combos"
        :key="combo.id"
        class="bg-zinc-900/50 border border-white/5 rounded-3xl overflow-hidden group hover:border-red-600/30 transition-all duration-500"
      >
        <div class="aspect-square relative overflow-hidden">
          <img
            v-if="combo.image"
            :src="combo.image"
            :alt="combo.name"
            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
            referrerpolicy="no-referrer"
          />
          <div v-else class="w-full h-full bg-black/40 flex items-center justify-center">
            <ShoppingBag class="w-16 h-16 text-zinc-700" />
          </div>
          <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-transparent" />
        </div>
        <div class="p-6">
          <div class="flex justify-between items-start gap-4 mb-2">
            <h3 class="text-xl font-black text-white uppercase tracking-tight">{{ combo.name }}</h3>
            <span class="text-red-500 font-black">{{ formatVND(Number(combo.price ?? 0)) }}</span>
          </div>
          <p class="text-gray-500 text-sm mb-6 line-clamp-2">{{ combo.description }}</p>

          <div class="flex items-center justify-between">
            <div class="flex items-center gap-4 bg-black/40 rounded-xl p-1 border border-white/5">
              <button @click="changeCombo(combo.id, -1)" class="p-2 text-gray-400 hover:text-white transition-colors">
                <Minus class="w-4 h-4" />
              </button>
              <span class="text-white font-black w-4 text-center">{{ selectedCombos[combo.id] || 0 }}</span>
              <button @click="changeCombo(combo.id, 1)" class="p-2 text-gray-400 hover:text-white transition-colors">
                <Plus class="w-4 h-4" />
              </button>
            </div>
            <div v-if="comboQuantity(combo.id)" class="text-right">
              <p class="text-[10px] text-gray-600 uppercase font-black">Subtotal</p>
              <p class="text-white font-bold">{{ formatVND(Number(combo.price ?? 0) * comboQuantity(combo.id)) }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="fixed bottom-0 left-0 right-0 bg-zinc-950/80 backdrop-blur-2xl border-t border-white/10 p-6 z-50">
      <div class="max-w-7xl mx-auto flex items-center justify-between">
        <div class="flex gap-12">
          <div class="hidden sm:block">
            <p class="text-gray-500 text-[10px] uppercase font-black tracking-widest mb-1">Seats Total</p>
            <p class="text-white font-black text-lg">{{ formatVND(seatTotal) }}</p>
          </div>
          <div class="hidden md:block">
            <p class="text-gray-500 text-[10px] uppercase font-black tracking-widest mb-1">Combos Total</p>
            <p class="text-white font-black text-lg">{{ formatVND(selectedComboTotal) }}</p>
          </div>
        </div>

        <div class="flex items-center gap-8">
          <div class="text-right">
            <p class="text-gray-500 text-[10px] uppercase font-black tracking-widest mb-1">{{ languageStore.t('common.total') }}</p>
            <p class="text-red-500 font-black text-3xl tracking-tighter">{{ formatVND(checkoutTotal) }}</p>
          </div>
          <button
            @click="confirmCombos"
            class="px-12 py-4 bg-red-600 text-white rounded-2xl font-black transition-all transform hover:scale-105 active:scale-95 shadow-[0_10px_30px_rgba(220,38,38,0.3)] flex items-center gap-2"
          >
            <ShoppingBag class="w-5 h-5" />
            {{ languageStore.t('booking.checkout') }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
