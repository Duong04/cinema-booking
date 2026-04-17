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
  NCard,
  darkTheme,
} from 'naive-ui'
import AppBreadcrumb from '@/features/admin/components/layout/AppBreadcrumb.vue'

const themeStore = useThemeStore()
const theme = computed(() => (themeStore.isDark ? darkTheme : null))
</script>

<template>
  <n-config-provider :theme="theme">
    <n-layout has-sider style="height: 100vh">
      <n-layout-sider
        collapse-mode="width"
        :collapsed-width="64"
        :width="220"
        show-trigger
        :native-scrollbar="false"
        style="box-shadow: 4px 0 12px rgba(0, 0, 0, 0.06)"
      >
        <AppSidebar />
      </n-layout-sider>

      <n-layout
        style="background: var(--n-color)"
        :style="{ background: themeStore.isDark ? '#101014' : '#f0f2f5' }"
      >
        <n-layout-header style="padding: 12px 20px; background: transparent; border-bottom: none">
          <n-card size="small" style="height: 100%; border-radius: 12px" :bordered="true">
            <AppHeader />
          </n-card>
        </n-layout-header>

        <n-layout-content
          :native-scrollbar="false"
          style="padding: 0 20px 20px; background: transparent"
        >
          <n-card size="small" style="border-radius: 12px; margin-bottom: 16px" :bordered="true">
            <AppBreadcrumb />
          </n-card>

          <n-card style="border-radius: 12px; min-height: calc(100vh - 220px)" :bordered="true">
            <router-view v-slot="{ Component }">
              <transition name="fade" appear>
                <component :is="Component" />
              </transition>
            </router-view>
          </n-card>
        </n-layout-content>

        <n-layout-footer style="padding: 0 20px 16px; background: transparent; border-top: none">
          <AppFooter />
        </n-layout-footer>
      </n-layout>
    </n-layout>
  </n-config-provider>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition:
    opacity 0.2s ease,
    transform 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(6px);
}
</style>
