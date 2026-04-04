<script setup lang="ts">
import { useRoute, useRouter } from 'vue-router'
import { MOVIES } from '@/data/mockData'
import { useLanguageStore } from '@/stores/shared/language'
import { useBookingStore } from '@/stores/client/booking'
import { Star, Clock, Play, ChevronLeft, Heart, X } from 'lucide-vue-next'
import { ref, computed } from 'vue'

const route = useRoute()
const router = useRouter()
const languageStore = useLanguageStore()
const bookingStore = useBookingStore()

const movie = MOVIES.find((m) => m.id === route.params.id)
const showTrailer = ref(false)

const isWishlisted = () => (movie ? bookingStore.wishlist.includes(movie.id) : false)

const otherMovies = computed(() => MOVIES.filter((m) => m.id !== movie?.id).slice(0, 4))

const handleBookNow = () => {
  if (movie) {
    bookingStore.setCurrentBooking({ movieId: movie.id })
    router.push(`/booking/showtime/${movie.id}`)
  }
}
</script>

<template>
  <div v-if="movie">
    <div class="relative h-[60vh] w-full bg-zinc-950">
      <img
        :src="movie.backdrop"
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
            :src="movie.poster"
            :alt="movie.title"
            class="w-full rounded-2xl shadow-2xl border border-white/10"
            referrerpolicy="no-referrer"
          />
        </div>

        <div class="flex-1 pt-4">
          <div class="flex flex-wrap items-center gap-4 mb-4">
            <span class="px-3 py-1 bg-red-600 text-white text-xs font-black rounded uppercase">{{
              movie.ageRating
            }}</span>
            <div class="flex items-center gap-1 text-yellow-500">
              <Star class="w-5 h-5 fill-current" />
              <span class="text-xl font-bold">{{ movie.rating }}</span>
            </div>
            <div class="flex items-center gap-1 text-gray-400">
              <Clock class="w-5 h-5" />
              <span class="text-sm"
                >{{ movie.duration }} {{ languageStore.t('movie.duration') }}</span
              >
            </div>
          </div>

          <h1 class="text-4xl md:text-6xl font-black text-white mb-6 tracking-tight uppercase">
            {{ languageStore.language === 'en' ? movie.title : movie.titleVi }}
          </h1>

          <div class="flex flex-wrap gap-2 mb-8">
            <span
              v-for="genre in languageStore.language === 'en' ? movie.genres : movie.genresVi"
              :key="genre"
              class="px-4 py-1.5 bg-white/5 border border-white/10 rounded-full text-sm text-gray-300"
            >
              {{ genre }}
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
              @click="showTrailer = true"
              class="px-8 py-4 bg-white/5 border border-white/10 text-white rounded-2xl font-black flex items-center justify-center gap-2 hover:bg-white/10 transition-all"
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
              {{ languageStore.language === 'en' ? movie.description : movie.descriptionVi }}
            </p>
          </section>

          <section>
            <h2
              class="text-2xl font-black text-white mb-6 uppercase tracking-tight italic flex items-center gap-3"
            >
              <div class="w-1.5 h-6 bg-red-600 rounded-full" />
              {{ languageStore.language === 'en' ? 'Cast' : 'Diễn viên' }}
            </h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-6">
              <div v-for="actor in movie.cast" :key="actor" class="group">
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
                v-for="m in otherMovies"
                :key="m.id"
                @click="
                  router.push({
                    name: 'movie-detail',
                    params: { id: movie.id },
                  })
                "
                class="flex gap-4 p-3 bg-white/5 border border-white/5 rounded-2xl hover:bg-white/10 transition-all cursor-pointer group"
              >
                <img
                  :src="m.poster"
                  :alt="m.title"
                  class="w-20 h-28 object-cover rounded-xl border border-white/10"
                  referrerpolicy="no-referrer"
                />
                <div class="flex flex-col justify-center">
                  <h3
                    class="text-white font-black uppercase text-sm group-hover:text-red-500 transition-colors"
                  >
                    {{ languageStore.language === 'en' ? m.title : m.titleVi }}
                  </h3>
                  <div class="flex items-center gap-2 mt-1">
                    <Star class="w-3 h-3 text-yellow-500 fill-current" />
                    <span class="text-gray-400 text-xs">{{ m.rating }}</span>
                  </div>
                </div>
              </div>
            </div>
          </section>
        </div>
      </div>
    </div>

    <!-- Trailer Modal -->
    <div
      v-if="showTrailer"
      class="fixed inset-0 z-[100] flex items-center justify-center p-4 md:p-12"
    >
      <div class="absolute inset-0 bg-black/95 backdrop-blur-xl" @click="showTrailer = false" />
      <div
        class="relative w-full max-w-5xl aspect-video bg-black rounded-3xl overflow-hidden shadow-2xl border border-white/10"
      >
        <button
          @click="showTrailer = false"
          class="absolute top-4 right-4 z-10 p-2 bg-black/50 text-white rounded-full hover:bg-red-600 transition-colors"
        >
          <X class="w-6 h-6" />
        </button>
        <iframe
          :src="movie.trailerUrl + '?autoplay=1'"
          class="w-full h-full"
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
      </div>
    </div>
  </div>
</template>
