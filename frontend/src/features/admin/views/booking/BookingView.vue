<script setup lang="ts">
import type { DataTableColumns, DataTableRowKey } from 'naive-ui'
import { NButton, NTag } from 'naive-ui'
import { computed, h, onMounted, ref } from 'vue'
import {
  CalendarOutline,
  CardOutline,
  CheckmarkDoneCircleOutline,
  ChevronDownOutline,
  ChevronUpOutline,
  FilmOutline,
  HourglassOutline,
  PeopleOutline,
  PricetagOutline,
  ReceiptOutline,
  SearchOutline,
  TicketOutline,
  TimeOutline,
  WalletOutline,
} from '@vicons/ionicons5'
import { useBooking } from '@/features/admin/composables/useBooking'
import { useThemeStore } from '@/stores/theme'
import type {
  Booking,
  BookingCombo,
  BookingPromotion,
  BookingStatus,
  PaymentStatus,
} from '@/features/admin/types/booking.type'
import { formatDateTime } from '@/shared/utils/formatDate'

const themeStore = useThemeStore()
const { data, loading, filters, pagination, fetchBookings } = useBooking()
const expandedRowKeys = ref<DataTableRowKey[]>([])

const statusFilters: Array<{ label: string; value: BookingStatus | null }> = [
  { label: 'Tất cả', value: null },
  { label: 'Chờ thanh toán', value: 'pending' },
  { label: 'Đã xác nhận', value: 'confirmed' },
  { label: 'Đã hủy', value: 'cancelled' },
  { label: 'Hoàn tiền', value: 'refunded' },
  { label: 'Hết hạn', value: 'expired' },
]

const bookingMetrics = computed(() => {
  const rows = data.value
  const paidBookings = rows.filter((row) => row.status === 'confirmed' || row.payment?.status === 'paid')
  const pendingBookings = rows.filter((row) => row.status === 'pending')
  const ticketCount = rows.reduce((sum, row) => sum + (row.items?.length ?? 0), 0)
  const paidAmount = paidBookings.reduce((sum, row) => sum + toNumber(row.total_amount), 0)

  return [
    {
      label: 'Doanh thu đã ghi nhận',
      value: money(paidAmount),
      caption: `${paidBookings.length} booking đã thanh toán`,
      icon: WalletOutline,
      className: 'metric-revenue',
    },
    {
      label: 'Đang chờ xử lý',
      value: pendingBookings.length.toString(),
      caption: 'Cần thanh toán/xác nhận',
      icon: HourglassOutline,
      className: 'metric-pending',
    },
    {
      label: 'Vé đã đặt',
      value: ticketCount.toString(),
      caption: `${data.value.length}/${pagination.itemCount || data.value.length} booking đang hiển thị`,
      icon: TicketOutline,
      className: 'metric-ticket',
    },
  ]
})

const visibleSummary = computed(() => {
  const total = pagination.itemCount || data.value.length
  return `${data.value.length}/${total} booking`
})

function toNumber(value?: number | string | null) {
  return Number(value ?? 0)
}

function money(value?: number | string | null) {
  return toNumber(value).toLocaleString('vi-VN') + ' đ'
}

function bookingStatusMeta(status: BookingStatus) {
  if (status === 'confirmed') return { label: 'Đã xác nhận', type: 'success' as const, className: 'status-confirmed' }
  if (status === 'pending') return { label: 'Chờ thanh toán', type: 'warning' as const, className: 'status-pending' }
  if (status === 'refunded') return { label: 'Đã hoàn tiền', type: 'info' as const, className: 'status-refunded' }
  if (status === 'expired') return { label: 'Hết hạn', type: 'default' as const, className: 'status-muted' }
  return { label: 'Đã hủy', type: 'error' as const, className: 'status-cancelled' }
}

function paymentStatusMeta(status?: PaymentStatus) {
  if (status === 'paid') return { label: 'Đã thanh toán', type: 'success' as const, className: 'status-confirmed' }
  if (status === 'pending') return { label: 'Chờ thanh toán', type: 'warning' as const, className: 'status-pending' }
  if (status === 'refunded') return { label: 'Đã hoàn tiền', type: 'info' as const, className: 'status-refunded' }
  if (status === 'failed') return { label: 'Thất bại', type: 'error' as const, className: 'status-cancelled' }
  return { label: 'Chưa tạo thanh toán', type: 'default' as const, className: 'status-muted' }
}

