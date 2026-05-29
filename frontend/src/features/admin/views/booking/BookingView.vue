<script setup lang="ts">
import type { DataTableColumns, DataTableRowKey } from 'naive-ui'
import { NButton, NTag } from 'naive-ui'
import { computed, h, onMounted, ref } from 'vue'
import {
  CalendarOutline,
  CardOutline,
  ChevronDownOutline,
  ChevronUpOutline,
  EyeOutline,
  FilmOutline,
  SearchOutline,
  TicketOutline,
  TimeOutline,
  TrashOutline,
} from '@vicons/ionicons5'
import { useBooking } from '@/features/admin/composables/useBooking'
import type { Booking, BookingStatus, PaymentStatus } from '@/features/admin/types/booking.type'
import { formatDateTime } from '@/shared/utils/formatDate'

const { data, loading, filters, pagination, fetchBookings } = useBooking()
const expandedRowKeys = ref<DataTableRowKey[]>([])

const statusFilters: Array<{ label: string; value: BookingStatus | null; className: string }> = [
  { label: 'Tất cả', value: null, className: 'filter-all' },
  { label: 'Đã thanh toán', value: 'confirmed', className: 'filter-paid' },
  { label: 'Chờ thanh toán', value: 'pending', className: 'filter-pending' },
  { label: 'Đã hủy', value: 'cancelled', className: 'filter-cancelled' },
]

const visibleSummary = computed(() => {
  const total = pagination.itemCount || data.value.length
  const visible = data.value.length

  return `${visible}/${total} đơn đặt vé`
})

function money(value?: number | string | null) {
  return Number(value ?? 0).toLocaleString('vi-VN') + ' đ'
}

function bookingStatusMeta(status: BookingStatus) {
  if (status === 'confirmed') return { label: 'Confirmed', type: 'success' as const, className: 'status-confirmed' }
  if (status === 'pending') return { label: 'Pending', type: 'warning' as const, className: 'status-pending' }
  if (status === 'refunded') return { label: 'Refunded', type: 'info' as const, className: 'status-refunded' }
  if (status === 'expired') return { label: 'Expired', type: 'default' as const, className: 'status-muted' }
  return { label: 'Cancelled', type: 'error' as const, className: 'status-cancelled' }
}

function paymentStatusMeta(status?: PaymentStatus) {
  if (status === 'paid') return { label: 'Đã thanh toán', className: 'filter-paid' }
  if (status === 'pending') return { label: 'Chờ thanh toán', className: 'filter-pending' }
  if (status === 'refunded') return { label: 'Đã hoàn tiền', className: 'filter-paid' }
  if (status === 'failed') return { label: 'Thất bại', className: 'filter-cancelled' }
  return { label: 'Chưa tạo thanh toán', className: 'filter-cancelled' }
}

function providerLabel(provider?: string | null) {
  if (provider === 'vnpay') return 'VNPay'
  if (provider === 'momo') return 'MoMo'
  if (provider === 'zalopay') return 'ZaloPay'
  if (provider === 'cashier') return 'Thu ngân'
  return 'Chưa có kênh'
}

function seatLabels(row: Booking) {
  return row.items?.map((item) => item.seat_label).filter(Boolean) ?? []
}

function ageRating(row: Booking) {
  return row.items?.[0]?.seat_type_name ?? 'C13'
}

function setStatusFilter(status: BookingStatus | null) {
  filters.status = status
}

function rowClassName(row: Booking) {
  return expandedRowKeys.value.includes(row.id) ? 'is-expanded-row' : ''
}

function renderIcon(icon: unknown, className = 'inline-icon') {
  return h('span', { class: className }, [h(icon as never)])
}

