<script setup lang="ts">
import { computed, onMounted, ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { addDays, format } from 'date-fns'
import { Calendar as CalendarIcon, ChevronLeft, MapPin } from 'lucide-vue-next'
import { useLanguageStore } from '@/stores/language'
import { useBookingFlow } from '@/features/client/composables/useBookingFlow'
import { useCity } from '@/features/client/composables/useCity'
import { showtimeService } from '@/features/client/services/showtime.service'
import type { PublicMovie } from '@/features/client/types/movie.type'
import type { City } from '@/features/client/types/city.type'
import type { PublicShowtime } from '@/features/client/types/showtime.type'
import BookingStepper from './components/BookingStepper.vue'

const route = useRoute()
const router = useRouter()
const languageStore = useLanguageStore()
const { setShowtime, formatVND } = useBookingFlow()
const { cities, fetchCities } = useCity()

const movieId = computed(() => String(route.params.movieId ?? ''))
const selectedDate = ref(format(new Date(), 'yyyy-MM-dd'))
const selectedCityId = ref<string | null>(null)
const selectedCinemaId = ref<string | null>(null)
const showtimes = ref<PublicShowtime[]>([])
const currentMovie = ref<PublicMovie | null>(null)
const loading = ref(false)
const locating = ref(false)
const initialized = ref(false)
const dates = Array.from({ length: 7 }, (_, index) => addDays(new Date(), index))
const selectedCityStorageKey = 'cinema_booking_selected_city'

const movie = computed(() => currentMovie.value ?? showtimes.value.find((showtime) => showtime.movie)?.movie ?? null)

const cinemas = computed(() => {
  const map = new Map<string, NonNullable<PublicShowtime['room']>['cinema']>()
  showtimes.value.forEach((showtime) => {
    const cinema = showtime.room?.cinema
    if (cinema?.id) map.set(cinema.id, cinema)
  })
  return Array.from(map.values()).filter(Boolean)
})

const visibleCinemas = computed(() =>
  selectedCinemaId.value
    ? cinemas.value.filter((cinema) => cinema?.id === selectedCinemaId.value)
    : cinemas.value,
)

function showtimeTime(showtime: PublicShowtime) {
  return showtime.start_time?.split(' ')[1]?.slice(0, 5) ?? ''
}

function roomLabel(showtime: PublicShowtime) {
  return [showtime.room?.name, showtime.room?.type].filter(Boolean).join(' - ')
}

function cityCoordinate(city: City, key: 'latitude' | 'longitude') {
  const value = city[key]
  if (value === null || value === undefined || value === '') return null

  const coordinate = Number(value)
  return Number.isFinite(coordinate) ? coordinate : null
}

function distanceInKm(from: GeolocationCoordinates, city: City) {
  const latitude = cityCoordinate(city, 'latitude')
  const longitude = cityCoordinate(city, 'longitude')
  if (latitude === null || longitude === null) return Number.POSITIVE_INFINITY

  const toRadians = (degree: number) => (degree * Math.PI) / 180
  const earthRadiusKm = 6371
  const latitudeDelta = toRadians(latitude - from.latitude)
  const longitudeDelta = toRadians(longitude - from.longitude)
  const a =
    Math.sin(latitudeDelta / 2) ** 2 +
    Math.cos(toRadians(from.latitude)) *
      Math.cos(toRadians(latitude)) *
      Math.sin(longitudeDelta / 2) ** 2

  return earthRadiusKm * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a))
}

function nearestCity(coords: GeolocationCoordinates) {
  return cities.value.reduce<City | null>((nearest, city) => {
    if (!nearest) return city
    return distanceInKm(coords, city) < distanceInKm(coords, nearest) ? city : nearest
  }, null)
}

function loadStoredCity() {
  const storedCityId = window.localStorage.getItem(selectedCityStorageKey)
  if (storedCityId && cities.value.some((city) => city.id === storedCityId)) {
    selectedCityId.value = storedCityId
  }
}

async function applyCurrentLocationCity() {
  if (!navigator.geolocation || cities.value.length === 0) return

  locating.value = true
  try {
    const position = await new Promise<GeolocationPosition>((resolve, reject) => {
      navigator.geolocation.getCurrentPosition(resolve, reject, {
        enableHighAccuracy: false,
        maximumAge: 1000 * 60 * 10,
        timeout: 8000,
      })
    })
    console.log('Current position:', position)

    const city = nearestCity(position.coords)
    if (city?.id && Number.isFinite(distanceInKm(position.coords, city))) {
      selectedCityId.value = city.id
      window.localStorage.setItem(selectedCityStorageKey, city.id)
    }
  } catch {
    loadStoredCity()
  } finally {
    locating.value = false
  }
}

