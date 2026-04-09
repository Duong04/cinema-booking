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
    path: 'cinema-chains',
    name: 'cinema-chainns',
    component: () => import('@/features/admin/views/city/CityView.vue'),
    meta: {
      title: 'Cinema - Admin Cinema Chains',
    },
  },
  {
    path: 'cities',
    name: 'cities',
    component: () => import('@/features/admin/views/city/CityView.vue'),
    meta: {
      title: 'Cinema - Admin Cities',
    },
  },
  {
    path: 'roles',
    name: 'roles',
    component: () => import('@/features/admin/views/role/RoleView.vue'),
    meta: {
      title: 'Cinema - Admin Roles',
    },
  },
  {
    path: 'permissions',
    name: 'permissions',
    component: () => import('@/features/admin/views/permission/PermissionView.vue'),
    meta: {
      title: 'Cinema - Admin Permissions',
    },
  },
  {
    path: 'ations',
    name: 'actions',
    component: () => import('@/features/admin/views/action/ActionView.vue'),
    meta: {
      title: 'Cinema - Admin Actions',
    },
  }
]