function renderExpand(row: Booking) {
  const ticketTotal = row.items?.reduce((sum, item) => sum + Number(item.price ?? 0), 0) ?? 0
  const comboTotal = Math.max(Number(row.total_amount ?? 0) - ticketTotal, 0)
  const labels = seatLabels(row)

  return h('div', { class: 'booking-detail' }, [
    h('section', { class: 'detail-block' }, [
      h('h4', 'Chi tiết suất chiếu'),
      h('dl', [
        h('div', [h('dt', 'Ngày chiếu'), h('dd', [renderIcon(CalendarOutline), formatDateTime(row.showtime?.start_time)])]),
        h('div', [h('dt', 'Phòng'), h('dd', row.showtime?.room?.name ?? '-')]),
        h('div', [h('dt', 'Rạp'), h('dd', row.showtime?.room?.cinema?.name ?? '-')]),
        h('div', [h('dt', 'Ghế'), h('dd', labels.length ? labels.join(', ') : '-')]),
      ]),
    ]),
    h('section', { class: 'detail-block' }, [
      h('h4', 'Thanh toán'),
      h('dl', [
        h('div', [h('dt', 'Kênh'), h('dd', providerLabel(row.payment?.provider))]),
        h('div', [h('dt', 'Mã giao dịch'), h('dd', row.payment?.transaction_code ?? '-')]),
        h('div', [h('dt', 'Paid at'), h('dd', formatDateTime(row.payment?.paid_at ?? undefined))]),
      ]),
    ]),
    h('section', { class: 'invoice-card' }, [
      h('h4', 'Chi tiết hóa đơn'),
      h('div', [h('span', 'Giá vé'), h('strong', money(ticketTotal))]),
      h('div', [h('span', 'Đồ ăn & nước'), h('strong', money(comboTotal))]),
      h('footer', [h('span', 'Thành tiền'), h('strong', money(row.total_amount))]),
      h('small', `Khách hàng: ${row.user?.email ?? '-'}`),
    ]),
  ])
}

function createColumns(): DataTableColumns<Booking> {
  return [
    {
      title: 'Mã booking',
      key: 'booking',
      width: 140,
      render: (row) =>
        h('div', { class: 'booking-code-cell' }, [
          h('strong', `#${row.booking_code.replace(/^BK-?/, '')}`),
          h('span', formatDateTime(row.created_at)),
        ]),
    },
    {
      title: 'Khách hàng',
      key: 'customer',
      width: 190,
      render: (row) =>
        h('div', { class: 'primary-cell' }, [
          h('strong', row.user?.name ?? 'Khách hàng'),
          h('span', row.user?.email ?? '-'),
        ]),
    },
    {
      title: 'Phim / phòng chiếu',
      key: 'showtime',
      minWidth: 270,
      render: (row) =>
        h('div', { class: 'movie-cell' }, [
          h('strong', row.showtime?.movie?.title ?? '-'),
          h('div', [
            h('span', { class: 'age-pill' }, ageRating(row)),
            h('span', row.showtime?.room?.name ?? '-'),
          ]),
        ]),
    },
    {
      title: 'Ghế đặt',
      key: 'seats',
      width: 150,
      render: (row) => {
        const labels = seatLabels(row)

        return h('div', { class: 'seat-list' }, labels.slice(0, 4).map((label) => h('span', label)))
      },
    },
    {
      title: 'Tổng tiền',
      key: 'amount',
      width: 140,
      align: 'right',
      render: (row) => h('strong', { class: 'amount-text' }, money(row.total_amount)),
    },
    {
      title: 'Trạng thái',
      key: 'status',
      width: 155,
      render: (row) => {
        const status = bookingStatusMeta(row.status)

        return h(NTag, { round: true, bordered: false, class: status.className, type: status.type }, { default: () => status.label })
      },
    },
    {
      title: 'Hành động',
      key: 'actions',
      width: 180,
      align: 'right',
      render: (row) => {
        const expanded = expandedRowKeys.value.includes(row.id)

        return h('div', { class: 'action-cell' }, [
          row.payment?.status === 'pending'
            ? h(NButton, { size: 'small', type: 'warning', strong: true }, { icon: () => h(CardOutline), default: () => 'Trả ngay' })
            : h(NButton, { size: 'small', secondary: true }, { icon: () => h(EyeOutline), default: () => 'Chi tiết vé' }),
          h(NButton, { size: 'tiny', text: true }, { icon: () => h(TrashOutline) }),
          h(NButton, { size: 'tiny', text: true, onClick: () => toggleExpand(row.id) }, { icon: () => h(expanded ? ChevronUpOutline : ChevronDownOutline) }),
        ])
      },
    },
  ]
}

