<script setup lang="ts">
import { useLanguageStore } from '@/stores/shared/language'
import { useBookingStore } from '@/stores/client/booking'
import { MOVIES } from '@/data/mockData'
import { Heart, Film } from 'lucide-vue-next'
import MovieCard from '../../components/ui/MovieCard.vue'

const languageStore = useLanguageStore()
const bookingStore = useBookingStore()

const wishlistMovies = computed(() => MOVIES.filter((m) => bookingStore.wishlist.includes(m.id)))
import { computed } from 'vue'
</script>

<template>
  <div class="pt-24 pb-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center gap-4 mb-12">
      <div class="w-12 h-12 bg-red-600/20 rounded-2xl flex items-center justify-center">
        <Heart class="w-6 h-6 text-red-500 fill-current" />
      </div>
      <h1 class="text-4xl font-black text-white uppercase italic tracking-tight">
        {{ languageStore.t('wishlist.title') }}
      </h1>
    </div>

    <div
      v-if="wishlistMovies.length === 0"
      class="text-center py-40 bg-zinc-900/50 rounded-[3rem] border border-dashed border-white/10"
    >
      <Film class="w-16 h-16 text-gray-800 mx-auto mb-6" />
      <p class="text-gray-500 text-xl mb-8">Your wishlist is empty.</p>
      <router-link
        to="/"
        class="px-8 py-4 bg-red-600 text-white rounded-2xl font-black hover:bg-red-700 transition-all"
      >
        EXPLORE MOVIES
      </router-link>
    </div>
    <div v-else class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
      <MovieCard v-for="movie in wishlistMovies" :key="movie.id" :movie="movie" />
    </div>
  </div>
</template>
