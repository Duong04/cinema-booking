import { ref, reactive, watch } from 'vue'
import { useDebounceFn } from '@vueuse/core'
import { permissionService } from '../services/permission.service'
import type { Permission } from '../types/permission.type'

export function usePermission() {
  const data = ref<Permission[]>([])
  const loading = ref(false)
  const filters = reactive({ search: '' })
  const pagination = reactive({
    page: 1,
    pageSize: 5,
    itemCount: 0,
    showSizePicker: true,
    pageSizes: [3, 5, 7],
    onChange: (page: number) => { pagination.page = page; fetchPermissions() },
    onUpdatePageSize: (pageSize: number) => { pagination.pageSize = pageSize; pagination.page = 1; fetchPermissions() },
  })

  async function fetchPermissions() {
    loading.value = true
    try {
      const res = await permissionService.getAllPermissions({
        page: pagination.page,
        limit: pagination.pageSize,
        q: filters.search || undefined,
      })
      data.value = res.data
      pagination.itemCount = res.meta.total
    } finally {
      loading.value = false
    }
  }

  async function deletePermission(id: string) {
      await permissionService.deletePermission(id)
      fetchPermissions()
    }

  const debouncedSearch = useDebounceFn(() => {
    pagination.page = 1
    fetchPermissions()
  }, 500)

  watch(() => filters.search, debouncedSearch)

  return { data, loading, filters, pagination, fetchPermissions, deletePermission }
}