function toggleExpand(id: string) {
  expandedRowKeys.value = expandedRowKeys.value.includes(id) ? [] : [id]
}

const columns = createColumns()

onMounted(fetchBookings)
</script>

<template>
  <section class="cinema-panel">
    <header class="panel-header">
      <div>
        <h2>
          <TicketOutline />
          Lịch sử đặt vé xem phim
        </h2>
        <p>Tìm kiếm, lọc danh sách đơn đặt vé và quản lý thanh toán trực quan.</p>
      </div>
      <div class="display-count">
        <span>Đang hiển thị</span>
        <strong>{{ visibleSummary }}</strong>
      </div>
    </header>

    <div class="toolbar-grid">
      <n-input
        v-model:value="filters.search"
        placeholder="Tìm theo mã đặt vé, tên khách hàng, SĐT..."
        clearable
        size="large"
      >
        <template #prefix>
          <n-icon><SearchOutline /></n-icon>
        </template>
      </n-input>

      <n-select size="large" placeholder="--- Tất cả phim ---" disabled>
        <template #prefix>
          <n-icon><FilmOutline /></n-icon>
        </template>
      </n-select>

      <n-select size="large" placeholder="Mới nhất trước" disabled />
    </div>

    <div class="status-tabs">
      <button
        v-for="item in statusFilters"
        :key="item.label"
        type="button"
        :class="[{ active: filters.status === item.value }, item.className]"
        @click="setStatusFilter(item.value)"
      >
        {{ item.label }}
      </button>
    </div>

    <n-data-table
      :columns="columns"
      :data="data"
      :expanded-row-keys="expandedRowKeys"
      :loading="loading"
      :pagination="pagination"
      :render-expand="renderExpand"
      :row-class-name="rowClassName"
      :row-key="(row: Booking) => row.id"
      :scroll-x="1220"
      remote
      @update:expanded-row-keys="(keys: DataTableRowKey[]) => (expandedRowKeys = keys)"
    />
  </section>
</template>

<style scoped>
.cinema-panel {
  overflow: hidden;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  background: #fff;
  box-shadow: 0 8px 24px rgb(15 23 42 / 0.04);
  color: #1f2937;
}

.panel-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 16px;
  padding: 28px 24px 18px;
}

.panel-header h2 {
  display: flex;
  align-items: center;
  gap: 8px;
  margin: 0;
  color: #111827;
  font-size: 18px;
  font-weight: 900;
}

.panel-header h2 svg {
  width: 18px;
  color: #6366f1;
}

.panel-header p {
  margin: 8px 0 0;
  color: #64748b;
  font-size: 13px;
}

.display-count {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-top: 8px;
  color: #64748b;
  font-size: 10px;
  font-weight: 800;
  letter-spacing: 0.08em;
  text-transform: uppercase;
}

.display-count strong {
  border-radius: 999px;
  background: #eef2ff;
  color: #4f46e5;
  padding: 6px 12px;
}

.toolbar-grid {
  display: grid;
  grid-template-columns: minmax(260px, 1.25fr) minmax(220px, 1fr) minmax(200px, 0.75fr);
  gap: 12px;
  padding: 0 24px 20px;
}

.status-tabs {
  display: flex;
  flex-wrap: wrap;
  gap: 28px;
  border-top: 1px solid #e5e7eb;
  padding: 14px 24px 22px;
}

