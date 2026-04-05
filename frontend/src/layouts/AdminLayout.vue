<script setup lang="ts">
import { computed } from 'vue'
import AppHeader from '@/features/admin/components/layout/AppHeader.vue'
import AppSidebar from '@/features/admin/components/layout/AppSidebar.vue'
import AppFooter from '@/features/admin/components/layout/AppFooter.vue'
import { useThemeStore } from '@/stores/theme'
import {
  NConfigProvider,
  NLayout,
  NLayoutSider,
  NLayoutHeader,
  NLayoutContent,
  NLayoutFooter,
  darkTheme,
} from 'naive-ui'

const themeStore = useThemeStore()

const theme = computed(() => (themeStore.isDark ? darkTheme : null))
</script>

<template>
  <n-config-provider :theme="theme">
    <n-layout has-sider class="h-screen">
      <n-layout-sider
        bordered
        collapse-mode="width"
        :collapsed-width="64"
        :width="220"
        show-trigger
      >
        <AppSidebar />
      </n-layout-sider>

      <n-layout>
        <n-layout-header bordered class="h-14 flex items-center px-4">
          <AppHeader />
        </n-layout-header>

        <n-layout-content class="p-4 bg-gray-100">
          <router-view v-slot="{ Component }">
            <transition name="fade" mode="out-in">
              <component :is="Component" />
            </transition>
          </router-view>
        </n-layout-content>

        <n-layout-footer bordered class="text-center p-2 text-sm text-gray-500">
          <AppFooter />
        </n-layout-footer>
      </n-layout>
    </n-layout>
  </n-config-provider>
</template>
