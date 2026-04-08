<script setup lang="ts">
import { useLanguageStore } from '@/stores/language';
import { Mail, ArrowLeft, Film, CheckCircle2 } from 'lucide-vue-next';

const languageStore = useLanguageStore();
</script>

<template>
  <div class="min-h-screen py-32 bg-zinc-950 flex items-center justify-center px-4 overflow-hidden relative font-sans">
    <div class="absolute inset-0 z-0">
      <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-red-600/10 rounded-full blur-[120px] animate-pulse" />
      <div class="absolute top-0 left-0 w-full h-full bg-[url('https://www.transparenttextures.com/patterns/stardust.png')] opacity-20" />
    </div>

    <div class="relative z-10 text-center max-w-2xl w-full">
      <div class="relative inline-block mb-12">
        <div class="w-32 h-32 bg-zinc-900 border border-white/5 rounded-[2.5rem] flex items-center justify-center shadow-2xl relative overflow-hidden group">
          <div class="absolute inset-0 bg-gradient-to-br from-green-600/20 to-transparent opacity-100 transition-opacity duration-500" />
          <CheckCircle2 class="w-16 h-16 text-green-500 relative z-10 animate-bounce" />
        </div>
        <!-- Decorative Elements -->
        <div class="absolute -top-4 -right-4 w-12 h-12 bg-red-600 rounded-2xl flex items-center justify-center shadow-lg shadow-red-600/20 animate-spin-slow">
          <Film class="text-white w-6 h-6" />
        </div>
      </div>

      <h1 class="text-4xl font-black text-white mb-6 uppercase italic tracking-tight leading-tight drop-shadow-2xl">
        {{ languageStore.t('register.success.title') }}
      </h1>
      
      <div class="space-y-4 mb-12">
        <p class="text-zinc-300 text-xl font-bold italic">
          {{ languageStore.t('register.success.subtitle') }}
        </p>
        <p class="text-zinc-500 text-lg font-medium leading-relaxed max-w-md mx-auto">
          {{ languageStore.t('register.success.instruction') }}
        </p>
      </div>

      <!-- Action Buttons -->
      <div class="flex flex-col sm:flex-row items-center justify-center gap-6">
        <router-link 
          :to="{ name: 'login' }" 
          class="w-full sm:w-auto px-12 py-5 bg-red-600 text-white rounded-2xl font-black uppercase text-xs tracking-[0.2em] hover:bg-red-700 transition-all shadow-xl shadow-red-600/30 flex items-center justify-center gap-3 group"
        >
          <ArrowLeft class="w-4 h-4 group-hover:-translate-x-1 transition-transform" />
          {{ languageStore.t('register.success.back_to_login') }}
        </router-link>
        
        <a 
          href="https://mail.google.com" 
          target="_blank"
          class="w-full sm:w-auto px-12 py-5 bg-white/5 backdrop-blur-xl border border-white/10 text-white rounded-2xl font-black uppercase text-xs tracking-[0.2em] hover:bg-white/10 transition-all flex items-center justify-center gap-3 group"
        >
          <Mail class="w-4 h-4 group-hover:-translate-y-1 transition-transform" />
          {{ languageStore.language === 'en' ? 'Open Mail' : 'Mở Email' }}
        </a>
      </div>

      <!-- Branding -->
      <div class="mt-16 flex items-center justify-center gap-2 opacity-40">
        <Film class="w-4 h-4 text-red-600" />
        <span class="text-sm font-black tracking-tighter text-white">CINEMAX</span>
      </div>
    </div>
  </div>
</template>

<style scoped>
@keyframes spin-slow {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

.animate-spin-slow {
  animation: spin-slow 12s linear infinite;
}

h1 {
  font-family: 'Inter', sans-serif;
}
</style>
