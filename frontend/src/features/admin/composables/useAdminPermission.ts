import { computed } from 'vue'
import { storeToRefs } from 'pinia'
import { useAuthStore } from '@/features/shared/stores/auth.store'
import { EnumUserRole } from '@/shared/types/auth'
import { ADMIN_ACTIONS } from '../configs/access-control.config'
import type { AdminActionKey, AdminPermissionKey } from '../configs/access-control.config'

export function useAdminPermission() {
  const authStore = useAuthStore()
  const { user } = storeToRefs(authStore)

  const isSuperAdmin = computed(() => user.value?.role?.name === EnumUserRole.ADMIN)

  function hasPermission(permissionKey: AdminPermissionKey | string) {
    if (isSuperAdmin.value) {
      return true
    }

    return user.value?.role?.permissions?.some((permission) => permission.key === permissionKey) ?? false
  }

  function can(permissionKey: AdminPermissionKey | string, actionKey: AdminActionKey = ADMIN_ACTIONS.VIEW) {
    if (isSuperAdmin.value) {
      return true
    }

    const permission = user.value?.role?.permissions?.find((item) => item.key === permissionKey)

    return permission?.actions?.some((action) => action.key === actionKey) ?? false
  }

  return {
    can,
    hasPermission,
    isSuperAdmin,
  }
}
