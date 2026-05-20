<script setup lang="ts">
import type { PublicMovie } from '@/features/client/types/movie.type'
import { useLanguageStore } from '@/stores/language'
import { Star } from 'lucide-vue-next'

defineProps<{
  movie: PublicMovie
  index: number
}>()

const languageStore = useLanguageStore()
</script>

<template>
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
        params: { id: movie.slug ?? movie.id },
      }"
      class="relative z-10 block"
    >
      <div
        class="movie-card relative aspect-[2/3] rounded-3xl overflow-hidden shadow-2xl shadow-black/50 border border-white/10 group-hover:border-red-600/50 transition-all duration-500"
      >
        <img
          :src="movie.poster_url"
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
              <Star class="w-3 h-3 text-yellow-500 fill-yellow-500" />
              <span class="text-xs font-bold text-white">{{ movie.rating_score ?? '-' }}</span>
            </div>
            <span
              class="px-2 py-1 bg-red-600 text-white text-[10px] font-black rounded uppercase tracking-widest"
            >
              TOP {{ index + 1 }}
            </span>
          </div>

          <h3 class="text-xl font-black text-white uppercase tracking-tight mb-4">
            {{ movie.title }}
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
          <Star class="w-3 h-3 text-yellow-500 fill-yellow-500" />
          <span class="text-xs font-bold text-white">{{ movie.rating_score ?? '-' }}</span>
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
</template>

<style scoped></style>