function providerLabel(provider?: string | null) {
  if (provider === 'vnpay') return 'VNPay'
  if (provider === 'momo') return 'MoMo'
  if (provider === 'zalopay') return 'ZaloPay'
  return 'Chưa có kênh'
}

function seatLabels(row: Booking) {
  return row.items?.map((item) => item.seat_label).filter(Boolean) ?? []
}

function ticketTotal(row: Booking) {
  return row.items?.reduce((sum, item) => sum + toNumber(item.price), 0) ?? 0
}

function comboName(combo: BookingCombo) {
  return combo.pivot?.combo_name ?? combo.combo_name ?? combo.name ?? 'Combo'
}

function comboQuantity(combo: BookingCombo) {
  return combo.pivot?.quantity ?? combo.quantity ?? 1
}

function comboTotal(row: Booking) {
  return row.combos?.reduce((sum, combo) => sum + toNumber(combo.pivot?.total_price ?? combo.total_price), 0) ?? 0
}

function promotionDiscount(promotion: BookingPromotion) {
  return toNumber(promotion.pivot?.discount_amount)
}

function discountTotal(row: Booking) {
  return row.promotions?.reduce((sum, promotion) => sum + promotionDiscount(promotion), 0) ?? 0
}

function statusLogs(row: Booking) {
  return row.status_logs ?? row.statusLogs ?? []
}

function showtimeLabel(row: Booking) {
  return formatDateTime(row.showtime?.start_time ?? row.showtime?.show_date)
}

function cinemaLabel(row: Booking) {
  return row.showtime?.room?.cinema?.name ?? '-'
}

function roomLabel(row: Booking) {
  return row.showtime?.room?.name ?? row.items?.[0]?.room_name ?? '-'
}

