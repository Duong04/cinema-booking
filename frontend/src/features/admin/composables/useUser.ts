import { ref, reactive, watch } from 'vue'
import { useDebounceFn } from '@vueuse/core'
import { userService } from '@/features/admin/services/user.service'
import type { User } from '../types/user.type'

export function useUser() {
  const data = ref<User[]>([])
  const loading = ref(false)
  const filters = reactive({
    search: '',
    role_id: null as string | null,
    ignore_role_id: null as string | null,
    is_active: null as 1 | 0 | null,
  })
  const pagination = reactive({
    page: 1,
    pageSize: 10,
    itemCount: 0,
    showSizePicker: true,
    pageSizes: [3, 5, 7],
    onChange: (page: number) => {
      pagination.page = page
      fetchUsers()
    },
    onUpdatePageSize: (pageSize: number) => {
      pagination.pageSize = pageSize
      pagination.page = 1
      fetchUsers()
    },
  })

  async function fetchUsers() {
    loading.value = true
    try {
      const res = await userService.getAllUsers({
        page: pagination.page,
        limit: pagination.pageSize,
        q: filters.search || undefined,
        role_id: filters.role_id || undefined,
        ignore_role_id: filters.ignore_role_id || undefined,
        is_active: filters.is_active ?? undefined,
      })
      data.value = res.data
      pagination.itemCount = res.meta.total
    } finally {
      loading.value = false
    }
  }

  const debouncedSearch = useDebounceFn(() => {
    pagination.page = 1
    fetchUsers()
  }, 500)

  watch(() => filters.search, debouncedSearch)

  watch(
    () => [filters.role_id, filters.ignore_role_id, filters.is_active],
    () => {
      pagination.page = 1
      fetchUsers()
    },
  )

  return { data, loading, filters, pagination, fetchUsers }
}
