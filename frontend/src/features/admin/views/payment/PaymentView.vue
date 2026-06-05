<script setup lang="ts">
import type { DataTableColumns, DataTableRowKey } from 'naive-ui'
import { NTag } from 'naive-ui'
import { computed, h, onMounted, ref } from 'vue'
import {
  AlertCircleOutline,
  CardOutline,
  CheckmarkDoneCircleOutline,
  ReceiptOutline,
  SearchOutline,
  SwapHorizontalOutline,
  TimeOutline,
  WalletOutline,
} from '@vicons/ionicons5'
import { usePayment } from '@/features/admin/composables/usePayment'
import { useThemeStore } from '@/stores/theme'
import type { Payment, PaymentProvider } from '@/features/admin/types/payment.type'
import type { BookingStatus, PaymentStatus } from '@/features/admin/types/booking.type'
import { formatDateTime } from '@/shared/utils/formatDate'

const themeStore = useThemeStore()
const { data, loading, filters, pagination, fetchPayments } = usePayment()
const expandedRowKeys = ref<DataTableRowKey[]>([])

const statusOptions: Array<{ label: string; value: PaymentStatus | null }> = [
  { label: 'Tất cả trạng thái', value: null },
  { label: 'Đang chờ', value: 'pending' },
  { label: 'Đã thanh toán', value: 'paid' },
  { label: 'Thất bại', value: 'failed' },
  { label: 'Hoàn tiền', value: 'refunded' },
]

const providerOptions: Array<{ label: string; value: PaymentProvider | null }> = [
  { label: 'Tất cả kênh', value: null },
  { label: 'VNPay', value: 'vnpay' },
  { label: 'MoMo', value: 'momo' },
  { label: 'ZaloPay', value: 'zalopay' },
]

const visibleSummary = computed(() => {
  const total = pagination.itemCount || data.value.length
  return `${data.value.length}/${total} giao dịch`
})

const paymentMetrics = computed(() => {
  const rows = data.value
  const paidRows = rows.filter((row) => row.status === 'paid')
  const pendingRows = rows.filter((row) => row.status === 'pending')
  const failedRows = rows.filter((row) => row.status === 'failed')
  const paidAmount = paidRows.reduce((sum, row) => sum + toNumber(row.amount), 0)

  return [
    {
      label: 'Đã ghi nhận',
      value: money(paidAmount),
      caption: `${paidRows.length} giao dịch thành công`,
      icon: WalletOutline,
      className: 'metric-revenue',
    },
    {
      label: 'Đang chờ',
      value: pendingRows.length.toString(),
      caption: 'Cần đối soát hoặc callback',
      icon: TimeOutline,
      className: 'metric-pending',
    },
    {
      label: 'Thanh toán lỗi',
      value: failedRows.length.toString(),
      caption: 'Cần kiểm tra lại cổng thanh toán',
      icon: AlertCircleOutline,
      className: 'metric-failed',
    },
  ]
})

function toNumber(value?: number | string | null) {
  return Number(value ?? 0)
}

function money(value?: number | string | null) {
  return toNumber(value).toLocaleString('vi-VN') + ' đ'
}

function statusMeta(status: PaymentStatus) {
  if (status === 'paid') return { label: 'Đã thanh toán', type: 'success' as const, className: 'status-confirmed' }
  if (status === 'pending') return { label: 'Đang chờ', type: 'warning' as const, className: 'status-pending' }
  if (status === 'refunded') return { label: 'Đã hoàn tiền', type: 'info' as const, className: 'status-refunded' }
  return { label: 'Thất bại', type: 'error' as const, className: 'status-cancelled' }
}

function bookingStatusMeta(status?: BookingStatus) {
  if (status === 'confirmed') return 'Đã xác nhận'
  if (status === 'pending') return 'Chờ thanh toán'
  if (status === 'refunded') return 'Đã hoàn tiền'
  if (status === 'expired') return 'Hết hạn'
  if (status === 'cancelled') return 'Đã hủy'
  return '-'
}

function providerLabel(provider?: string | null) {
  return providerOptions.find((option) => option.value === provider)?.label ?? provider?.toUpperCase() ?? '-'
}

function providerClass(provider?: string | null) {
  if (provider === 'momo') return 'provider-momo'
  if (provider === 'zalopay') return 'provider-zalopay'
  return 'provider-vnpay'
}

