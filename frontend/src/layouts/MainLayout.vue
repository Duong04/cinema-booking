<script setup lang="ts">
import AppHeader from '@/components/client/layouts/AppHeader.vue'
import AppFooter from '@/components/client/layouts/AppFooter.vue'
</script>

<template>
  <div class="min-h-screen bg-black text-white selection:bg-red-600 selection:text-white font-sans">
    <AppHeader />
    <main>
      <router-view v-slot="{ Component }">
        <transition name="fade" mode="out-in">
          <component :is="Component" />
        </transition>
      </router-view>
    </main>
    <AppFooter />
  </div>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>