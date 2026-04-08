import { ref, reactive, watch } from 'vue'
import { useDebounceFn } from '@vueuse/core'
import { useMessage, useDialog } from 'naive-ui'
import { roleService } from '../services/role.service'
import type { Role } from '../types/role.type'

export function useRole() {
  const message = useMessage()
  const dialog = useDialog()

  const data = ref<Role[]>([])
  const loading = ref(false)
  const filters = reactive({ search: '' })
  const pagination = reactive({
    page: 1,
    pageSize: 5,
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

  function handleDelete(row: Role) {
    dialog.warning({
      title: 'Confirm Delete',
      content: `Are you sure you want to delete "${row.name}"?`,
      positiveText: 'Delete',
      negativeText: 'Cancel',
      onPositiveClick: async () => {
        try {
          await roleService.deleteRole(row.id)
          message.success('Role deleted successfully')
          fetchRoles()
        } catch {
          message.error('Failed to delete role')
        }
      },
    })
  }

  const debouncedSearch = useDebounceFn(() => {
    pagination.page = 1
    fetchRoles()
  }, 500)

  watch(() => filters.search, debouncedSearch)

  return { data, loading, filters, pagination, fetchRoles, handleDelete }
}