function bookingCode(row: Payment) {
  return row.booking?.booking_code ?? '-'
}

function customerName(row: Payment) {
  return row.booking?.user?.name ?? 'Khách hàng'
}

function movieTitle(row: Payment) {
  return row.booking?.showtime?.movie?.title ?? '-'
}

function cinemaLabel(row: Payment) {
  return row.booking?.showtime?.room?.cinema?.name ?? '-'
}

function roomLabel(row: Payment) {
  return row.booking?.showtime?.room?.name ?? '-'
}

function showtimeLabel(row: Payment) {
  return formatDateTime(row.booking?.showtime?.start_time ?? row.booking?.showtime?.show_date)
}

function renderStatusTag(status: PaymentStatus) {
  const meta = statusMeta(status)
  return h(NTag, { round: true, bordered: false, class: meta.className, type: meta.type }, { default: () => meta.label })
}

function renderIcon(icon: unknown, className = 'inline-icon') {
  return h('span', { class: className }, [h(icon as never)])
}

function renderExpand(row: Payment) {
  return h('div', { class: 'payment-detail' }, [
    h('section', { class: 'detail-block' }, [
      h('div', { class: 'detail-heading' }, [renderIcon(ReceiptOutline), h('h4', 'Thông tin giao dịch')]),
      h('dl', [
        h('div', [h('dt', 'Mã giao dịch'), h('dd', row.transaction_code ?? 'Chưa có mã')]),
        h('div', [h('dt', 'Kênh'), h('dd', [h('span', { class: `provider-pill ${providerClass(row.provider)}` }, providerLabel(row.provider))])]),
        h('div', [h('dt', 'Trạng thái'), h('dd', [renderStatusTag(row.status)])]),
        h('div', [h('dt', 'Số tiền'), h('dd', money(row.amount))]),
      ]),
    ]),
    h('section', { class: 'detail-block' }, [
      h('div', { class: 'detail-heading' }, [renderIcon(CardOutline), h('h4', 'Booking liên quan')]),
      h('dl', [
        h('div', [h('dt', 'Mã booking'), h('dd', bookingCode(row))]),
        h('div', [h('dt', 'Trạng thái booking'), h('dd', bookingStatusMeta(row.booking?.status))]),
        h('div', [h('dt', 'Khách hàng'), h('dd', customerName(row))]),
        h('div', [h('dt', 'Email'), h('dd', row.booking?.user?.email ?? '-')]),
      ]),
    ]),
    h('section', { class: 'detail-block' }, [
      h('div', { class: 'detail-heading' }, [renderIcon(TimeOutline), h('h4', 'Suất chiếu')]),
      h('dl', [
        h('div', [h('dt', 'Phim'), h('dd', movieTitle(row))]),
        h('div', [h('dt', 'Thời gian'), h('dd', showtimeLabel(row))]),
        h('div', [h('dt', 'Rạp'), h('dd', cinemaLabel(row))]),
        h('div', [h('dt', 'Phòng'), h('dd', roomLabel(row))]),
      ]),
    ]),
    h('section', { class: 'detail-block' }, [
      h('div', { class: 'detail-heading' }, [renderIcon(SwapHorizontalOutline), h('h4', 'Dòng tiền')]),
      h('dl', [
        h('div', [h('dt', 'Tạo lúc'), h('dd', formatDateTime(row.created_at))]),
        h('div', [h('dt', 'Ghi nhận lúc'), h('dd', formatDateTime(row.paid_at ?? undefined))]),
        h('div', [h('dt', 'Số tiền hoàn'), h('dd', money(row.refunded_amount))]),
        h('div', [h('dt', 'Trạng thái hoàn'), h('dd', row.refund_status ?? '-')]),
      ]),
    ]),
  ])
}

function rowClassName(row: Payment) {
  return expandedRowKeys.value.includes(row.id) ? 'is-expanded-row' : ''
}

