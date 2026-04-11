<script setup lang="ts">
import { useLanguageStore } from '@/stores/language';
import { Film, Home, ArrowLeft } from 'lucide-vue-next';

const languageStore = useLanguageStore();
</script>

<template>
  <div class="min-h-screen bg-zinc-950 flex items-center justify-center px-4 overflow-hidden relative">
    <!-- Background Elements -->
    <div class="absolute inset-0 z-0">
      <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] bg-red-600/10 rounded-full blur-[120px] animate-pulse" />
      <div class="absolute top-0 left-0 w-full h-full bg-[url('https://www.transparenttextures.com/patterns/stardust.png')] opacity-20" />
    </div>

    <div class="relative z-10 text-center max-w-2xl">
      <!-- Animated Icon -->
      <div class="relative inline-block mb-12">
        <div class="absolute inset-0 bg-red-600 blur-2xl opacity-20 animate-pulse" />
        <div class="relative bg-zinc-900 border border-white/10 p-8 rounded-[2.5rem] shadow-2xl transform hover:rotate-12 transition-transform duration-500">
          <Film class="w-24 h-24 text-red-600 animate-bounce" />
          <div class="absolute -top-2 -right-2 bg-red-600 text-white text-xs font-black px-3 py-1 rounded-full shadow-lg">
            404
          </div>
        </div>
      </div>

      <!-- Text Content -->
      <h1 class="text-6xl md:text-8xl font-black text-white mb-6 uppercase italic tracking-tighter drop-shadow-2xl">
        {{ languageStore.t('404.title') }}
      </h1>
      
      <p class="text-gray-400 text-lg md:text-xl font-medium mb-12 leading-relaxed max-w-lg mx-auto">
        {{ languageStore.t('404.subtitle') }}
      </p>

      <!-- Action Buttons -->
      <div class="flex flex-col sm:flex-row items-center justify-center gap-6">
        <router-link 
          to="/" 
          class="w-full sm:w-auto px-10 py-5 bg-red-600 text-white rounded-2xl font-black uppercase tracking-widest hover:bg-red-700 transition-all flex items-center justify-center gap-3 shadow-xl shadow-red-600/30 group"
        >
          <Home class="w-5 h-5 group-hover:-translate-y-1 transition-transform" />
          {{ languageStore.t('404.back_home') }}
        </router-link>
        
        <button 
          @click="$router.back()"
          class="w-full sm:w-auto px-10 py-5 bg-white/5 backdrop-blur-md border border-white/10 text-white rounded-2xl font-black uppercase tracking-widest hover:bg-white/10 transition-all flex items-center justify-center gap-3"
        >
          <ArrowLeft class="w-5 h-5" />
          {{ languageStore.language === 'en' ? 'Go Back' : 'Quay lại' }}
        </button>
      </div>

      <!-- Decorative Film Strip -->
      <div class="absolute -bottom-32 left-0 w-full opacity-5 pointer-events-none select-none">
        <div class="flex gap-4 animate-marquee">
          <div v-for="i in 10" :key="i" class="w-64 h-40 bg-white rounded-lg flex-shrink-0 border-4 border-dashed border-black" />
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
@keyframes marquee {
  0% { transform: translateX(0); }
  100% { transform: translateX(-50%); }
}

.animate-marquee {
  display: flex;
  width: 200%;
  animation: marquee 20s linear infinite;
}
</style>
