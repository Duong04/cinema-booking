<script setup lang="ts">
import { h, ref } from 'vue'
import { NAvatar, NDropdown, NIcon, NButton, NBadge, NText, useMessage } from 'naive-ui'
import {
  PersonCircleOutline,
  LogOutOutline,
  SettingsOutline,
  MoonOutline,
  SunnyOutline,
  NotificationsOutline,
} from '@vicons/ionicons5'
import { useThemeStore } from '@/stores/theme'
import { useAuthStore } from '@/features/shared/stores/auth.store'
import { useRouter } from 'vue-router'

const router = useRouter()
const authStore = useAuthStore()
const message = useMessage()
const themeStore = useThemeStore()
const isLoggingOut = ref(false)

const options = [
  {
    label: 'Thông tin cá nhân',
    key: 'profile',
    icon: () => h(NIcon, null, { default: () => h(PersonCircleOutline) }),
  },
  {
    label: 'Cài đặt',
    key: 'settings',
    icon: () => h(NIcon, null, { default: () => h(SettingsOutline) }),
  },
  { type: 'divider', key: 'd1' },
  {
    label: 'Đăng xuất',
    key: 'logout',
    icon: () => h(NIcon, null, { default: () => h(LogOutOutline) }),
  },
]

async function handleLogout() {
  if (isLoggingOut.value) return
  
  isLoggingOut.value = true
  try {
    const success = await authStore.logout()
    if (success) {
      message.success('Đăng xuất thành công')
      router.push('/login')
    }
  } finally {
    isLoggingOut.value = false
  }
}

async function handleSelect(key: string) {
  switch (key) {
    case 'profile':
      router.push({ name: 'profile' })
      break
    case 'admin':
      router.push({ name: 'settings' })
      break
    case 'logout':
      await handleLogout()
      break
  }
}
</script>

<template>
  <div
    style="
      display: flex;
      justify-content: space-between;
      align-items: center;
      width: 100%;
      height: 100%;
    "
  >
    <n-text style="font-weight: 600; font-size: 15px">Dashboard</n-text>

    <div style="display: flex; align-items: center; gap: 8px">
      <n-button circle quaternary @click="themeStore.toggleTheme">
        <template #icon>
          <n-icon size="18">
            <component :is="themeStore.isDark ? SunnyOutline : MoonOutline" />
          </n-icon>
        </template>
      </n-button>

      <n-badge :value="3" dot>
        <n-button circle quaternary>
          <template #icon>
            <n-icon size="18"><NotificationsOutline /></n-icon>
          </template>
        </n-button>
      </n-badge>

      <n-dropdown trigger="click" :options="options" @select="handleSelect">
        <div
          style="
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            padding: 4px 8px;
            border-radius: 8px;
          "
        >
          <n-avatar round size="small" style="background: #6366f1; color: white; font-size: 13px">
            T
          </n-avatar>
          <n-text style="font-size: 13px; font-weight: 500">Tinh</n-text>
        </div>
      </n-dropdown>
    </div>
  </div>
</template>
