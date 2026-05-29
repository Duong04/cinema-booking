<script setup lang="ts">
import { computed, onMounted, ref, watch } from 'vue'
import { useDebounceFn } from '@vueuse/core'
import { useLanguageStore } from '@/stores/language'
import { useCinema } from '@/features/client/composables/useCinema'
import { useCity } from '@/features/client/composables/useCity'
import { MapPin, Phone, Clock, Search, SlidersHorizontal, Star, Coffee, Wifi, Car, Tv } from 'lucide-vue-next'
import type { Cinema } from '@/features/client/types/cinema.type'

const languageStore = useLanguageStore()
const searchQuery = ref('')
const selectedCityId = ref<string | null>(null)
const page = ref(1)
const limit = 9
const { cinemas, cinemasMeta, loadingCinemas, fetchCinemas } = useCinema()
const { cities, fetchCities } = useCity()

const pageCount = computed(() => cinemasMeta.value?.last_page ?? 1)
const totalCinemas = computed(() => cinemasMeta.value?.total ?? cinemas.value.length)

const amenities = ['IMAX', 'Gold Class', 'Dolby Atmos']

function fetchCinemaList(pageNumber = page.value) {
  page.value = pageNumber
  fetchCinemas({
    page: page.value,
    limit,
    q: searchQuery.value || undefined,
    city_id: selectedCityId.value || undefined,
  })
}

function resetAndFetchCinemas() {
  fetchCinemaList(1)
}

const debouncedSearch = useDebounceFn(resetAndFetchCinemas, 500)

function getCinemaChain(cinema: Cinema) {
  return cinema.cinema_chain ?? cinema.cinemaChain ?? null
}

function getCinemaImage(cinema: Cinema) {
  const chainLogo = getCinemaChain(cinema)?.logo
  if (chainLogo) return chainLogo

  return `https://images.unsplash.com/photo-1517604931442-7e0c8ed2963c?q=80&w=1200&auto=format&fit=crop&cinema=${cinema.id}`
}

const getAmenityIcon = (amenity: string) => {
  switch (amenity) {
    case 'IMAX': return Tv
    case 'Gold Class': return Coffee
    case 'Dolby Atmos': return Wifi
    case '4DX': return SlidersHorizontal
    case 'Sweetbox': return Star
    default: return Car
  }
}

onMounted(() => {
  fetchCinemaList()
  fetchCities()
})

watch(selectedCityId, resetAndFetchCinemas)
watch(searchQuery, debouncedSearch)
</script>

