<script setup lang="ts">
import { useLanguageStore } from '@/stores/language'
import { Bell, Ticket, ChevronRight } from 'lucide-vue-next'

const languageStore = useLanguageStore()

const notifications = [
  {
    id: 1,
    title: 'Booking Confirmed!',
    message: 'Your tickets are ready. Enjoy the show!',
    time: '2 hours ago',
    icon: Ticket,
    color: 'text-emerald-500',
    bg: 'bg-emerald-500/10',
  },
]
</script>

<template>
  <div class="pt-24 pb-20 max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center gap-4 mb-12">
      <div class="w-12 h-12 bg-red-600/20 rounded-2xl flex items-center justify-center">
        <Bell class="w-6 h-6 text-red-500" />
      </div>
      <h1 class="text-4xl font-black text-white uppercase italic tracking-tight">
        {{ languageStore.t('notification.title') }}
      </h1>
    </div>

    <div class="space-y-4">
      <div
        v-for="notif in notifications"
        :key="notif.id"
        class="group p-6 bg-zinc-900 rounded-3xl border border-white/5 flex items-start gap-6 hover:bg-zinc-800 transition-all cursor-pointer"
      >
        <div
          :class="[
            'w-12 h-12 rounded-2xl flex items-center justify-center flex-shrink-0',
            notif.bg,
          ]"
        >
          <component :is="notif.icon" :class="['w-6 h-6', notif.color]" />
        </div>
        <div class="flex-1">
          <div class="flex justify-between items-start mb-1">
            <h3 class="text-white font-bold">{{ notif.title }}</h3>
            <span class="text-gray-500 text-xs">{{ notif.time }}</span>
          </div>
          <p class="text-gray-400 text-sm leading-relaxed">{{ notif.message }}</p>
        </div>
        <ChevronRight
          class="w-5 h-5 text-gray-700 group-hover:text-white transition-colors self-center"
        />
      </div>
    </div>
  </div>
</template>
