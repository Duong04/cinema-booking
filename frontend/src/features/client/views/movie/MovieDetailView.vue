<script setup lang="ts">
import { useRoute, useRouter } from 'vue-router'
import { useLanguageStore } from '@/stores/language'
import { useBookingStore } from '@/stores/client/booking'
import { Star, Clock, Play, ChevronLeft, Heart, X } from 'lucide-vue-next'
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { useMovie } from '@/features/client/composables/useMovie'
import { Swiper, SwiperSlide } from 'swiper/vue'
import { Navigation } from 'swiper/modules'

const route = useRoute()
const router = useRouter()
const languageStore = useLanguageStore()
const bookingStore = useBookingStore()
const {
  selectedMovie: movie,
  relatedMovies,
  fetchMovieDetail,
  fetchRelatedMovies,
} = useMovie()

const showTrailer = ref(false)
let previousBodyOverflow = ''
let previousHtmlOverflow = ''
const castSliderModules = [Navigation]
const fallbackCast = [
  'Timothee Chalamet',
  'Zendaya',
  'Rebecca Ferguson',
  'Austin Butler',
  'CR7',
]

const routeMovieSlug = computed(() => String(route.params.id ?? ''))

const isWishlisted = () => (movie.value ? bookingStore.wishlist.includes(movie.value.id) : false)

const movieCast = computed(() => {
  return fallbackCast
})

const statusLabel = computed(() => {
  if (movie.value?.status === 'now_showing') {
    return languageStore.language === 'en' ? 'Now Playing' : 'Đang chiếu'
  }

  return languageStore.language === 'en' ? 'Coming Soon' : 'Sắp chiếu'
})

const statusClass = computed(() =>
  movie.value?.status === 'now_showing'
    ? 'border-emerald-400/40 bg-emerald-500/90 text-white shadow-emerald-500/20'
    : 'border-amber-300/40 bg-amber-500/90 text-zinc-950 shadow-amber-500/20',
)

const trailerSource = computed(() => {
  const trailerUrl = movie.value?.trailer_url?.trim()
  if (!trailerUrl) return null

  const addAutoplay = (url: string) => {
    const separator = url.includes('?') ? '&' : '?'
    return `${url}${separator}autoplay=1`
  }

  try {
    const url = new URL(trailerUrl)
    const hostname = url.hostname.replace(/^www\./, '')
    const isYoutube = hostname === 'youtube.com' || hostname === 'youtu.be'

    if (isYoutube) {
      const videoId =
        hostname === 'youtu.be'
          ? url.pathname.slice(1)
          : url.pathname.startsWith('/embed/')
            ? url.pathname.split('/')[2]
            : url.pathname.startsWith('/shorts/')
              ? url.pathname.split('/')[2]
              : url.searchParams.get('v')

      if (videoId) {
        return {
          type: 'iframe' as const,
          src: `https://www.youtube.com/embed/${videoId}?autoplay=1`,
        }
      }
    }

    if (hostname === 'vimeo.com') {
      const videoId = url.pathname.split('/').filter(Boolean)[0]
      if (videoId) {
        return {
          type: 'iframe' as const,
          src: `https://player.vimeo.com/video/${videoId}?autoplay=1`,
        }
      }
    }
  } catch {
    
  }

  if (/\.(mp4|webm|ogg)(\?.*)?$/i.test(trailerUrl)) {
    return { type: 'video' as const, src: trailerUrl }
  }

  return { type: 'iframe' as const, src: addAutoplay(trailerUrl) }
})

const handleBookNow = () => {
  if (movie.value) {
    router.push({ name: 'booking-showtime', params: { movieId: movie.value.id } })
  }
}

const openTrailer = () => {
  if (trailerSource.value) {
    showTrailer.value = true
  }
}

const lockPageScroll = () => {
  previousBodyOverflow = document.body.style.overflow
  previousHtmlOverflow = document.documentElement.style.overflow
  document.body.style.overflow = 'hidden'
  document.documentElement.style.overflow = 'hidden'
}

const unlockPageScroll = () => {
  document.body.style.overflow = previousBodyOverflow
  document.documentElement.style.overflow = previousHtmlOverflow
}

onMounted(() => {
  fetchMovieDetail(routeMovieSlug.value)
  fetchRelatedMovies(routeMovieSlug.value)
})

watch(routeMovieSlug, (slug) => {
  showTrailer.value = false
  fetchMovieDetail(slug)
  fetchRelatedMovies(slug)
})

watch(showTrailer, (isOpen) => {
  if (isOpen) {
    lockPageScroll()
  } else {
    unlockPageScroll()
  }
})

onUnmounted(() => {
  unlockPageScroll()
})
</script>

