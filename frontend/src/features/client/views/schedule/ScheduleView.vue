<script setup lang="ts">
import { SHOWTIMES, MOVIES, CINEMAS } from '@/data/mockData';
import { useLanguageStore } from '@/stores/language';
import { Calendar, Clock, MapPin } from 'lucide-vue-next';

const languageStore = useLanguageStore();

const getMovie = (id: string) => MOVIES.find(m => m.id === id);
const getCinema = (id: string) => CINEMAS.find(c => c.id === id);
</script>

<template>
  <div class="pt-24 pb-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between mb-12">
      <h1 class="text-4xl font-black text-white uppercase italic tracking-tight">
        {{ languageStore.t('nav.schedule') }}
      </h1>
    </div>

    <div class="space-y-8">
      <div v-for="showtime in SHOWTIMES" :key="showtime.id" class="p-6 bg-zinc-900 rounded-3xl border border-white/5 flex flex-col md:flex-row items-center gap-8 hover:bg-zinc-800 transition-all">
        <img :src="getMovie(showtime.movieId)?.poster" class="w-24 rounded-xl" referrerpolicy="no-referrer" />
        
        <div class="flex-1">
          <h3 class="text-2xl font-black text-white mb-2">
            {{ languageStore.language === 'en' ? getMovie(showtime.movieId)?.title : getMovie(showtime.movieId)?.titleVi }}
          </h3>
          <div class="flex flex-wrap gap-6 text-gray-400 text-sm">
            <div class="flex items-center gap-2">
              <MapPin class="w-4 h-4 text-red-500" />
              {{ getCinema(showtime.cinemaId)?.name }}
            </div>
            <div class="flex items-center gap-2">
              <Calendar class="w-4 h-4 text-red-500" />
              {{ showtime.date }}
            </div>
            <div class="flex items-center gap-2">
              <Clock class="w-4 h-4 text-red-500" />
              {{ showtime.time }}
            </div>
            <div class="px-2 py-0.5 bg-white/5 rounded text-[10px] font-bold uppercase tracking-widest border border-white/10">
              {{ showtime.format }}
            </div>
          </div>
        </div>

        <div class="text-center md:text-right">
          <p class="text-red-500 font-black text-2xl mb-4">{{ showtime.price.toLocaleString() }} VND</p>
          <router-link :to="`/booking/seats/${showtime.id}`" class="px-8 py-3 bg-red-600 text-white rounded-xl font-bold hover:bg-red-700 transition-all block">
            BOOK NOW
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>
