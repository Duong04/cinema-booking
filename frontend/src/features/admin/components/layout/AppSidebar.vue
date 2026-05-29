<script setup lang="ts">
import { NMenu, NText } from 'naive-ui'
import { computed } from 'vue'
import { menuOptions, handleMenuSelect } from '../../configs/menu.config'
import type { AdminMenuOption } from '../../configs/menu.config'
import { useAdminPermission } from '../../composables/useAdminPermission'

const { hasPermission } = useAdminPermission()

const canShowMenuItem = (option: AdminMenuOption) => {
  if (!option.permissionKey) {
    return true
  }

  return hasPermission(option.permissionKey)
}

const filterMenuOptionsByPermission = (options: AdminMenuOption[]): AdminMenuOption[] => {
  return options.reduce<AdminMenuOption[]>((visibleOptions, option) => {
    const children = option.children ? filterMenuOptionsByPermission(option.children) : undefined
    const isVisible = canShowMenuItem(option) && (!option.children || children?.length)

    if (isVisible) {
      visibleOptions.push({
        ...option,
        ...(children ? { children } : {}),
      })
    }

    return visibleOptions
  }, [])
}

const visibleMenuOptions = computed(() => filterMenuOptionsByPermission(menuOptions))
</script>

<template>
  <div style="height: 100%; display: flex; flex-direction: column">
    <div
      style="
        height: 64px;
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 0 20px;
        border-bottom: 1px solid var(--n-border-color);
      "
    >
      <div
        style="
          width: 32px;
          height: 32px;
          border-radius: 8px;
          background: #6366f1;
          color: white;
          font-weight: 700;
          font-size: 15px;
          display: flex;
          align-items: center;
          justify-content: center;
          flex-shrink: 0;
        "
      >
        A
      </div>
      <n-text style="font-weight: 600; font-size: 15px; letter-spacing: 0.02em"> Admin </n-text>
    </div>

    <div style="flex: 1; padding: 8px">
      <n-menu :options="visibleMenuOptions" @update:value="handleMenuSelect" />
    </div>
  </div>
</template>