function createColumns(): DataTableColumns<Payment> {
  return [
    {
      type: 'expand',
      width: 48,
      fixed: 'left',
      renderExpand,
    },
    {
      title: 'Mã giao dịch',
      key: 'payment',
      width: 230,
      fixed: 'left',
      render: (row) =>
        h('div', { class: 'payment-code-cell' }, [
          h('strong', row.transaction_code ?? 'Chưa có mã'),
          h('span', formatDateTime(row.created_at)),
        ]),
    },
    {
      title: 'Booking',
      key: 'booking',
      width: 190,
      render: (row) =>
        h('div', { class: 'primary-cell' }, [
          h('strong', bookingCode(row)),
          h('span', bookingStatusMeta(row.booking?.status)),
        ]),
    },
    {
      title: 'Khách hàng',
      key: 'customer',
      width: 230,
      render: (row) =>
        h('div', { class: 'primary-cell' }, [
          h('strong', customerName(row)),
          h('span', row.booking?.user?.email ?? '-'),
        ]),
    },
    {
      title: 'Nội dung thanh toán',
      key: 'content',
      minWidth: 330,
      render: (row) =>
        h('div', { class: 'movie-cell' }, [
          h('strong', movieTitle(row)),
          h('div', [
            h('span', { class: `provider-pill ${providerClass(row.provider)}` }, providerLabel(row.provider)),
            h('span', `${cinemaLabel(row)} - ${roomLabel(row)}`),
          ]),
        ]),
    },
    {
      title: 'Số tiền',
      key: 'amount',
      width: 150,
      align: 'right',
      render: (row) =>
        h('div', { class: 'amount-cell' }, [
          h('strong', money(row.amount)),
          h('span', showtimeLabel(row)),
        ]),
    },
    {
      title: 'Trạng thái',
      key: 'status',
      width: 150,
      render: (row) => renderStatusTag(row.status),
    },
    {
      title: 'Dòng tiền',
      key: 'cashflow',
      width: 180,
      render: (row) =>
        h('div', { class: 'primary-cell' }, [
          h('strong', row.paid_at ? 'Đã ghi nhận' : 'Chưa ghi nhận'),
          h('span', formatDateTime(row.paid_at ?? row.created_at)),
        ]),
    },
  ]
}

const columns = createColumns()

onMounted(fetchPayments)
</script>

<template>
  <n-space vertical :size="16" class="payment-page" :class="{ 'is-dark': themeStore.isDark }">
    <n-card :bordered="false" class="page-card">
      <template #header>
        <n-space align="center" :size="10">
          <n-icon size="22" color="#2563eb">
            <WalletOutline />
          </n-icon>
          <span>Đối soát thanh toán</span>
        </n-space>
      </template>

      <template #header-extra>
        <n-tag round type="info" :bordered="false">
          {{ visibleSummary }}
        </n-tag>
      </template>

      <n-space vertical :size="16">
        <div class="metric-grid">
          <article v-for="metric in paymentMetrics" :key="metric.label" class="metric-card" :class="metric.className">
            <span class="metric-icon">
              <component :is="metric.icon" />
            </span>
            <div>
              <p>{{ metric.label }}</p>
              <strong>{{ metric.value }}</strong>
              <small>{{ metric.caption }}</small>
            </div>
          </article>
        </div>

        <div class="payment-toolbar">
          <n-input
            v-model:value="filters.search"
            class="toolbar-search"
            placeholder="Tìm mã giao dịch, booking hoặc khách hàng..."
            clearable
          >
            <template #prefix>
              <n-icon><SearchOutline /></n-icon>
            </template>
          </n-input>

          <n-select
            v-model:value="filters.provider"
            class="toolbar-provider"
            :options="providerOptions"
            label-field="label"
            value-field="value"
            placeholder="Tất cả kênh"
            clearable
          />

          <n-select
            v-model:value="filters.status"
            class="toolbar-status"
            :options="statusOptions"
            label-field="label"
            value-field="value"
            placeholder="Tất cả trạng thái"
            clearable
          />

          <n-date-picker
            v-model:value="filters.dateRange"
            class="toolbar-date"
            type="daterange"
            clearable
            format="dd/MM/yyyy"
            start-placeholder="Từ ngày"
            end-placeholder="Đến ngày"
          />

          <div class="toolbar-count">
            <n-icon><CheckmarkDoneCircleOutline /></n-icon>
            <span>{{ visibleSummary }}</span>
          </div>
        </div>

        <n-button-group>
          <n-button
            v-for="item in statusOptions"
            :key="item.label"
            :type="filters.status === item.value ? 'primary' : 'default'"
            secondary
            @click="filters.status = item.value"
          >
            {{ item.value === null ? 'Tất cả' : item.label }}
          </n-button>
        </n-button-group>
      </n-space>
    </n-card>

    <n-card :bordered="false" content-style="padding: 0" class="table-card">
      <n-data-table
        :columns="columns"
        :data="data"
        :expanded-row-keys="expandedRowKeys"
        :loading="loading"
        :pagination="pagination"
        :row-class-name="rowClassName"
        :row-key="(row: Payment) => row.id"
        :scroll-x="1500"
        remote
        @update:expanded-row-keys="(keys: DataTableRowKey[]) => (expandedRowKeys = keys)"
      />
    </n-card>
  </n-space>
