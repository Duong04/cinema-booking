<script setup lang="ts">
import { NMenu } from 'naive-ui'
import { menuOptions } from '../../configs/menu.config'

</script>

<template>
  <div class="h-full flex flex-col">
    <div class="h-14 flex items-center justify-center font-bold">
      ADMIN
    </div>

    <n-menu :options="menuOptions" />
  </div>
</template>

<style scoped></style>
