import { reactive, ref, watch } from 'vue'
import { useDebounceFn } from '@vueuse/core'
import { bookingService } from '@/features/admin/services/booking.service'
import type { Booking, BookingStatus } from '@/features/admin/types/booking.type'

type DateRangeValue = [number, number] | null

function formatDateParam(value?: number) {
  if (!value) return undefined
  const date = new Date(value)
  const year = date.getFullYear()
  const month = String(date.getMonth() + 1).padStart(2, '0')
  const day = String(date.getDate()).padStart(2, '0')

  return `${year}-${month}-${day}`
}

export function useBooking() {
  const data = ref<Booking[]>([])
  const loading = ref(false)
  const filters = reactive({
    search: '',
    status: null as BookingStatus | null,
    dateRange: null as DateRangeValue,
  })

  const pagination = reactive({
    page: 1,
    pageSize: 10,
    itemCount: 0,
    showSizePicker: true,
    pageSizes: [5, 10, 15],
    onChange: (page: number) => {
      pagination.page = page
      fetchBookings()
    },
    onUpdatePageSize: (pageSize: number) => {
      pagination.pageSize = pageSize
      pagination.page = 1
      fetchBookings()
    },
  })

  async function fetchBookings() {
    loading.value = true
    try {
      const res = await bookingService.getAllBookings({
        page: pagination.page,
        limit: pagination.pageSize,
        q: filters.search || undefined,
        status: filters.status || undefined,
        from_date: formatDateParam(filters.dateRange?.[0]),
        to_date: formatDateParam(filters.dateRange?.[1]),
      })

      data.value = res.data
      pagination.itemCount = res.meta.total
    } finally {
      loading.value = false
    }
  }

  const debouncedSearch = useDebounceFn(() => {
    pagination.page = 1
    fetchBookings()
  }, 500)

  watch(() => filters.search, debouncedSearch)
  watch(() => [filters.status, filters.dateRange], () => {
    pagination.page = 1
    fetchBookings()
  })

  return { data, loading, filters, pagination, fetchBookings }
}
