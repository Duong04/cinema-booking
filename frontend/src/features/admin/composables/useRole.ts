import { ref, reactive, watch } from 'vue'
import { useDebounceFn } from '@vueuse/core'
import { roleService } from '@/features/admin/services/role.service'
import type { Role } from '@/features/admin/types/role.type'

export function useRole() {
  const data = ref<Role[]>([])
  const loading = ref(false)
  const filters = reactive({ search: '' })
  const pagination = reactive({
    page: 1,
    pageSize: 10,
    itemCount: 0,
    showSizePicker: true,
    pageSizes: [3, 5, 7],
    onChange: (page: number) => { pagination.page = page; fetchRoles() },
    onUpdatePageSize: (pageSize: number) => { pagination.pageSize = pageSize; pagination.page = 1; fetchRoles() },
  })

  async function fetchRoles() {
    loading.value = true
    try {
      const res = await roleService.getAllRoles({
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

  const debouncedSearch = useDebounceFn(() => {
    pagination.page = 1
    fetchRoles()
  }, 500)

  watch(() => filters.search, debouncedSearch)

  async function deleteRole(id: string) {
    await roleService.deleteRole(id)
    fetchRoles()
  }

  return { data, loading, filters, pagination, fetchRoles, deleteRole }
}