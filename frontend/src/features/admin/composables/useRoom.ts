import { ref, reactive, watch } from 'vue'
import { useDebounceFn } from '@vueuse/core'
import { roomService } from '@/features/admin/services/room.service'
import type { Room } from '@/features/admin/types/room.type'

export function useRoom() {
  const data = ref<Room[]>([])
  const loading = ref(false)
  const filters = reactive({ search: '' })
  const pagination = reactive({
    page: 1,
    pageSize: 5,
    itemCount: 0,
    showSizePicker: true,
    pageSizes: [3, 5, 7],
    onChange: (page: number) => { pagination.page = page; fetchRooms() },
    onUpdatePageSize: (pageSize: number) => { pagination.pageSize = pageSize; pagination.page = 1; fetchRooms() },
  })

  async function fetchRooms() {
    loading.value = true
    try {
      const res = await roomService.getAllRooms({
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

  async function deleteRoom(id: string) {
      await roomService.deleteRoom(id)
      fetchRooms()
    }

  const debouncedSearch = useDebounceFn(() => {
    pagination.page = 1
    fetchRooms()
  }, 500)

  watch(() => filters.search, debouncedSearch)

  return { data, loading, filters, pagination, fetchRooms, deleteRoom }
}