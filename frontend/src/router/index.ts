// src/router/index.ts
import { createRouter, createWebHistory } from 'vue-router'
import { clientRoutes } from './client.routes'
// import { adminRoutes } from './admin.routes'
// import { authGuard, adminGuard } from './guards'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition
    }
    return { top: 0 }
  },
  routes: [
    // {
    //   path: '/auth',
    //   component: () => import('@/layouts/shared/AuthLayout.vue'),
    //   children: [
    //     {
    //       path: 'login',
    //       name: 'login',
    //       component: () => import('@/views/shared/Login.vue'),
    //     },
    //     {
    //       path: 'forgot-password',
    //       name: 'forgot-password',
    //       component: () => import('@/views/shared/ForgotPassword.vue'),
    //     },
    //   ],
    // },
    {
      path: '/',
      component: () => import('@/layouts/MainLayout.vue'),
      children: clientRoutes,
    },
    // {
    //   path: '/admin',
    //   component: () => import('@/layouts/admin/AdminLayout.vue'),
    //   meta: { requiresAdmin: true },
    //   children: adminRoutes,
    // },
    {
      path: '/:pathMatch(.*)*',
      name: 'not-found',
      component: () => import('@/views/shared/NotfoundView.vue')
    },
  ],
})

// router.beforeEach(authGuard)
// router.beforeEach(adminGuard)

router.afterEach((to) => {
  document.title = (to.meta.title as string) || 'CINEMAX'
})

export default router