.status-tabs button {
  border: 0;
  border-radius: 999px;
  background: transparent;
  color: #64748b;
  cursor: pointer;
  font-size: 12px;
  font-weight: 800;
  padding: 7px 12px;
  text-transform: uppercase;
}

.status-tabs button.active,
.status-tabs button:hover {
  background: #f3f4f6;
  color: #111827;
}

:deep(.n-data-table) {
  --n-td-color: #fff !important;
  --n-td-color-hover: #f8fafc !important;
  --n-th-color: #f9fafb !important;
  --n-border-color: #e5e7eb !important;
  --n-th-text-color: #64748b !important;
  --n-td-text-color: #1f2937 !important;
}

:deep(.n-data-table-th) {
  font-size: 10px !important;
  font-weight: 900 !important;
  letter-spacing: 0.18em;
  text-transform: uppercase;
}

:deep(.n-data-table-td) {
  padding: 18px 24px !important;
}

.booking-code-cell,
.primary-cell,
.movie-cell {
  display: flex;
  flex-direction: column;
  gap: 5px;
}

.booking-code-cell strong {
  width: max-content;
  border-radius: 4px;
  background: #f3f4f6;
  color: #111827;
  font-size: 12px;
  padding: 6px 9px;
}

.booking-code-cell span,
.primary-cell span,
.movie-cell span {
  color: #64748b;
  font-size: 12px;
}

.primary-cell strong,
.movie-cell strong {
  color: #111827;
  font-weight: 900;
}

.movie-cell > div {
  display: flex;
  align-items: center;
  gap: 8px;
}

.age-pill,
.seat-list span {
  border-radius: 4px;
  background: #f1f5f9;
  color: #475569;
  font-size: 11px;
  font-weight: 800;
  padding: 4px 7px;
}

.seat-list {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
}

.amount-text {
  color: #111827;
  font-size: 14px;
  font-weight: 900;
}

.action-cell {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: 8px;
}

:deep(.status-confirmed) {
  background: #dcfce7 !important;
  color: #15803d !important;
}

:deep(.status-pending) {
  background: #fef3c7 !important;
  color: #b45309 !important;
}

:deep(.status-cancelled) {
  background: #fee2e2 !important;
  color: #b91c1c !important;
}

:deep(.status-refunded) {
  background: #dbeafe !important;
  color: #1d4ed8 !important;
}

.booking-detail {
  display: grid;
  grid-template-columns: 1fr 1fr minmax(280px, 0.9fr);
  gap: 24px;
  background: #f8fafc;
  padding: 22px 24px 26px;
}

.detail-block h4,
.invoice-card h4 {
  margin: 0 0 14px;
  color: #4f46e5;
  font-size: 13px;
  font-weight: 900;
  letter-spacing: 0.04em;
  text-transform: uppercase;
}

.detail-block dl {
  display: grid;
  gap: 10px;
  margin: 0;
}

.detail-block dl div,
.invoice-card div,
.invoice-card footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
}

.detail-block dt,
.invoice-card span {
  color: #64748b;
  font-size: 12px;
}

.detail-block dd,
.invoice-card strong {
  display: flex;
  align-items: center;
  gap: 6px;
  margin: 0;
  color: #111827;
  font-size: 12px;
  font-weight: 900;
}

.invoice-card {
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  background: #fff;
  box-shadow: 0 14px 40px rgb(15 23 42 / 0.08);
  padding: 20px;
}

.invoice-card footer {
  border-top: 1px solid #e5e7eb;
  margin-top: 14px;
  padding-top: 14px;
}

.invoice-card footer strong {
  color: #dc2626;
  font-size: 20px;
}

.invoice-card small {
  display: block;
  margin-top: 18px;
  color: #94a3b8;
  font-size: 11px;
}

.inline-icon svg {
  width: 13px;
  color: #6366f1;
}
</style>
