<script setup lang="ts">
import { onMounted } from 'vue'
import { MOVIES } from '@/data/mockData'
import { useLanguageStore } from '@/stores/language'
import { Film, TrendingUp, Newspaper, Gift, ChevronRight } from 'lucide-vue-next'
import MovieCard from '../../components/ui/MovieCard.vue'
import HeroSlider from '@/features/client/views/home/components/HeroSlider.vue'
import { Swiper, SwiperSlide } from 'swiper/vue'
import { useMovie } from '@/features/client/composables/useMovie'
import { useGenre } from '@/features/client/composables/useGenre'
import type { ClientMovie } from '@/features/client/types/movie.type'

const languageStore = useLanguageStore()
const featuredMovies = MOVIES.slice(0, 3)
const {
  topMovies,
  nowPlayingMovies,
  comingSoonMovies,
  fetchTopMovies,
  fetchNowPlayingMovies,
  fetchComingSoonMovies,
} = useMovie()
const { genres, fetchGenres } = useGenre()

onMounted(() => {
  fetchTopMovies()
  fetchNowPlayingMovies()
  fetchComingSoonMovies()
  fetchGenres()
})

const topMovieMetric = (movie: ClientMovie) => {
  if (typeof movie.soldTicketsCount === 'number') {
    return languageStore.language === 'en'
      ? `${movie.soldTicketsCount} tickets`
      : `${movie.soldTicketsCount} vé`
  }

  return movie.rating
}

const news = [
  {
    id: 1,
    title: 'Dune: Part Two breaks box office records',
    date: '2024-03-15',
    image: 'https://picsum.photos/seed/news1/400/250',
  },
  {
    id: 2,
    title: 'New trailer for Joker: Folie à Deux released',
    date: '2024-03-12',
    image: 'https://picsum.photos/seed/news2/400/250',
  },
  {
    id: 3,
    title: 'Christopher Nolan wins Best Director at Oscars',
    date: '2024-03-11',
    image: 'https://picsum.photos/seed/news3/400/250',
  },
]

const promos = [
  {
    id: 1,
    title: 'Student Discount: 20% Off',
    code: 'STUDENT20',
    image: 'https://picsum.photos/seed/promo1/400/250',
  },
  {
    id: 2,
    title: 'Weekend Combo: Popcorn + Soda',
    code: 'WEEKEND',
    image: 'https://picsum.photos/seed/promo2/400/250',
  },
]
</script>

