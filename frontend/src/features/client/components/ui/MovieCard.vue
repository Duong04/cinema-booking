<script setup lang="ts">
import type { Movie } from '@/data/mockData';
import { useLanguageStore } from '@/stores/language';
import { Star, Clock, Play } from 'lucide-vue-next';

defineProps<{
  movie: Movie;
}>();

const languageStore = useLanguageStore();
</script>

<template>
  <div class="group relative bg-zinc-900 rounded-2xl overflow-hidden border border-white/5 shadow-2xl transition-all hover:-translate-y-2">
    <router-link :to="{
                  name: 'movie-detail',
                  params: { id: movie.id }
                }">
      <div class="aspect-[2/3] relative overflow-hidden">
        <img
          :src="movie.poster"
          :alt="movie.title"
          class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
          referrerpolicy="no-referrer"
        />
        <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-4">
           <button class="w-full py-2.5 bg-red-600 text-white rounded-xl font-semibold flex items-center justify-center gap-2 transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300">
             <Play class="w-4 h-4 fill-current" />
             {{ languageStore.t('movie.book_now') }}
           </button>
        </div>
        <div class="absolute top-2 right-2 px-2 py-1 bg-black/60 backdrop-blur-md rounded-lg flex items-center gap-1 border border-white/10">
          <Star class="w-3 h-3 text-yellow-500 fill-yellow-500" />
          <span class="text-xs font-bold text-white">{{ movie.rating }}</span>
        </div>
        <div class="absolute top-2 left-2 px-2 py-1 bg-red-600 rounded-lg">
          <span class="text-[10px] font-black text-white">{{ movie.ageRating }}</span>
        </div>
      </div>
      <div class="p-4">
        <h3 class="text-white font-bold truncate group-hover:text-red-500 transition-colors">
          {{ languageStore.language === 'en' ? movie.title : movie.titleVi }}
        </h3>
        <div class="flex items-center gap-3 mt-1 text-zinc-500 text-xs">
          <span class="flex items-center gap-1">
            <Clock class="w-3 h-3" />
            {{ movie.duration }} {{ languageStore.t('movie.duration') }}
          </span>
          <span>•</span>
          <span class="truncate">
            {{ languageStore.language === 'en' ? movie.genres[0] : movie.genresVi[0] }}
          </span>
        </div>
      </div>
    </router-link>
  </div>
</template>
