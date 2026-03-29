// src/router/admin.routes.ts
import type { RouteRecordRaw } from 'vue-router'

export const adminRoutes: RouteRecordRaw[] = [
  {
    path: '',
    name: 'admin-dashboard',
    component: <h1> Hello world </h1>,
  }
]