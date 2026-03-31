<script setup lang="ts">
import { RouterView } from 'vue-router'
import { onMounted } from 'vue'

import { useAuthStore } from './stores/shared/auth.store'

const authStore = useAuthStore()

onMounted(async () => {
  if (localStorage.getItem('is_logged_in') === 'true') {
    await authStore.fetchMe()
  }
})
</script>

<template>
  <router-view />
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