async function fetchShowtimes() {
  loading.value = true
  try {
    const res = await showtimeService.getPublicShowtimes({
      limit: 100,
      movie_id: movieId.value,
      show_date: selectedDate.value,
      city_id: selectedCityId.value ?? undefined,
    })
    showtimes.value = res.data
    currentMovie.value = showtimes.value.find((showtime) => showtime.movie)?.movie ?? currentMovie.value
    if (selectedCinemaId.value && !cinemas.value.some((cinema) => cinema?.id === selectedCinemaId.value)) {
      selectedCinemaId.value = null
    }
  } finally {
    loading.value = false
  }
}

function selectShowtime(showtime: PublicShowtime) {
  setShowtime(showtime)
  router.push({ name: 'booking-seats', params: { showtimeId: showtime.id } })
}

function selectCity(cityId: string | null) {
  selectedCityId.value = cityId
  selectedCinemaId.value = null

  if (cityId) {
    window.localStorage.setItem(selectedCityStorageKey, cityId)
  } else {
    window.localStorage.removeItem(selectedCityStorageKey)
  }
}

async function initializeShowtimes() {
  await fetchCities()
  loadStoredCity()
  await applyCurrentLocationCity()
  initialized.value = true
  fetchShowtimes()
}

onMounted(initializeShowtimes)

watch([selectedDate, selectedCityId], () => {
  if (initialized.value) fetchShowtimes()
})
</script>

