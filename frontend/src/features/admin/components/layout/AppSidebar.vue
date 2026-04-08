<script setup lang="ts">
import { NMenu } from 'naive-ui'
import { menuOptions, handleMenuSelect } from '../../configs/menu.config';

</script>

<template>
  <div class="h-full flex flex-col">
    <div class="h-14 flex items-center justify-center font-bold">
      ADMIN
    </div>

    <n-menu :options="menuOptions" @update:value="handleMenuSelect" />
  </div>
</template>

<style scoped></style>
