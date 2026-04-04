<script setup lang="ts">
import { RouterView } from 'vue-router'
import { onMounted } from 'vue'
import { NConfigProvider, NMessageProvider } from 'naive-ui'
import { useAuthStore } from './features/shared/auth/stores/auth.store'

const authStore = useAuthStore()

onMounted(async () => {
  if (localStorage.getItem('is_logged_in') === 'true') {
    await authStore.fetchMe()
  }
})
</script>

<template>
  <n-config-provider>
    <n-message-provider :max="3" closable>
      <router-view />
    </n-message-provider>
  </n-config-provider>
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
