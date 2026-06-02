import { reactive, ref, watch } from 'vue'
import { useDebounceFn } from '@vueuse/core'
import { paymentService } from '@/features/admin/services/payment.service'
import type { PaymentProvider } from '@/features/admin/types/payment.type'
import type { Payment } from '@/features/admin/types/payment.type'
import type { PaymentStatus } from '@/features/admin/types/booking.type'

type DateRangeValue = [number, number] | null

function formatDateParam(value?: number) {
  if (!value) return undefined
  const date = new Date(value)
  const year = date.getFullYear()
  const month = String(date.getMonth() + 1).padStart(2, '0')
  const day = String(date.getDate()).padStart(2, '0')

  return `${year}-${month}-${day}`
}

export function usePayment() {
  const data = ref<Payment[]>([])
  const loading = ref(false)
  const filters = reactive({
    search: '',
    status: null as PaymentStatus | null,
    provider: null as PaymentProvider | null,
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
      fetchPayments()
    },
    onUpdatePageSize: (pageSize: number) => {
      pagination.pageSize = pageSize
      pagination.page = 1
      fetchPayments()
    },
  })

  async function fetchPayments() {
    loading.value = true
    try {
      const res = await paymentService.getAllPayments({
        page: pagination.page,
        limit: pagination.pageSize,
        q: filters.search || undefined,
        status: filters.status || undefined,
        provider: filters.provider || undefined,
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
    fetchPayments()
  }, 500)

  watch(() => filters.search, debouncedSearch)
  watch(() => [filters.status, filters.provider, filters.dateRange], () => {
    pagination.page = 1
    fetchPayments()
  })

  return { data, loading, filters, pagination, fetchPayments }
}
