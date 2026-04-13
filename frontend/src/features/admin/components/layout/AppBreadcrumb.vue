<script setup lang="ts">
import { NBreadcrumb, NBreadcrumbItem } from 'naive-ui'
import { useRouter } from 'vue-router'
import { useBreadcrumb } from '../../composables/useBreadcrumb';

const router = useRouter()
const { breadcrumbs, pageTitle } = useBreadcrumb()
</script>

<template>
  <div class="flex flex-col justify-center gap-0.5">
    <h2 class="text-base font-semibold leading-none">{{ pageTitle }}</h2>
    <n-breadcrumb v-if="breadcrumbs.length > 1">
      <n-breadcrumb-item
        v-for="(crumb, index) in breadcrumbs"
        :key="index"
        :clickable="!!crumb.name"
        @click="crumb.name && router.push({ name: crumb.name })"
      >
        {{ crumb.label }}
      </n-breadcrumb-item>
    </n-breadcrumb>
  </div>
</template>