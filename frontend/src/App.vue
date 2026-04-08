<script setup lang="ts">
import { RouterView } from 'vue-router'
import { onMounted } from 'vue'
import { NMessageProvider, NDialogProvider } from 'naive-ui'
import { useAuthStore } from './features/shared/auth/stores/auth.store'
import { STORAGE_KEYS } from './shared/constants/storage'

const authStore = useAuthStore()

onMounted(async () => {
  if (localStorage.getItem(STORAGE_KEYS.IS_LOGGED_IN) === 'true') {
    await authStore.fetchMe()
  }
})
</script>

<template>
    <n-message-provider :max="3" closable>
      <n-dialog-provider>
        <router-view />
      </n-dialog-provider>
    </n-message-provider>
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