<template>
  <div class="pt-24 pb-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <BookingStepper :current-step="1" />

    <div class="flex items-center gap-4 mb-8">
      <button @click="router.back()" class="p-2 bg-white/5 rounded-full text-white hover:bg-white/10">
        <ChevronLeft class="w-6 h-6" />
      </button>
      <h1 class="text-3xl font-black text-white uppercase tracking-tight italic">
        {{ languageStore.t('booking.select_showtime') }}
      </h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-12">
      <aside class="lg:col-span-1">
        <div class="sticky top-24 space-y-6">
          <img
            v-if="movie"
            :src="movie.poster_url"
            :alt="movie.title"
            class="w-full rounded-2xl shadow-2xl border border-white/10"
            referrerpolicy="no-referrer"
          />
          <h2 class="text-2xl font-black text-white uppercase tracking-tight">
            {{ movie?.title }}
          </h2>
        </div>
      </aside>

      <div class="lg:col-span-3 space-y-10">
        <section>
          <h3 class="text-white font-bold mb-4 flex items-center gap-2">
            <CalendarIcon class="w-5 h-5 text-red-500" />
            {{ languageStore.language === 'en' ? 'Select date' : 'Chọn ngày' }}
          </h3>
          <div class="flex gap-4 overflow-x-auto pb-4 scrollbar-hide">
            <button
              v-for="date in dates"
              :key="format(date, 'yyyy-MM-dd')"
              @click="selectedDate = format(date, 'yyyy-MM-dd')"
              :class="[
                'flex-shrink-0 w-20 py-4 rounded-2xl border transition-all flex flex-col items-center',
                selectedDate === format(date, 'yyyy-MM-dd')
                  ? 'bg-red-600 border-red-600 text-white'
                  : 'bg-white/5 border-white/10 text-gray-400 hover:bg-white/10',
              ]"
            >
              <span class="text-xs uppercase font-bold">{{ format(date, 'EEE') }}</span>
              <span class="text-xl font-black">{{ format(date, 'dd') }}</span>
            </button>
          </div>
        </section>

        <section>
          <h3 class="text-white font-bold mb-4 flex items-center gap-2">
            <MapPin class="w-5 h-5 text-red-500" />
            {{ languageStore.language === 'en' ? 'Select city' : 'Chọn thành phố' }}
          </h3>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <button
              @click="selectCity(null)"
              :class="[
                'p-4 rounded-2xl border text-left transition-all',
                selectedCityId === null
                  ? 'bg-red-600/20 border-red-600 text-white'
                  : 'bg-white/5 border-white/10 text-gray-400 hover:bg-white/10',
              ]"
            >
              <p class="font-bold text-white">{{ languageStore.language === 'en' ? 'All cities' : 'Tất cả thành phố' }}</p>
              <p class="text-xs text-gray-500 mt-1">
                {{ locating ? (languageStore.language === 'en' ? 'Detecting location...' : 'Đang xác định vị trí...') : (languageStore.language === 'en' ? 'Manual fallback' : 'Dự phòng thủ công') }}
              </p>
            </button>
            <button
              v-for="city in cities"
              :key="city.id"
              @click="selectCity(city.id)"
              :class="[
                'p-4 rounded-2xl border text-left transition-all',
                selectedCityId === city.id
                  ? 'bg-red-600/20 border-red-600 text-white'
                  : 'bg-white/5 border-white/10 text-gray-400 hover:bg-white/10',
              ]"
            >
              <p class="font-bold text-white">{{ city.name }}</p>
              <p class="text-xs text-gray-500 mt-1">
                {{ selectedCityId === city.id ? (languageStore.language === 'en' ? 'Selected city' : 'Thành phố đang chọn') : (languageStore.language === 'en' ? 'Show showtimes here' : 'Xem lịch chiếu tại đây') }}
              </p>
            </button>
          </div>
        </section>

        <section>
          <h3 class="text-white font-bold mb-4 flex items-center gap-2">
            <MapPin class="w-5 h-5 text-red-500" />
            {{ languageStore.language === 'en' ? 'Select cinema' : 'Chọn rạp' }}
          </h3>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <button
              @click="selectedCinemaId = null"
              :class="[
                'p-4 rounded-2xl border text-left transition-all',
                selectedCinemaId === null
                  ? 'bg-red-600/20 border-red-600 text-white'
                  : 'bg-white/5 border-white/10 text-gray-400 hover:bg-white/10',
              ]"
            >
              <p class="font-bold text-white">{{ languageStore.language === 'en' ? 'All cinemas' : 'Tất cả rạp' }}</p>
            </button>
            <button
              v-for="cinema in cinemas"
              :key="cinema?.id"
              @click="selectedCinemaId = cinema?.id ?? null"
              :class="[
                'p-4 rounded-2xl border text-left transition-all',
                selectedCinemaId === cinema?.id
                  ? 'bg-red-600/20 border-red-600 text-white'
                  : 'bg-white/5 border-white/10 text-gray-400 hover:bg-white/10',
              ]"
            >
              <p class="font-bold text-white">{{ cinema?.name }}</p>
              <p class="text-xs text-gray-500 mt-1">{{ cinema?.city?.name }}</p>
            </button>
          </div>
        </section>

        <section class="space-y-8">
          <div v-if="loading" class="py-16 text-center text-gray-500 font-bold">
            {{ languageStore.language === 'en' ? 'Loading showtimes...' : 'Đang tải suất chiếu...' }}
          </div>
          <template v-else>
            <div
              v-for="cinema in visibleCinemas"
              :key="cinema?.id"
              class="p-6 bg-zinc-900 rounded-3xl border border-white/5"
            >
              <h4 class="text-white font-bold mb-6 flex items-center gap-2">
                <div class="w-1 h-4 bg-red-600 rounded-full" />
                {{ cinema?.name }}
              </h4>
              <div class="flex flex-wrap gap-4">
                <button
                  v-for="showtime in showtimes.filter((item) => item.room?.cinema?.id === cinema?.id)"
                  :key="showtime.id"
                  @click="selectShowtime(showtime)"
                  class="px-6 py-3 bg-white/5 border border-white/10 rounded-xl text-white hover:bg-red-600 hover:border-red-600 transition-all group"
                >
                  <span class="block text-lg font-black">{{ showtimeTime(showtime) }}</span>
                  <span class="block text-[10px] uppercase font-bold text-gray-500 group-hover:text-white/80">
                    {{ roomLabel(showtime) }} • {{ formatVND(Number(showtime.base_price)) }}
                  </span>
                </button>
              </div>
            </div>
          </template>
          <div v-if="!loading && showtimes.length === 0" class="py-16 text-center text-gray-500 font-bold">
            {{ languageStore.language === 'en' ? 'No showtimes found.' : 'Không có suất chiếu phù hợp.' }}
          </div>
        </section>
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
