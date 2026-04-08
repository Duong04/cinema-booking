<script setup lang="ts">
import { h } from 'vue'
import {
  NAvatar,
  NDropdown,
  NIcon,
  NButton,
  NBadge,
  useMessage
} from 'naive-ui'
import {
  PersonCircleOutline,
  LogOutOutline,
  SettingsOutline,
  MoonOutline,
  SunnyOutline,
  NotificationsOutline
} from '@vicons/ionicons5'
import { useThemeStore } from '@/stores/theme'

const message = useMessage()
const themeStore = useThemeStore()

const options = [
  {
    label: 'Profile',
    key: 'profile',
    icon: () => h(NIcon, null, { default: () => h(PersonCircleOutline) })
  },
  {
    label: 'Settings',
    key: 'settings',
    icon: () => h(NIcon, null, { default: () => h(SettingsOutline) })
  },
  {
    type: 'divider',
    key: 'd1'
  },
  {
    label: 'Logout',
    key: 'logout',
    icon: () => h(NIcon, null, { default: () => h(LogOutOutline) })
  }
]

function handleSelect(key: string) {
  if (key === 'logout') {
    message.info('Logout...')
  }
}
</script>

<template>
  <div class="flex justify-between items-center w-full">
    <!-- Left -->
    <div class="font-semibold text-lg">
      Dashboard
    </div>

    <div class="flex items-center gap-4">
      
      <n-button circle quaternary @click="themeStore.toggleTheme">
        <n-icon size="20">
          <component :is="themeStore.isDark ? SunnyOutline : MoonOutline" />
        </n-icon>
      </n-button>

      <n-badge :value="3" dot>
        <n-button circle quaternary>
          <n-icon size="20">
            <NotificationsOutline />
          </n-icon>
        </n-button>
      </n-badge>

      <n-dropdown
        trigger="click"
        :options="options"
        @select="handleSelect"
      >
        <div class="flex items-center gap-2 cursor-pointer">
          <n-avatar round size="small">
            <template #default>
              T
            </template>
          </n-avatar>
          <span class="text-sm font-medium">Tinh</span>
        </div>
      </n-dropdown>

    </div>
  </div>
</template>

<style scoped>
</style>