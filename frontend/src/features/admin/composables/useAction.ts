import { ref, reactive, watch } from 'vue'
import { useDebounceFn } from '@vueuse/core'
import { actionService } from '../services/action.service'
import type { Action } from '../types/action.type'

export function useAction() {
  const data = ref<Action[]>([])
  const loading = ref(false)
  const filters = reactive({ search: '' })
  const pagination = reactive({
    page: 1,
    pageSize: 5,
    itemCount: 0,
    showSizePicker: true,
    pageSizes: [3, 5, 7],
    onChange: (page: number) => { pagination.page = page; fetchActions() },
    onUpdatePageSize: (pageSize: number) => { pagination.pageSize = pageSize; pagination.page = 1; fetchActions() },
  })

  async function fetchActions() {
    loading.value = true
    try {
      const res = await actionService.getAllActions({
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

  async function deleteAction(id: string) {
      await actionService.deleteAction(id)
      fetchActions()
    }

  const debouncedSearch = useDebounceFn(() => {
    pagination.page = 1
    fetchActions()
  }, 500)

  watch(() => filters.search, debouncedSearch)

  return { data, loading, filters, pagination, fetchActions, deleteAction }
}