<template>
  <div>
    <!-- Hero Slider -->
    <HeroSlider :movies="featuredMovies" />

    <!-- Movie Sections -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12 relative z-10 space-y-24">
      <!-- Top 5 Movies -->
      <section>
        <div class="flex items-center justify-between mb-12">
          <h2
            class="text-2xl md:text-4xl font-black text-white tracking-tight uppercase italic flex items-center gap-4"
          >
            <TrendingUp class="w-10 h-10 text-yellow-500" />
            {{ languageStore.t('home.top_movies') }}
          </h2>
        </div>
        <Swiper
          :loop="true"
          :grabCursor="true"
          :slidesPerView="2"
          :spaceBetween="24"
          :breakpoints="{
            320: { slidesPerView: 2, spaceBetween: 16 },
            640: { slidesPerView: 3, spaceBetween: 20 },
            1024: { slidesPerView: 4, spaceBetween: 24 },
          }"
          :observer="true"
          :observeParents="true"
          class="py-12"
        >
          <SwiperSlide
            v-for="(movie, index) in topMovies"
            :key="movie.id"
            class="transition-all duration-500"
          >
            <div class="group relative">
              <div
                class="absolute -left-12 top-1/2 -translate-y-1/2 text-[16rem] font-black italic z-0 pointer-events-none select-none leading-none transition-all duration-700 group-hover:-translate-x-4"
              >
                <span
                  class="text-transparent bg-clip-text bg-gradient-to-b from-white/20 to-transparent outline-text"
                >
                  {{ index + 1 }}
                </span>
              </div>

              <!-- GIỮ NGUYÊN -->
              <router-link
                :to="{
                  name: 'movie-detail',
                  params: { id: movie.id },
                }"
                class="relative z-10 block"
              >
                <div
                  class="movie-card relative aspect-[2/3] rounded-3xl overflow-hidden shadow-2xl shadow-black/50 border border-white/10 group-hover:border-red-600/50 transition-all duration-500"
                >
                  <img
                    :src="movie.poster"
                    :alt="movie.title"
                    class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                    referrerpolicy="no-referrer"
                  />

                  <!-- Overlay Info -->
                  <div
                    class="absolute inset-0 bg-gradient-to-t from-black via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 flex flex-col justify-end p-6"
                  >
                    <div class="flex items-center gap-2 mb-2">
                      <div
                        class="flex items-center gap-1 text-yellow-500 bg-black/60 backdrop-blur-md px-2 py-1 rounded-lg"
                      >
                        <TrendingUp class="w-4 h-4" />
                        <span class="text-sm font-bold">{{ topMovieMetric(movie) }}</span>
                      </div>
                      <span
                        class="px-2 py-1 bg-red-600 text-white text-[10px] font-black rounded uppercase tracking-widest"
                      >
                        TOP {{ index + 1 }}
                      </span>
                    </div>

                    <h3 class="text-xl font-black text-white uppercase tracking-tight mb-4">
                      {{ languageStore.language === 'en' ? movie.title : movie.titleVi }}
                    </h3>

                    <button
                      class="w-full py-3 bg-white text-black rounded-xl font-black text-sm hover:bg-red-600 hover:text-white transition-colors"
                    >
                      {{ languageStore.t('movie.book_now') }}
                    </button>
                  </div>

                  <!-- Static Rating -->
                  <div
                    class="absolute top-4 right-4 flex items-center gap-1 text-yellow-500 bg-black/60 backdrop-blur-md px-3 py-1.5 rounded-xl border border-white/10 group-hover:opacity-0 transition-opacity"
                  >
                    <TrendingUp class="w-4 h-4" />
                    <span class="text-sm font-bold">{{ topMovieMetric(movie) }}</span>
                  </div>

                  <div class="absolute top-4 left-4 flex items-center gap-1.5">
                    <span
                      class="px-3 py-1.5 rounded-xl text-[11px] font-black uppercase tracking-widest text-white border border-white/20 backdrop-blur-md"
                      :class="index === 0 ? 'bg-yellow-500/80 border-yellow-400/50' : 'bg-black/60'"
                    >
                      {{ index === 0 ? '🏆 TOP 1' : `TOP ${index + 1}` }}
                    </span>
                  </div>
                </div>
              </router-link>
            </div>
          </SwiperSlide>
        </Swiper>
      </section>

      <!-- Now Playing -->
      <section>
        <div class="flex items-center justify-between mb-8">
          <h2
            class="text-2xl md:text-3xl font-black text-white tracking-tight uppercase italic flex items-center gap-3"
          >
            <div class="w-2 h-8 bg-red-600 rounded-full" />
            {{ languageStore.t('home.now_playing') }}
          </h2>
          <router-link
            to="/movies"
            class="text-red-500 font-bold hover:underline flex items-center gap-1"
          >
            View All <ChevronRight class="w-4 h-4" />
          </router-link>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
          <MovieCard v-for="movie in nowPlayingMovies" :key="movie.id" :movie="movie" />
        </div>
      </section>

      <!-- Promotions -->
      <section>
        <div class="flex items-center justify-between mb-8">
          <h2
            class="text-2xl md:text-3xl font-black text-white tracking-tight uppercase italic flex items-center gap-3"
          >
            <Gift class="w-8 h-8 text-emerald-500" />
            {{ languageStore.t('home.promotions') }}
          </h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <div
            v-for="promo in promos"
            :key="promo.id"
            class="relative group overflow-hidden rounded-3xl aspect-[16/9] md:aspect-[21/9]"
          >
            <img
              :src="promo.image"
              class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
              referrerpolicy="no-referrer"
            />
            <div
              class="absolute inset-0 bg-gradient-to-t from-black via-black/20 to-transparent p-8 flex flex-col justify-end"
            >
              <h3 class="text-2xl font-black text-white mb-2">
                {{ promo.title }}
              </h3>
              <div class="flex items-center justify-between">
                <span
                  class="px-4 py-1 bg-white/10 backdrop-blur-md rounded-full text-white font-mono text-sm border border-white/20"
                >
                  CODE: {{ promo.code }}
                </span>
                <button
                  class="px-6 py-2 bg-red-600 text-white rounded-xl font-bold hover:bg-red-700 transition-colors"
                >
                  CLAIM
                </button>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Rewards Banner -->
      <section
        class="bg-gradient-to-r from-zinc-900 to-black border border-white/10 rounded-3xl p-8 md:p-12 flex flex-col md:flex-row items-center justify-between gap-8"
      >
        <div class="max-w-xl text-center md:text-left">
          <h2 class="text-3xl font-black text-white mb-4">JOIN CINEMAX REWARDS</h2>
          <p class="text-gray-400 text-lg">
            Get 10% off your first booking and earn points for every ticket purchased.
          </p>
        </div>
        <button
          class="px-10 py-4 bg-white text-black rounded-2xl font-black hover:bg-gray-200 transition-colors whitespace-nowrap"
        >
          SIGN UP NOW
        </button>
      </section>

      <!-- News -->
      <section>
        <div class="flex items-center justify-between mb-8">
          <h2
            class="text-2xl md:text-3xl font-black text-white tracking-tight uppercase italic flex items-center gap-3"
          >
            <Newspaper class="w-8 h-8 text-blue-500" />
            {{ languageStore.t('home.news') }}
          </h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div v-for="item in news" :key="item.id" class="group cursor-pointer">
            <div class="aspect-video overflow-hidden rounded-2xl mb-4 border border-white/5">
              <img
                :src="item.image"
                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                referrerpolicy="no-referrer"
              />
            </div>
            <p class="text-gray-500 text-xs uppercase font-bold mb-2">
              {{ item.date }}
            </p>
            <h3
              class="text-white font-bold group-hover:text-red-500 transition-colors line-clamp-2"
            >
              {{ item.title }}
            </h3>
          </div>
        </div>
      </section>

      <!-- Genres -->
      <section>
        <div class="flex items-center justify-between mb-8">
          <h2
            class="text-2xl md:text-3xl font-black text-white tracking-tight uppercase italic flex items-center gap-3"
          >
            <Film class="w-8 h-8 text-purple-500" />
            {{ languageStore.language === 'en' ? 'Browse by Genre' : 'Khám phá theo thể loại' }}
          </h2>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
          <button
            v-for="genre in genres"
            :key="genre.id"
            class="p-6 bg-zinc-900 rounded-2xl border border-white/5 hover:border-red-600 transition-all text-center group"
          >
            <div class="text-gray-400 group-hover:text-white font-bold transition-colors">
              {{ genre.name }}
            </div>
          </button>
        </div>
      </section>

      <!-- Coming Soon -->
      <section>
        <div class="flex items-center justify-between mb-8">
          <h2
            class="text-2xl md:text-3xl font-black text-white tracking-tight uppercase italic flex items-center gap-3"
          >
            <div class="w-2 h-8 bg-orange-500 rounded-full" />
            {{ languageStore.t('home.coming_soon') }}
          </h2>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 opacity-80">
          <MovieCard
            v-for="movie in comingSoonMovies"
            :key="`soon-${movie.id}`"
            :movie="movie"
          />
        </div>
      </section>
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

.outline-text {
  -webkit-text-stroke: 2px rgba(255, 255, 255, 0.1);
}
</style>