</template>

<style scoped>
.payment-page {
  --view-surface: #ffffff;
  --view-surface-soft: #f8fafc;
  --view-surface-muted: #f1f5f9;
  --view-border: #e5e7eb;
  --view-border-soft: #eef2f7;
  --view-text: #111827;
  --view-body: #1f2937;
  --view-muted: #64748b;
  --view-table-head: #f8fafc;
  --view-expanded: #fcfcfd;
  --view-shadow: 0 1px 2px rgb(15 23 42 / 0.04);

  color: var(--view-body);
}

.payment-page.is-dark {
  --view-surface: #18181c;
  --view-surface-soft: #222228;
  --view-surface-muted: #2a2a31;
  --view-border: rgba(255, 255, 255, 0.1);
  --view-border-soft: rgba(255, 255, 255, 0.08);
  --view-text: #f4f4f5;
  --view-body: #d4d4d8;
  --view-muted: #a1a1aa;
  --view-table-head: #202027;
  --view-expanded: #1f1f25;
  --view-shadow: 0 1px 2px rgb(0 0 0 / 0.22);
}

.page-card,
.table-card {
  border: 1px solid var(--view-border-soft);
  border-radius: 8px;
  box-shadow: var(--view-shadow);
}

:deep(.page-card .n-card-header__main) {
  font-size: 18px;
  font-weight: 700;
}

.metric-grid {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 12px;
}

.metric-card {
  display: flex;
  min-height: 104px;
  align-items: center;
  gap: 14px;
  border: 1px solid var(--view-border);
  border-radius: 8px;
  background: var(--view-surface);
  padding: 16px;
}

.metric-icon {
  display: grid;
  width: 44px;
  height: 44px;
  flex: 0 0 auto;
  place-items: center;
  border-radius: 8px;
}

:deep(.metric-icon svg) {
  width: 22px;
  height: 22px;
}

.metric-card p,
.metric-card small {
  margin: 0;
  color: var(--view-muted);
  font-size: 12px;
}

.metric-card strong {
  display: block;
  margin: 3px 0;
  color: var(--view-text);
  font-size: 22px;
  font-weight: 800;
}

.metric-revenue .metric-icon {
  background: #dcfce7;
  color: #15803d;
}

.metric-pending .metric-icon {
  background: #fef3c7;
  color: #b45309;
}

.metric-failed .metric-icon {
  background: #fee2e2;
  color: #b91c1c;
}

.payment-page.is-dark .metric-revenue .metric-icon {
  background: rgba(34, 197, 94, 0.16);
  color: #86efac;
}

.payment-page.is-dark .metric-pending .metric-icon {
  background: rgba(245, 158, 11, 0.16);
  color: #fcd34d;
}

.payment-page.is-dark .metric-failed .metric-icon {
  background: rgba(239, 68, 68, 0.16);
  color: #fca5a5;
}

.payment-toolbar {
  display: grid;
  grid-template-columns: minmax(240px, 1fr) minmax(140px, 180px) minmax(170px, 220px) minmax(250px, 310px) auto;
  gap: 12px;
  align-items: center;
}

.toolbar-search,
.toolbar-provider,
.toolbar-status,
.toolbar-date {
  min-width: 0;
}

.toolbar-count {
  display: inline-flex;
  min-width: 132px;
  height: 34px;
  align-items: center;
  justify-content: center;
  gap: 7px;
  border: 1px solid #dbeafe;
  border-radius: 8px;
  background: #eff6ff;
  color: #1d4ed8;
  font-size: 13px;
  font-weight: 700;
  padding: 0 12px;
  white-space: nowrap;
}

.payment-page.is-dark .toolbar-count {
  border-color: rgba(59, 130, 246, 0.28);
  background: rgba(59, 130, 246, 0.12);
  color: #93c5fd;
}

.toolbar-count svg {
  width: 15px;
  height: 15px;
}

