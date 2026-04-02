import type { NavigationGuardNext, RouteLocationNormalized } from 'vue-router'
import { useAuthStore } from '@/stores/shared/auth.store'

export const authGuard = async (
  to: RouteLocationNormalized,
  _from: RouteLocationNormalized,
  next: NavigationGuardNext
) => {
  const authStore = useAuthStore()

  if (!authStore.isInitialized && localStorage.getItem('is_logged_in') === 'true') {
    await authStore.fetchMe() 
  }

  console.log('Auth Guard After Fetch:', to.meta.guestOnly, authStore.isLoggedIn)

  // Bây giờ isLoggedIn đã chính xác
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

  if (to.meta.requiresAdmin && !authStore.isAdmin) {
    next({ name: 'home' }) 
    return
  }

  next()
}