<template>
  <div class="min-h-screen bg-zinc-950">
    <!-- Hero Section -->
    <section class="relative h-[80vh] md:h-[100vh] flex flex-col items-center justify-center overflow-hidden">
      <div>
        <img 
          src="https://images.unsplash.com/photo-1517604931442-7e0c8ed2963c?q=80&w=2070&auto=format&fit=crop" 
          class="w-full h-full object-cover opacity-30 grayscale"
          alt="Cinema background"
        />
        <div class="absolute inset-0 bg-gradient-to-t from-zinc-950 via-zinc-950/60 to-transparent" />
      </div>

      <div class="absolute z-10 text-center px-4 max-w-4xl mx-auto">
        <h1 class="text-5xl md:text-8xl font-black text-white uppercase italic tracking-tighter mb-6 drop-shadow-2xl">
          {{ languageStore.t('nav.cinemas') }}
        </h1>
        <p class="text-gray-400 text-lg md:text-xl font-medium mb-12 max-w-2xl mx-auto leading-relaxed">
          {{ languageStore.language === 'en' ? 'Find the nearest CineMax and enjoy the ultimate cinematic experience.' : 'Tìm rạp CineMax gần nhất và tận hưởng trải nghiệm điện ảnh đỉnh cao.' }}
        </p>

        <!-- Search Bar -->
        <div class="relative max-w-2xl mx-auto group">
          <div class="absolute inset-y-0 left-6 flex items-center pointer-events-none">
            <Search class="w-6 h-6 text-gray-500 group-focus-within:text-red-600 transition-colors" />
          </div>
          <input 
            v-model="searchQuery"
            type="text" 
            :placeholder="languageStore.language === 'en' ? 'Search by cinema name or address...' : 'Tìm theo tên rạp hoặc địa chỉ...'"
            class="w-full bg-white/5 backdrop-blur-2xl border border-white/10 rounded-3xl py-6 pl-16 pr-6 text-white placeholder:text-gray-600 focus:ring-2 focus:ring-red-600/50 focus:border-red-600 outline-none transition-all text-lg shadow-2xl"
          />
        </div>
      </div>
    </section>

    <!-- Content Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-16 relative z-20 pb-32">
      <!-- City Filter -->
      <div class="flex items-center justify-between gap-6 mb-12">
        <div class="flex items-center gap-3 overflow-x-auto pb-4 scrollbar-hide">
          <button
            @click="selectedCityId = null"
            :class="[
              'shrink-0 px-8 py-3 rounded-2xl text-xs font-black uppercase tracking-widest transition-all border',
              selectedCityId === null
                ? 'bg-red-600 border-red-600 text-white shadow-lg shadow-red-600/20'
                : 'bg-white/5 border-white/5 text-gray-400 hover:border-white/10 hover:text-white'
            ]"
          >
            {{ languageStore.language === 'en' ? 'All' : 'Tất cả' }}
          </button>
          <button
            v-for="city in cities"
            :key="city.id"
            @click="selectedCityId = city.id"
            :class="[
              'shrink-0 px-8 py-3 rounded-2xl text-xs font-black uppercase tracking-widest transition-all border',
              selectedCityId === city.id
                ? 'bg-red-600 border-red-600 text-white shadow-lg shadow-red-600/20'
                : 'bg-white/5 border-white/5 text-gray-400 hover:border-white/10 hover:text-white'
            ]"
          >
            {{ city.name }}
          </button>
        </div>
        <p class="hidden sm:block shrink-0 text-gray-600 text-[10px] font-black uppercase tracking-widest">
          {{ totalCinemas }} {{ languageStore.language === 'en' ? 'Results' : 'Kết quả' }}
        </p>
      </div>

      <div class="relative">
        <div
          v-if="loadingCinemas"
          class="absolute inset-x-0 top-8 z-30 flex justify-center pointer-events-none"
        >
          <div
            class="flex h-12 w-12 items-center justify-center rounded-full border border-red-500/30 bg-zinc-950/85 shadow-2xl shadow-red-950/30 backdrop-blur-xl"
          >
            <span class="cinema-loading-spinner" />
          </div>
        </div>

        <!-- Cinemas Grid -->
        <div
          v-if="cinemas.length > 0"
          :class="[
            'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 transition-all duration-300',
            loadingCinemas ? 'opacity-45 blur-[1px]' : 'opacity-100 blur-0',
          ]"
        >
          <div
            v-for="cinema in cinemas"
            :key="cinema.id"
            class="group bg-zinc-900/40 backdrop-blur-md rounded-[2.5rem] overflow-hidden border border-white/5 transition-all duration-500 hover:border-red-600/40 hover:shadow-[0_20px_50px_rgba(220,38,38,0.15)] hover:-translate-y-2"
          >
            <!-- Cinema Image -->
            <div class="aspect-video relative overflow-hidden">
              <img
                :src="getCinemaImage(cinema)"
                :alt="cinema.name"
                class="w-full h-full object-cover transition-transform duration-1000"
                referrerpolicy="no-referrer"
              />
              <div class="absolute inset-0 bg-gradient-to-t from-zinc-950 via-transparent to-transparent" />

              <!-- City Badge -->
              <div class="absolute top-4 left-4 px-3 py-1 bg-red-600 rounded-lg shadow-lg">
                <span class="text-[10px] font-black text-white uppercase tracking-widest">
                  {{ cinema.city?.name ?? 'Cinema' }}
                </span>
              </div>
            </div>

            <!-- Cinema Details -->
            <div class="p-8">
              <p class="mb-2 text-[10px] font-black uppercase tracking-[0.16em] text-red-500">
                {{ getCinemaChain(cinema)?.name ?? 'Cinema' }}
              </p>
              <h2 class="text-2xl font-black text-white mb-4 group-hover:text-red-500 transition-colors uppercase tracking-tight">
                {{ cinema.name }}
              </h2>

              <div class="space-y-4 mb-8">
                <div class="flex items-start gap-3 text-gray-400 text-sm">
                  <MapPin class="w-5 h-5 text-red-600 flex-shrink-0" />
                  <p class="leading-relaxed">{{ cinema.address }}</p>
                </div>
                <div class="flex items-center gap-3 text-gray-400 text-sm">
                  <Phone class="w-5 h-5 text-red-600" />
                  <p>+84 123 456 789</p>
                </div>
                <div class="flex items-center gap-3 text-gray-400 text-sm">
                  <Clock class="w-5 h-5 text-red-600" />
                  <p>08:00 - 23:00</p>
                </div>
              </div>

              <!-- Amenities -->
              <div class="flex flex-wrap gap-2 mb-8">
                <div
                  v-for="amenity in amenities"
                  :key="amenity"
                  class="flex items-center gap-1.5 px-3 py-1.5 bg-white/5 rounded-xl border border-white/5 text-[10px] font-black text-gray-400 uppercase tracking-tighter"
                >
                  <component :is="getAmenityIcon(amenity)" class="w-3 h-3 text-red-600" />
                  {{ amenity }}
                </div>
              </div>

              <button class="w-full py-4 bg-white text-black rounded-2xl font-black uppercase tracking-widest hover:bg-red-600 hover:text-white transition-all flex items-center justify-center gap-3">
                <MapPin class="w-5 h-5" />
                {{ languageStore.language === 'en' ? 'View on Map' : 'Xem trên bản đồ' }}
              </button>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div
          v-else
          :class="[
            'text-center py-32 bg-zinc-900/30 rounded-[4rem] border border-dashed border-white/10 transition-opacity duration-300',
            loadingCinemas ? 'opacity-45' : 'opacity-100',
          ]"
        >
          <div class="w-24 h-24 bg-white/5 rounded-full flex items-center justify-center mx-auto mb-8">
            <MapPin class="w-12 h-12 text-gray-600" />
          </div>
          <h3 class="text-3xl font-black text-white mb-4 uppercase italic">
            {{ languageStore.language === 'en' ? 'No Cinemas Found' : 'Không tìm thấy rạp' }}
          </h3>
          <p class="text-gray-500">
            {{ languageStore.language === 'en' ? 'Try searching for a different location.' : 'Hãy thử tìm kiếm một địa điểm khác.' }}
          </p>
        </div>
      </div>

      <div v-if="pageCount > 1" class="mt-12 flex justify-center">
        <n-pagination
          v-model:page="page"
          :page-count="pageCount"
          :page-slot="6"
          :disabled="loadingCinemas"
          class="cinema-pagination"
          @update:page="fetchCinemaList"
        />
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