:deep(.n-data-table-th) {
  background: var(--view-table-head) !important;
  color: var(--view-muted) !important;
  font-size: 12px !important;
  font-weight: 700 !important;
}

:deep(.n-data-table-td) {
  vertical-align: top;
}

:deep(.is-expanded-row td) {
  background: var(--view-expanded) !important;
}

:deep(.payment-code-cell),
:deep(.primary-cell),
:deep(.movie-cell),
:deep(.amount-cell) {
  display: flex;
  flex-direction: column;
  gap: 6px;
  min-width: 0;
  line-height: 1.35;
}

:deep(.payment-code-cell strong),
:deep(.primary-cell strong),
:deep(.movie-cell strong),
:deep(.amount-cell strong) {
  display: block;
  color: var(--view-text);
  font-weight: 700;
  overflow-wrap: anywhere;
}

:deep(.payment-code-cell span),
:deep(.primary-cell span),
:deep(.movie-cell span),
:deep(.amount-cell span) {
  display: block;
  color: var(--view-muted);
  font-size: 12px;
  overflow-wrap: anywhere;
}

:deep(.movie-cell > div) {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 8px;
}

:deep(.amount-cell) {
  align-items: flex-end;
}

:deep(.amount-cell strong) {
  font-size: 14px;
}

:deep(.provider-pill) {
  display: inline-flex;
  width: fit-content;
  align-items: center;
  border-radius: 6px;
  font-size: 11px;
  font-weight: 700;
  padding: 4px 8px;
}

:deep(.provider-vnpay) {
  background: #dbeafe;
  color: #1d4ed8;
}

:deep(.provider-momo) {
  background: #fce7f3;
  color: #be185d;
}

:deep(.provider-zalopay) {
  background: #e0f2fe;
  color: #0369a1;
}

.payment-page.is-dark :deep(.provider-vnpay) {
  background: rgba(59, 130, 246, 0.16);
  color: #93c5fd;
}

.payment-page.is-dark :deep(.provider-momo) {
  background: rgba(236, 72, 153, 0.16);
  color: #f9a8d4;
}

.payment-page.is-dark :deep(.provider-zalopay) {
  background: rgba(14, 165, 233, 0.18);
  color: #7dd3fc;
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

.payment-page.is-dark :deep(.status-confirmed) {
  background: rgba(34, 197, 94, 0.16) !important;
  color: #86efac !important;
}

.payment-page.is-dark :deep(.status-pending) {
  background: rgba(245, 158, 11, 0.16) !important;
  color: #fcd34d !important;
}

.payment-page.is-dark :deep(.status-cancelled) {
  background: rgba(239, 68, 68, 0.16) !important;
  color: #fca5a5 !important;
}

.payment-page.is-dark :deep(.status-refunded) {
  background: rgba(59, 130, 246, 0.16) !important;
  color: #93c5fd !important;
}

:deep(.payment-detail) {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 14px;
  background: var(--view-surface-soft);
  padding: 16px 24px 20px;
}

:deep(.detail-block) {
  border: 1px solid var(--view-border);
  border-radius: 8px;
  background: var(--view-surface);
  padding: 16px;
}

:deep(.detail-heading) {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 14px;
}

:deep(.detail-heading h4) {
  margin: 0;
  color: var(--view-body);
  font-size: 13px;
  font-weight: 800;
}

:deep(.detail-block dl) {
  display: grid;
  gap: 10px;
  margin: 0;
}

:deep(.detail-block dl div) {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
}

:deep(.detail-block dt) {
  color: var(--view-muted);
  font-size: 12px;
}

:deep(.detail-block dd) {
  display: flex;
  align-items: center;
  gap: 6px;
  min-width: 0;
  margin: 0;
  color: var(--view-text);
  font-size: 12px;
  font-weight: 700;
  text-align: right;
}

:deep(.inline-icon svg) {
  width: 14px;
  height: 14px;
  color: #2563eb;
}

.payment-page.is-dark :deep(.inline-icon svg) {
  color: #93c5fd;
}

@media (max-width: 1200px) {
  .payment-toolbar,
  :deep(.payment-detail) {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

@media (max-width: 768px) {
  .metric-grid,
  .payment-toolbar,
  :deep(.payment-detail) {
    grid-template-columns: 1fr;
  }

  .toolbar-count {
    justify-content: flex-start;
  }
}
</style>
