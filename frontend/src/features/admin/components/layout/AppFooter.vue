<script setup lang="ts">
import { NText } from 'naive-ui'
const currentYear = new Date().getFullYear()
</script>

<template>
  <div
    style="
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 8px;
    "
  >
    <n-text depth="3" style="font-size: 12px">
      © {{ currentYear }} SugarDev. All rights reserved.
    </n-text>

    <div style="display: flex; align-items: center; gap: 8px">
      <n-text depth="3" style="font-size: 12px; cursor: pointer">Privacy Policy</n-text>
      <n-text depth="3" style="font-size: 12px">·</n-text>
      <n-text depth="3" style="font-size: 12px; cursor: pointer">Terms</n-text>
      <n-text depth="3" style="font-size: 12px">·</n-text>
      <n-text depth="3" style="font-size: 12px; cursor: pointer">Support</n-text>
    </div>
  </div>
</template>
