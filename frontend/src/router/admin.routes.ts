import type { RouteRecordRaw } from 'vue-router'

export const adminRoutes: RouteRecordRaw[] = [
  {
    path: 'doashboard',
    name: 'dashboard',
    component: () => import('@/features/admin/views/dashboard/DashboardView.vue'),
    meta: {
      title: 'Cinema - Admin Dashboard',
    },
  },
  {
    path: 'roles',
    name: 'role',
    component: () => import('@/features/admin/views/roles/RoleView.vue'),
    meta: {
      title: 'Cinema - Admin Roles',
    },
  }
]