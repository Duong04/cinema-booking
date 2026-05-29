import type { NavigationGuardNext, RouteLocationNormalized } from 'vue-router'
import { useAuthStore } from '@/features/shared/stores/auth.store'
import { STORAGE_KEYS } from '@/shared/constants/storage'

export const authGuard = async (
  to: RouteLocationNormalized,
  _from: RouteLocationNormalized,
  next: NavigationGuardNext
) => {
  const authStore = useAuthStore()

  if (!authStore.isInitialized && localStorage.getItem(STORAGE_KEYS.IS_LOGGED_IN) === 'true') {
    await authStore.fetchMe() 
  }

  if (to.meta.requiresAuth && !authStore.isLoggedIn) {
    next({ name: 'login', query: { redirect: to.fullPath } })
    return
  }

  if (to.meta.guestOnly && authStore.isLoggedIn) {
    next({ name: 'home' }) 
    return
  }

  next()
}

export const adminGuard = (
  to: RouteLocationNormalized,
  _from: RouteLocationNormalized,
  next: NavigationGuardNext
) => {
  const authStore = useAuthStore()

  if (to.meta.requiresAdmin && authStore.isClient) {
    next({ name: 'home' }) 
    return
  }

  next()
}