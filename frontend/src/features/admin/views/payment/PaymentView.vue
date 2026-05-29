<script setup lang="ts">
import type { DataTableColumns } from 'naive-ui'
import { NTag } from 'naive-ui'
import { h, onMounted } from 'vue'
import {
  CardOutline,
  CashOutline,
  FilmOutline,
  SearchOutline,
  SwapHorizontalOutline,
  WalletOutline,
} from '@vicons/ionicons5'
import { usePayment } from '@/features/admin/composables/usePayment'
import type { Payment } from '@/features/admin/types/payment.type'
import type { PaymentStatus } from '@/features/admin/types/booking.type'
import { formatDateTime } from '@/shared/utils/formatDate'

const { data, loading, filters, pagination, fetchPayments } = usePayment()

const statusOptions = [
  { label: 'Đang chờ', value: 'pending' },
  { label: 'Đã thanh toán', value: 'paid' },
  { label: 'Thất bại', value: 'failed' },
  { label: 'Hoàn tiền', value: 'refunded' },
]

const providerOptions = [
  { label: 'VNPay', value: 'vnpay' },
  { label: 'MoMo', value: 'momo' },
  { label: 'ZaloPay', value: 'zalopay' },
  { label: 'Thu ngân', value: 'cashier' },
]

function money(value?: number | string | null) {
  return Number(value ?? 0).toLocaleString('vi-VN') + ' đ'
}

function statusMeta(status: PaymentStatus) {
  if (status === 'paid') return { label: 'Paid', type: 'success' as const, className: 'status-confirmed' }
  if (status === 'pending') return { label: 'Waiting', type: 'warning' as const, className: 'status-pending' }
  if (status === 'refunded') return { label: 'Refunded', type: 'info' as const, className: 'status-refunded' }
  return { label: 'Failed', type: 'error' as const, className: 'status-cancelled' }
}

function providerLabel(provider: string) {
  return providerOptions.find((option) => option.value === provider)?.label ?? provider.toUpperCase()
}

function providerClass(provider: string) {
  if (provider === 'momo') return 'provider-momo'
  if (provider === 'zalopay') return 'provider-zalopay'
  if (provider === 'cashier') return 'provider-cashier'
  return 'provider-vnpay'
}

function createColumns(): DataTableColumns<Payment> {
  return [
    {
      title: 'Mã giao dịch',
      key: 'payment',
      width: 230,
      render: (row) =>
        h('div', { class: 'payment-code-cell' }, [
          h('strong', row.transaction_code ?? 'Chưa có mã'),
          h('span', formatDateTime(row.created_at)),
        ]),
    },
    {
      title: 'Booking',
      key: 'booking',
      width: 170,
      render: (row) =>
        h('div', { class: 'primary-cell' }, [
          h('strong', `#${row.booking?.booking_code?.replace(/^BK-?/, '') ?? '-'}`),
          h('span', row.booking?.status ?? '-'),
        ]),
    },
    {
      title: 'Khách hàng',
      key: 'customer',
      width: 210,
      render: (row) =>
        h('div', { class: 'primary-cell' }, [
          h('strong', row.booking?.user?.name ?? 'Khách hàng'),
          h('span', row.booking?.user?.email ?? '-'),
        ]),
    },
    {
      title: 'Nội dung thanh toán',
      key: 'content',
      minWidth: 260,
      render: (row) =>
        h('div', { class: 'movie-cell' }, [
          h('strong', row.booking?.showtime?.movie?.title ?? '-'),
          h('div', [
            h('span', { class: 'provider-pill ' + providerClass(row.provider) }, providerLabel(row.provider)),
            h('span', row.booking?.showtime?.room?.cinema?.name ?? '-'),
          ]),
        ]),
    },
    {
      title: 'Số tiền',
      key: 'amount',
      width: 150,
      align: 'right',
      render: (row) => h('strong', { class: 'amount-text' }, money(row.amount)),
    },
    {
      title: 'Trạng thái',
      key: 'status',
      width: 145,
      render: (row) => {
        const status = statusMeta(row.status)

        return h(NTag, { round: true, bordered: false, class: status.className, type: status.type }, { default: () => status.label })
      },
    },
    {
      title: 'Dòng tiền',
      key: 'cashflow',
      width: 170,
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
  <section class="cinema-panel">
    <header class="panel-header">
      <div>
        <h2>
          <WalletOutline />
          Đối soát thanh toán
        </h2>
        <p>Theo dõi giao dịch, kênh thanh toán và trạng thái dòng tiền theo từng booking.</p>
      </div>
      <div class="display-count">
        <span>Đang hiển thị</span>
        <strong>{{ data.length }}/{{ pagination.itemCount || data.length }} giao dịch</strong>
      </div>
    </header>

    <div class="toolbar-grid">
      <n-input
        v-model:value="filters.search"
        placeholder="Tìm mã giao dịch, booking, khách hàng..."
        clearable
        size="large"
      >
        <template #prefix>
          <n-icon><SearchOutline /></n-icon>
        </template>
      </n-input>

      <n-select
        v-model:value="filters.provider"
        :options="providerOptions"
        placeholder="--- Tất cả kênh ---"
        clearable
        size="large"
      >
        <template #prefix>
          <n-icon><CardOutline /></n-icon>
        </template>
      </n-select>

      <n-select
        v-model:value="filters.status"
        :options="statusOptions"
        placeholder="--- Tất cả trạng thái ---"
        clearable
        size="large"
      >
        <template #prefix>
          <n-icon><SwapHorizontalOutline /></n-icon>
        </template>
      </n-select>
    </div>

    <div class="status-tabs">
      <button type="button" :class="{ active: filters.status === null }" @click="filters.status = null">Tất cả</button>
      <button type="button" class="filter-paid" :class="{ active: filters.status === 'paid' }" @click="filters.status = 'paid'">Đã thanh toán</button>
      <button type="button" class="filter-pending" :class="{ active: filters.status === 'pending' }" @click="filters.status = 'pending'">Đang chờ</button>
      <button type="button" class="filter-cancelled" :class="{ active: filters.status === 'failed' }" @click="filters.status = 'failed'">Thất bại</button>
    </div>

    <n-data-table
      :columns="columns"
      :data="data"
      :loading="loading"
      :pagination="pagination"
      :row-key="(row: Payment) => row.id"
      :scroll-x="1340"
      remote
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
  grid-template-columns: minmax(260px, 1.25fr) minmax(200px, 0.8fr) minmax(220px, 0.9fr);
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

.payment-code-cell,
.primary-cell,
.movie-cell {
  display: flex;
  flex-direction: column;
  gap: 5px;
}

.payment-code-cell strong {
  width: max-content;
  border-radius: 4px;
  background: #f3f4f6;
  color: #111827;
  font-size: 12px;
  padding: 6px 9px;
}

.payment-code-cell span,
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

.provider-pill {
  border-radius: 999px;
  font-size: 11px;
  font-weight: 900;
  padding: 4px 8px;
}

.provider-vnpay {
  background: #dbeafe;
  color: #1d4ed8;
}

.provider-momo {
  background: #fce7f3;
  color: #be185d;
}

.provider-zalopay {
  background: #e0f2fe;
  color: #0369a1;
}

.provider-cashier {
  background: #dcfce7;
  color: #15803d;
}

.amount-text {
  color: #111827;
  font-size: 14px;
  font-weight: 900;
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
</style>
