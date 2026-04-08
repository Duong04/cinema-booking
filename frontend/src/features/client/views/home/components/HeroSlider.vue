<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'
import { type Movie } from '@/data/mockData'
import { useLanguageStore } from '@/stores/language'
import { Star, Play, Info, ChevronLeft, ChevronRight } from 'lucide-vue-next'

const props = defineProps<{
  movies: Movie[]
}>()

const languageStore = useLanguageStore()
const currentIndex = ref(0)
const isTransitioning = ref(false)
let autoPlayInterval: ReturnType<typeof setInterval> | null = null

const nextSlide = () => {
  if (isTransitioning.value) return
  isTransitioning.value = true
  currentIndex.value = (currentIndex.value + 1) % props.movies.length
  setTimeout(() => {
    isTransitioning.value = false
  }, 1000)
}

const prevSlide = () => {
  if (isTransitioning.value) return
  isTransitioning.value = true
  currentIndex.value = (currentIndex.value - 1 + props.movies.length) % props.movies.length
  setTimeout(() => {
    isTransitioning.value = false
  }, 1000)
}

const startAutoPlay = () => {
  autoPlayInterval = setInterval(nextSlide, 8000)
}

const stopAutoPlay = () => {
  if (autoPlayInterval) clearInterval(autoPlayInterval)
}

onMounted(() => {
  startAutoPlay()
})

onUnmounted(() => {
  stopAutoPlay()
})
</script>

<template>
  <section class="relative min-h-[70vh] md:h-[85vh] w-full overflow-hidden bg-zinc-950">
    <!-- Slides -->
    <div class="relative h-full w-full">
      <div
        v-for="(movie, index) in movies"
        :key="movie.id"
        class="absolute inset-0 transition-opacity duration-1000 ease-in-out"
        :class="[index === currentIndex ? 'opacity-100 z-10' : 'opacity-0 z-0']"
      >
        <!-- Background: Video or Image -->
        <div class="absolute inset-0">
          <!-- Poster Image (Always present as fallback/loading state) -->
          <img
            :src="movie.backdrop"
            :alt="movie.title"
            class="w-full h-full object-cover opacity-60 md:opacity-100"
            referrerpolicy="no-referrer"
          />

          <!-- Video Overlay -->
          <video
            v-if="movie.videoUrl && index === currentIndex"
            :key="movie.videoUrl"
            autoplay
            muted
            loop
            playsinline
            class="absolute inset-0 w-full h-full object-cover opacity-60 md:opacity-100 transition-opacity duration-1000"
            @loadeddata="(e: any) => (e.target.style.opacity = 1)"
            style="opacity: 0"
          >
            <source :src="movie.videoUrl" type="video/mp4" />
          </video>

          <div
            class="absolute inset-0 bg-gradient-to-r from-black via-black/40 to-transparent z-10"
          />
          <div
            class="absolute inset-0 bg-gradient-to-t from-black via-black/20 to-transparent z-10"
          />
        </div>

        <!-- Content -->
        <div
          class="relative z-20 h-full w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-24 md:pt-32 flex flex-col justify-center"
        >
          <div
            class="max-w-2xl transform transition-all duration-1000 delay-300"
            :class="[
              index === currentIndex ? 'translate-y-0 opacity-100' : 'translate-y-10 opacity-0',
            ]"
          >
            <div class="flex items-center gap-3 mb-4">
              <span
                v-if="movie.videoUrl"
                class="px-3 py-1 bg-red-600 text-white text-[10px] font-black rounded uppercase tracking-widest flex items-center gap-1.5 shadow-lg shadow-red-600/20"
              >
                <div class="w-1.5 h-1.5 bg-white rounded-full animate-pulse" />
                Live Trailer
              </span>
              <span
                v-else
                class="px-3 py-1 bg-white/10 backdrop-blur-md text-white text-[10px] font-black rounded uppercase tracking-widest border border-white/10"
              >
                Featured
              </span>
              <div class="flex items-center gap-1 text-yellow-500 drop-shadow-md">
                <Star class="w-4 h-4 fill-current" />
                <span class="text-sm font-bold">{{ movie.rating }}</span>
              </div>
            </div>
            <h1
              class="text-4xl md:text-7xl font-black text-white mb-4 tracking-tighter uppercase leading-none drop-shadow-[0_10px_10px_rgba(0,0,0,0.5)]"
            >
              {{ languageStore.language === 'en' ? movie.title : movie.titleVi }}
            </h1>
            <p
              class="text-base md:text-lg text-gray-200 mb-8 line-clamp-3 font-medium leading-relaxed drop-shadow-lg"
            >
              {{ languageStore.language === 'en' ? movie.description : movie.descriptionVi }}
            </p>
            <div class="flex flex-wrap gap-4">
              <router-link
                :to="{
                  name: 'movie-detail',
                  params: { id: movie.id }
                }"
                class="px-6 md:px-8 py-3 md:py-4 bg-red-600 text-white rounded-2xl font-bold flex items-center gap-2 hover:bg-red-700 transition-all transform hover:scale-105 shadow-xl shadow-red-600/20"
              >
                <Play class="w-5 h-5 fill-current" />
                {{ languageStore.t('movie.book_now') }}
              </router-link>
              <router-link
                :to="{
                  name: 'movie-detail',
                  params: { id: movie.id }
                }"
                class="px-6 md:px-8 py-3 md:py-4 bg-white/10 backdrop-blur-md text-white border border-white/20 rounded-2xl font-bold flex items-center gap-2 hover:bg-white/20 transition-all"
              >
                <Info class="w-5 h-5" />
                {{ languageStore.t('movie.details') }}
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Controls -->
    <div class="absolute bottom-12 right-4 sm:right-8 lg:right-12 z-20 flex items-center gap-4">
      <button
        @click="prevSlide"
        class="p-4 bg-white/5 backdrop-blur-md border border-white/10 rounded-full text-white hover:bg-white/20 transition-all active:scale-90"
      >
        <ChevronLeft class="w-6 h-6" />
      </button>
      <button
        @click="nextSlide"
        class="p-4 bg-white/5 backdrop-blur-md border border-white/10 rounded-full text-white hover:bg-white/20 transition-all active:scale-90"
      >
        <ChevronRight class="w-6 h-6" />
      </button>
    </div>

    <!-- Indicators -->
    <div class="absolute bottom-12 left-1/2 -translate-x-1/2 z-20 flex items-center gap-2">
      <button
        v-for="(_, index) in movies"
        :key="index"
        @click="currentIndex = index"
        class="h-1.5 rounded-full transition-all duration-500"
        :class="[index === currentIndex ? 'w-8 bg-red-600' : 'w-2 bg-white/20']"
      ></button>
    </div>
  </section>
</template>

<style scoped></style>