<template>
  <div v-if="movie">
    <div class="relative h-[60vh] w-full bg-zinc-950">
      <img
        :src="movie.banner_url ?? movie.poster_url"
        :alt="movie.title"
        class="w-full h-full object-cover opacity-60 md:opacity-100"
        referrerpolicy="no-referrer"
        @error="
          (e: any) =>
            (e.target.src =
              'https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?q=80&w=2070&auto=format&fit=crop')
        "
      />
      <div class="absolute inset-0 bg-gradient-to-t from-black via-black/40 to-transparent" />
      <button
        @click="router.back()"
        class="absolute top-24 left-4 md:left-8 p-3 bg-black/50 backdrop-blur-md rounded-full text-white hover:bg-black/70 transition-colors"
      >
        <ChevronLeft class="w-6 h-6" />
      </button>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8 relative z-10">
      <div class="flex flex-col md:flex-row gap-8">
        <div class="w-64 flex-shrink-0 mx-auto md:mx-0">
          <img
            :src="movie.poster_url"
            :alt="movie.title"
            class="w-full rounded-2xl shadow-2xl border border-white/10"
            referrerpolicy="no-referrer"
          />
        </div>

        <div class="flex-1 pt-4">
          <div class="flex flex-wrap items-center gap-4 mb-4">
            <span class="px-3 py-1 bg-red-600 text-white text-xs font-black rounded uppercase">{{
              movie.rating ?? 'P'
            }}</span>
            <span
              :class="[
                'rounded-lg border px-3 py-1 text-xs font-black uppercase tracking-wider shadow-lg',
                statusClass,
              ]"
            >
              {{ statusLabel }}
            </span>
            <div class="flex items-center gap-1 text-yellow-500">
              <Star class="w-5 h-5 fill-current" />
              <span class="text-xl font-bold">{{ movie.rating_score ?? '-' }}</span>
            </div>
            <div class="flex items-center gap-1 text-gray-400">
              <Clock class="w-5 h-5" />
              <span class="text-sm"
                >{{ movie.duration_minutes }} {{ languageStore.t('movie.duration') }}</span
              >
            </div>
          </div>

          <h1 class="text-4xl md:text-6xl font-black text-white mb-6 tracking-tight uppercase">
            {{ movie.title }}
          </h1>

          <div class="flex flex-wrap gap-2 mb-8">
            <span
              v-for="genre in movie.genres"
              :key="genre.id"
              class="px-4 py-1.5 bg-white/5 border border-white/10 rounded-full text-sm text-gray-300"
            >
              {{ genre.name }}
            </span>
          </div>

          <div class="flex gap-4 mb-12">
            <button
              @click="handleBookNow"
              class="flex-1 md:flex-none px-12 py-4 bg-red-600 text-white rounded-2xl font-black flex items-center justify-center gap-2 hover:bg-red-700 transition-all transform hover:scale-105 shadow-xl shadow-red-600/20"
            >
              <Play class="w-5 h-5 fill-current" />
              {{ languageStore.t('movie.book_now') }}
            </button>
            <button
              @click="openTrailer"
              :disabled="!trailerSource"
              class="px-8 py-4 bg-white/5 border border-white/10 text-white rounded-2xl font-black flex items-center justify-center gap-2 hover:bg-white/10 transition-all disabled:cursor-not-allowed disabled:opacity-40"
            >
              <Play class="w-5 h-5" />
              Trailer
            </button>
            <button
              @click="bookingStore.toggleWishlist(movie.id)"
              :class="[
                'p-4 rounded-2xl border transition-all',
                isWishlisted()
                  ? 'bg-red-600/20 border-red-600 text-red-500'
                  : 'bg-white/5 border-white/10 text-white hover:bg-white/10',
              ]"
            >
              <Heart :class="['w-6 h-6', isWishlisted() && 'fill-current']" />
            </button>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-12 mt-16">
        <div class="lg:col-span-2 space-y-12">
          <section>
            <h2
              class="text-2xl font-black text-white mb-6 uppercase tracking-tight italic flex items-center gap-3"
            >
              <div class="w-1.5 h-6 bg-red-600 rounded-full" />
              {{ languageStore.t('movie.synopsis') }}
            </h2>
            <p class="text-gray-400 text-lg leading-relaxed">
              {{ movie.description }}
            </p>
          </section>

          <section>
            <h2
              class="text-2xl font-black text-white mb-6 uppercase tracking-tight italic flex items-center gap-3"
            >
              <div class="w-1.5 h-6 bg-red-600 rounded-full" />
              {{ languageStore.language === 'en' ? 'Cast' : 'Diễn viên' }}
            </h2>
            <div class="relative">
              <Swiper
                :modules="castSliderModules"
                :navigation="{
                  prevEl: '.cast-swiper-prev',
                  nextEl: '.cast-swiper-next',
                }"
                :grabCursor="true"
                :slidesPerView="2"
                :spaceBetween="16"
                :breakpoints="{
                  640: { slidesPerView: 3, spaceBetween: 20 },
                  1024: { slidesPerView: 4, spaceBetween: 24 },
                }"
                :observer="true"
                :observeParents="true"
                class="pb-2"
              >
                <SwiperSlide v-for="actor in movieCast" :key="actor">
                  <div class="group">
                    <div
                      class="aspect-[3/4] rounded-2xl overflow-hidden bg-zinc-900 mb-3 border border-white/5"
                    >
                      <img
                        :src="`https://picsum.photos/seed/${actor}/300/400`"
                        :alt="actor"
                        class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-500"
                        referrerpolicy="no-referrer"
                      />
                    </div>
                    <p class="text-white font-bold text-sm">{{ actor }}</p>
                    <p class="text-gray-500 text-xs uppercase font-black tracking-widest">Actor</p>
                  </div>
                </SwiperSlide>
              </Swiper>

              <button
                class="cast-swiper-prev hidden md:flex absolute left-0 top-[42%] z-10 -translate-x-1/2 -translate-y-1/2 p-3 bg-black/70 border border-white/10 rounded-full text-white hover:bg-red-600 transition-colors"
                type="button"
              >
                <ChevronLeft class="w-5 h-5" />
              </button>
              <button
                class="cast-swiper-next hidden md:flex absolute right-0 top-[42%] z-10 translate-x-1/2 -translate-y-1/2 p-3 bg-black/70 border border-white/10 rounded-full text-white hover:bg-red-600 transition-colors"
                type="button"
              >
                <ChevronLeft class="w-5 h-5 rotate-180" />
              </button>
            </div>
          </section>
        </div>

        <div class="space-y-12">
          <section>
            <h2
              class="text-2xl font-black text-white mb-6 uppercase tracking-tight italic flex items-center gap-3"
            >
              <div class="w-1.5 h-6 bg-red-600 rounded-full" />
              {{ languageStore.language === 'en' ? 'More Movies' : 'Phim khác' }}
            </h2>
            <div class="space-y-4">
              <div
                v-for="m in relatedMovies"
                :key="m.id"
                @click="
                  router.push({
                    name: 'movie-detail',
                    params: { id: m.slug ?? m.id },
                  })
                "
                class="flex gap-4 p-3 bg-white/5 border border-white/5 rounded-2xl hover:bg-white/10 transition-all cursor-pointer group"
              >
                <img
                  :src="m.poster_url"
                  :alt="m.title"
                  class="w-20 h-28 object-cover rounded-xl border border-white/10"
                  referrerpolicy="no-referrer"
                />
                <div class="flex flex-col justify-center">
                  <h3
                    class="text-white font-black uppercase text-sm group-hover:text-red-500 transition-colors"
                  >
                    {{ m.title }}
                  </h3>
                  <div class="flex items-center gap-2 mt-1">
                    <Star class="w-3 h-3 text-yellow-500 fill-current" />
                    <span class="text-gray-400 text-xs">{{ m.rating_score ?? '-' }}</span>
                  </div>
                </div>
              </div>
            </div>
          </section>
        </div>
      </div>
    </div>

    <!-- Trailer Modal -->
    <Transition name="trailer-modal">
      <div
        v-if="showTrailer && trailerSource"
        class="fixed inset-0 z-[100] flex items-center justify-center p-4 md:p-12"
      >
        <div
          class="absolute inset-0 bg-zinc-950/35 backdrop-blur-xl"
          @click="showTrailer = false"
        />
        <div
          class="relative w-full max-w-5xl overflow-hidden rounded-[1.75rem] border border-white/15 bg-zinc-950/80 shadow-[0_30px_120px_rgba(0,0,0,0.65)] ring-1 ring-white/10"
        >
          <div class="flex items-center justify-between border-b border-white/10 px-4 py-3 md:px-5">
            <div class="min-w-0">
              <p class="text-[10px] font-black uppercase tracking-[0.2em] text-red-500">
                Trailer
              </p>
              <h3 class="truncate text-sm font-bold text-white md:text-base">
                {{ movie.title }}
              </h3>
            </div>
            <button
              @click="showTrailer = false"
              class="ml-4 rounded-full border border-white/10 bg-white/10 p-2 text-white backdrop-blur-md transition-all hover:border-red-500/60 hover:bg-red-600"
            >
              <X class="h-5 w-5" />
            </button>
          </div>
          <div class="aspect-video bg-black">
            <iframe
              v-if="trailerSource.type === 'iframe'"
              :src="trailerSource.src"
              class="h-full w-full"
              frameborder="0"
              allow="
                accelerometer;
                autoplay;
                clipboard-write;
                encrypted-media;
                gyroscope;
                picture-in-picture;
              "
              allowfullscreen
            ></iframe>
            <video
              v-else
              :src="trailerSource.src"
              class="h-full w-full"
              controls
              autoplay
            />
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>

<style scoped>
.trailer-modal-enter-active,
.trailer-modal-leave-active {
  transition: opacity 220ms ease;
}

.trailer-modal-enter-active > div:last-child,
.trailer-modal-leave-active > div:last-child {
  transition:
    opacity 220ms ease,
    transform 260ms cubic-bezier(0.22, 1, 0.36, 1);
}

.trailer-modal-enter-from,
.trailer-modal-leave-to {
  opacity: 0;
}

.trailer-modal-enter-from > div:last-child,
.trailer-modal-leave-to > div:last-child {
  opacity: 0;
  transform: translateY(18px) scale(0.96);
}
</style>