function movieTitle(row: Booking) {
  return row.showtime?.movie?.title ?? row.items?.[0]?.movie_title ?? '-'
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

function renderStatusTag(status: BookingStatus) {
  const meta = bookingStatusMeta(status)

  return h(NTag, { round: true, bordered: false, class: meta.className, type: meta.type }, { default: () => meta.label })
}

function renderPaymentTag(status?: PaymentStatus) {
  const meta = paymentStatusMeta(status)

  return h(NTag, { round: true, bordered: false, class: meta.className, type: meta.type }, { default: () => meta.label })
}

function renderEmptyText(text = 'Chưa có dữ liệu') {
  return h('span', { class: 'muted-text' }, text)
}

function renderSeatChips(row: Booking, limit = 5) {
  const labels = seatLabels(row)
  const visible = labels.slice(0, limit).map((label) => h('span', { class: 'seat-chip' }, label))

  if (labels.length > limit) {
    visible.push(h('span', { class: 'seat-chip more' }, `+${labels.length - limit}`))
  }

  return visible.length ? visible : [renderEmptyText('-')]
}

function renderExpand(row: Booking) {
  const subtotal = ticketTotal(row) + comboTotal(row)
  const discount = discountTotal(row)
  const logs = statusLogs(row)

  return h('div', { class: 'booking-detail' }, [
    h('section', { class: 'detail-block' }, [
      h('div', { class: 'detail-heading' }, [renderIcon(FilmOutline), h('h4', 'Suất chiếu')]),
      h('dl', [
        h('div', [h('dt', 'Phim'), h('dd', movieTitle(row))]),
        h('div', [h('dt', 'Thời gian'), h('dd', [renderIcon(CalendarOutline), showtimeLabel(row)])]),
        h('div', [h('dt', 'Rạp'), h('dd', cinemaLabel(row))]),
        h('div', [h('dt', 'Phòng'), h('dd', roomLabel(row))]),
      ]),
    ]),
    h('section', { class: 'detail-block' }, [
      h('div', { class: 'detail-heading' }, [renderIcon(PeopleOutline), h('h4', 'Khách & vé')]),
      h('dl', [
        h('div', [h('dt', 'Khách hàng'), h('dd', row.user?.name ?? 'Khách hàng')]),
        h('div', [h('dt', 'Email'), h('dd', row.user?.email ?? '-')]),
        h('div', [h('dt', 'Số vé'), h('dd', `${row.items?.length ?? 0} vé`)]),
        h('div', [h('dt', 'Ghế'), h('dd', { class: 'detail-seat-list' }, renderSeatChips(row, 12))]),
      ]),
    ]),
    h('section', { class: 'detail-block' }, [
      h('div', { class: 'detail-heading' }, [renderIcon(CardOutline), h('h4', 'Thanh toán')]),
      h('dl', [
        h('div', [h('dt', 'Trạng thái'), h('dd', [renderPaymentTag(row.payment?.status)])]),
        h('div', [h('dt', 'Kênh'), h('dd', providerLabel(row.payment?.provider))]),
        h('div', [h('dt', 'Mã giao dịch'), h('dd', row.payment?.transaction_code ?? '-')]),
        h('div', [h('dt', 'Ghi nhận lúc'), h('dd', formatDateTime(row.payment?.paid_at ?? undefined))]),
      ]),
    ]),
    h('section', { class: 'invoice-card' }, [
      h('div', { class: 'detail-heading' }, [renderIcon(ReceiptOutline), h('h4', 'Hóa đơn')]),
      h('div', [h('span', `Vé (${row.items?.length ?? 0})`), h('strong', money(ticketTotal(row)))]),
      h('div', [h('span', 'Combo'), h('strong', money(comboTotal(row)))]),
      h('div', [h('span', 'Tạm tính'), h('strong', money(subtotal))]),
      h('div', { class: discount > 0 ? 'discount-line' : '' }, [h('span', 'Khuyến mãi'), h('strong', discount > 0 ? `-${money(discount)}` : money(0))]),
      h('footer', [h('span', 'Thành tiền'), h('strong', money(row.total_amount))]),
    ]),
    h('section', { class: 'wide-detail' }, [
      h('div', { class: 'detail-heading' }, [renderIcon(PricetagOutline), h('h4', 'Combo & khuyến mãi')]),
      h('div', { class: 'compact-list' }, [
        ...(row.combos?.length
          ? row.combos.map((combo) =>
              h('div', [
                h('span', `${comboName(combo)} x${comboQuantity(combo)}`),
                h('strong', money(combo.pivot?.total_price ?? combo.total_price)),
              ]),
            )
          : [h('div', [h('span', 'Combo'), renderEmptyText('Không sử dụng')])]),
        ...(row.promotions?.length
          ? row.promotions.map((promotion) =>
              h('div', [
                h('span', promotion.code ?? promotion.name ?? 'Khuyến mãi'),
                h('strong', `-${money(promotionDiscount(promotion))}`),
              ]),
            )
          : [h('div', [h('span', 'Khuyến mãi'), renderEmptyText('Không áp dụng')])]),
      ]),
    ]),
    h('section', { class: 'wide-detail' }, [
      h('div', { class: 'detail-heading' }, [renderIcon(TimeOutline), h('h4', 'Vòng đời booking')]),
      h('div', { class: 'timeline-list' }, [
        h('div', [h('span', 'Tạo đơn'), h('strong', formatDateTime(row.created_at))]),
        row.confirmed_at ? h('div', [h('span', 'Xác nhận'), h('strong', formatDateTime(row.confirmed_at))]) : null,
        row.expired_at ? h('div', [h('span', 'Hết hạn giữ đơn'), h('strong', formatDateTime(row.expired_at))]) : null,
        row.cancelled_at ? h('div', [h('span', 'Hủy/hoàn'), h('strong', formatDateTime(row.cancelled_at))]) : null,
        ...logs.slice(0, 3).map((log) =>
          h('div', [
            h('span', bookingStatusMeta(log.new_status ?? row.status).label),
            h('strong', formatDateTime(log.changed_at)),
          ]),
        ),
      ].filter(Boolean)),
    ]),
  ])
}

function createColumns(): DataTableColumns<Booking> {
  return [
    {
      type: 'expand',
      width: 48,
      fixed: 'left',
      renderExpand,
    },
    {
      title: 'Mã booking',
      key: 'booking',
      width: 170,
      fixed: 'left',
      render: (row) =>
        h('div', { class: 'booking-code-cell' }, [
          h('strong', row.booking_code),
          h('span', formatDateTime(row.created_at)),
        ]),
    },
    {
      title: 'Khách hàng',
      key: 'customer',
      width: 220,
      render: (row) =>
        h('div', { class: 'primary-cell' }, [
          h('strong', row.user?.name ?? 'Khách hàng'),
          h('span', row.user?.email ?? '-'),
        ]),
    },
    {
      title: 'Phim / suất chiếu',
      key: 'showtime',
      minWidth: 320,
      render: (row) =>
        h('div', { class: 'movie-cell' }, [
          h('strong', movieTitle(row)),
          h('div', [
            h('span', { class: 'showtime-pill' }, showtimeLabel(row)),
            h('span', `${cinemaLabel(row)} - ${roomLabel(row)}`),
          ]),
        ]),
    },
    {
      title: 'Ghế',
      key: 'seats',
      width: 180,
      render: (row) => h('div', { class: 'seat-list' }, renderSeatChips(row)),
    },
    {
      title: 'Thanh toán',
      key: 'payment',
      width: 190,
      render: (row) =>
        h('div', { class: 'payment-cell' }, [
          renderPaymentTag(row.payment?.status),
          h('span', providerLabel(row.payment?.provider)),
        ]),
    },
    {
      title: 'Tổng tiền',
      key: 'amount',
      width: 150,
      align: 'right',
      render: (row) =>
        h('div', { class: 'amount-cell' }, [
          h('strong', money(row.total_amount)),
          h('span', `${row.items?.length ?? 0} vé`),
        ]),
    },
    {
      title: 'Trạng thái',
      key: 'status',
      width: 150,
      render: (row) => renderStatusTag(row.status),
    },
    {
      title: 'Thao tác',
      key: 'actions',
      width: 120,
      align: 'right',
      fixed: 'right',
      render: (row) => {
        const expanded = expandedRowKeys.value.includes(row.id)

        return h('div', { class: 'action-cell' }, [
          h(
            NButton,
            { size: 'small', secondary: true, onClick: () => toggleExpand(row.id) },
            {
              icon: () => h(expanded ? ChevronUpOutline : ChevronDownOutline),
              default: () => (expanded ? 'Thu gọn' : 'Chi tiết'),
            },
          ),
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
  <n-space vertical :size="16" class="booking-page" :class="{ 'is-dark': themeStore.isDark }">
    <n-card :bordered="false" class="page-card">
      <template #header>
        <n-space align="center" :size="10">
          <n-icon size="22" color="#2563eb">
            <TicketOutline />
          </n-icon>
          <span>Quản lý booking vé phim</span>
        </n-space>
      </template>

      <template #header-extra>
        <n-tag round type="info" :bordered="false">
          {{ visibleSummary }}
        </n-tag>
      </template>

      <n-space vertical :size="16">
        <div class="metric-grid">
          <article v-for="metric in bookingMetrics" :key="metric.label" class="metric-card" :class="metric.className">
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

        <div class="booking-toolbar">
          <n-input
            v-model:value="filters.search"
            class="toolbar-search"
            placeholder="Tìm mã booking, khách hàng hoặc tên phim..."
            clearable
          >
            <template #prefix>
              <n-icon><SearchOutline /></n-icon>
            </template>
          </n-input>

          <n-select
            v-model:value="filters.status"
            class="toolbar-status"
            :options="statusFilters"
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
            v-for="item in statusFilters"
            :key="item.label"
            :type="filters.status === item.value ? 'primary' : 'default'"
            secondary
            @click="setStatusFilter(item.value)"
          >
            {{ item.label }}
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
        :row-key="(row: Booking) => row.id"
        :scroll-x="1500"
        remote
        @update:expanded-row-keys="(keys: DataTableRowKey[]) => (expandedRowKeys = keys)"
      />
    </n-card>
  </n-space>
</template>

<style scoped>
.booking-page {
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

.booking-page.is-dark {
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

.metric-ticket .metric-icon {
  background: #dbeafe;
  color: #1d4ed8;
}

.booking-page.is-dark .metric-revenue .metric-icon {
  background: rgba(34, 197, 94, 0.16);
  color: #86efac;
}

.booking-page.is-dark .metric-pending .metric-icon {
  background: rgba(245, 158, 11, 0.16);
  color: #fcd34d;
}

.booking-page.is-dark .metric-ticket .metric-icon {
  background: rgba(59, 130, 246, 0.16);
  color: #93c5fd;
}

.booking-toolbar {
  display: grid;
  grid-template-columns: minmax(260px, 1fr) minmax(170px, 220px) minmax(260px, 320px) auto;
  gap: 12px;
  align-items: center;
}

.toolbar-search,
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

.booking-page.is-dark .toolbar-count {
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

:deep(.booking-code-cell),
:deep(.primary-cell),
:deep(.movie-cell),
:deep(.payment-cell),
:deep(.amount-cell) {
  display: flex;
  flex-direction: column;
  gap: 6px;
  min-width: 0;
  line-height: 1.35;
}

:deep(.booking-code-cell strong),
:deep(.primary-cell strong),
:deep(.movie-cell strong),
:deep(.amount-cell strong) {
  display: block;
  color: var(--view-text);
  font-weight: 700;
  overflow-wrap: anywhere;
}

:deep(.booking-code-cell span),
:deep(.primary-cell span),
:deep(.movie-cell span),
:deep(.payment-cell span),
:deep(.amount-cell span),
:deep(.muted-text) {
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

:deep(.showtime-pill),
:deep(.seat-chip) {
  display: inline-flex;
  width: fit-content;
  align-items: center;
  border-radius: 6px;
  background: var(--view-surface-muted);
  color: var(--view-body);
  font-size: 11px;
  font-weight: 700;
  padding: 4px 7px;
}

:deep(.seat-list),
:deep(.detail-seat-list) {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
}

:deep(.seat-chip.more) {
  background: #e0f2fe;
  color: #0369a1;
}

.booking-page.is-dark :deep(.seat-chip.more) {
  background: rgba(14, 165, 233, 0.18);
  color: #7dd3fc;
}

:deep(.payment-cell) {
  align-items: flex-start;
}

:deep(.amount-cell) {
  align-items: flex-end;
}

:deep(.amount-cell strong) {
  font-size: 14px;
}

:deep(.action-cell) {
  display: flex;
  gap: 8px;
  justify-content: flex-end;
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

.booking-page.is-dark :deep(.status-confirmed) {
  background: rgba(34, 197, 94, 0.16) !important;
  color: #86efac !important;
}

.booking-page.is-dark :deep(.status-pending) {
  background: rgba(245, 158, 11, 0.16) !important;
  color: #fcd34d !important;
}

.booking-page.is-dark :deep(.status-cancelled) {
  background: rgba(239, 68, 68, 0.16) !important;
  color: #fca5a5 !important;
}

.booking-page.is-dark :deep(.status-refunded) {
  background: rgba(59, 130, 246, 0.16) !important;
  color: #93c5fd !important;
}

:deep(.status-muted) {
  background: var(--view-surface-muted) !important;
  color: var(--view-muted) !important;
}

:deep(.booking-detail) {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 14px;
  background: var(--view-surface-soft);
  padding: 16px 24px 20px;
}

:deep(.detail-block),
:deep(.wide-detail),
:deep(.invoice-card) {
  border: 1px solid var(--view-border);
  border-radius: 8px;
  background: var(--view-surface);
  padding: 16px;
}

:deep(.wide-detail) {
  grid-column: span 2;
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

:deep(.detail-block dl div),
:deep(.invoice-card div),
:deep(.invoice-card footer),
:deep(.compact-list div),
:deep(.timeline-list div) {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
}

:deep(.detail-block dt),
:deep(.invoice-card span),
:deep(.compact-list span),
:deep(.timeline-list span) {
  color: var(--view-muted);
  font-size: 12px;
}

:deep(.detail-block dd),
:deep(.invoice-card strong),
:deep(.compact-list strong),
:deep(.timeline-list strong) {
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

:deep(.invoice-card footer) {
  border-top: 1px solid var(--view-border);
  margin-top: 12px;
  padding-top: 12px;
}

:deep(.invoice-card footer strong) {
  color: #dc2626;
  font-size: 20px;
}

.booking-page.is-dark :deep(.invoice-card footer strong) {
  color: #f87171;
}

:deep(.discount-line strong),
:deep(.compact-list strong) {
  color: #15803d;
}

.booking-page.is-dark :deep(.discount-line strong),
.booking-page.is-dark :deep(.compact-list strong) {
  color: #86efac;
}

:deep(.compact-list),
:deep(.timeline-list) {
  display: grid;
  gap: 10px;
}

:deep(.inline-icon svg) {
  width: 14px;
  height: 14px;
  color: #2563eb;
}

.booking-page.is-dark :deep(.inline-icon svg) {
  color: #93c5fd;
}

@media (max-width: 1200px) {
  .booking-toolbar {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }

  :deep(.booking-detail) {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}

@media (max-width: 768px) {
  .metric-grid,
  :deep(.booking-detail) {
    grid-template-columns: 1fr;
  }

  .booking-toolbar {
    grid-template-columns: 1fr;
  }

  .toolbar-count {
    justify-content: flex-start;
  }

  :deep(.wide-detail) {
    grid-column: auto;
  }
}
</style>