.cinema-pagination :deep(.n-pagination-item) {
  border-color: rgba(255, 255, 255, 0.08);
  background-color: rgba(255, 255, 255, 0.05);
  color: rgb(212 212 216);
  font-weight: 800;
  transition:
    border-color 0.2s ease,
    background-color 0.2s ease,
    color 0.2s ease,
    transform 0.2s ease;
}

.cinema-pagination :deep(.n-pagination-item:not(.n-pagination-item--disabled):hover) {
  border-color: rgba(220, 38, 38, 0.65);
  background-color: rgba(220, 38, 38, 0.14);
  color: white;
  transform: translateY(-1px);
}

.cinema-pagination :deep(.n-pagination-item.n-pagination-item--active) {
  border-color: rgb(220 38 38);
  background-color: rgb(220 38 38);
  color: white;
  box-shadow: 0 12px 28px rgba(220, 38, 38, 0.28);
}

.cinema-pagination :deep(.n-pagination-item.n-pagination-item--disabled) {
  opacity: 0.35;
}

.cinema-loading-spinner {
  width: 1.35rem;
  height: 1.35rem;
  border-radius: 9999px;
  border: 3px solid rgba(255, 255, 255, 0.16);
  border-top-color: rgb(220 38 38);
  animation: cinema-loading-spin 0.75s linear infinite;
}

@keyframes cinema-loading-spin {
  to {
    transform: rotate(360deg);
  }
}
</style>
