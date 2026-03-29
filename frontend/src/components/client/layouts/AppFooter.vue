<script setup lang="ts">
import { Film } from 'lucide-vue-next'
</script>

<template>
  <footer class="border-t border-white/10 pt-20 pb-10 bg-zinc-950">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-16">
        <div class="col-span-1 md:col-span-2">
          <router-link to="/" class="flex items-center gap-2 mb-6">
            <div class="w-10 h-10 bg-red-600 rounded-xl flex items-center justify-center">
              <Film class="text-white w-6 h-6" />
            </div>
            <span class="text-2xl font-black text-white tracking-tighter">CINEMAX</span>
          </router-link>
          <p class="text-gray-500 max-w-sm leading-relaxed">
            The ultimate destination for movie lovers. Experience the latest blockbusters with
            state-of-the-art technology.
          </p>
        </div>
        <div>
          <h4 class="text-white font-bold mb-6 uppercase tracking-widest text-sm">Quick Links</h4>
          <ul class="space-y-4 text-gray-500 text-sm">
            <li>
              <router-link to="/" class="hover:text-red-500 transition-colors">Home</router-link>
            </li>
            <li>
              <router-link to="/movies" class="hover:text-red-500 transition-colors"
                >Movies</router-link
              >
            </li>
            <li>
              <router-link to="/cinemas" class="hover:text-red-500 transition-colors"
                >Cinemas</router-link
              >
            </li>
            <li>
              <router-link to="/schedule" class="hover:text-red-500 transition-colors"
                >Schedule</router-link
              >
            </li>
          </ul>
        </div>
      </div>
      <div
        class="border-t border-white/5 pt-8 flex flex-col md:flex-row justify-between items-center gap-4"
      >
        <p class="text-gray-600 text-xs">
          © {{ new Date().getFullYear }} CINEMAX. All rights reserved.
        </p>
      </div>
    </div>
  </footer>
</template>

<style scoped></style>
