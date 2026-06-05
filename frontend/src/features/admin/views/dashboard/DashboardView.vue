<script setup lang="ts">
import { computed, onMounted, reactive, ref } from 'vue'
import {
  BarChartOutline,
  CalendarOutline,
  CardOutline,
  FilmOutline,
  PieChartOutline,
  RefreshOutline,
  TicketOutline,
  WalletOutline,
} from '@vicons/ionicons5'
import { statisticService } from '@/features/admin/services/statistic.service'
import { useThemeStore } from '@/stores/theme'
import type {
  DashboardStatistics,
  StatisticGranularity,
  TopCinema,
  TopMovie,
} from '@/features/admin/types/statistic.type'

const themeStore = useThemeStore()
const loading = ref(false)
const dashboard = ref<DashboardStatistics | null>(null)
const quickRange = ref('30')
const filters = reactive<{
  from_date: string | null
  to_date: string | null
  granularity: StatisticGranularity
}>({
  from_date: null,
  to_date: null,
  granularity: 'day' as StatisticGranularity,
})

const maxRevenue = computed(() =>
  Math.max(...(dashboard.value?.revenue_series.map((item) => item.revenue) ?? [0]), 1),
)

const maxTickets = computed(() =>
  Math.max(...(dashboard.value?.ticket_series.map((item) => item.tickets) ?? [0]), 1),
)

const bookingFunnelItems = computed(() => {
  const funnel = dashboard.value?.booking_funnel
  if (!funnel) return []

  return [
    { label: 'Chờ thanh toán', value: funnel.pending, className: 'pending' },
    { label: 'Đã xác nhận', value: funnel.confirmed, className: 'confirmed' },
    { label: 'Đã hủy', value: funnel.cancelled, className: 'cancelled' },
    { label: 'Hết hạn', value: funnel.expired, className: 'expired' },
    { label: 'Hoàn tiền', value: funnel.refunded, className: 'refunded' },
  ]
})

const metricCards = computed(() => {
  const kpis = dashboard.value?.kpis
  if (!kpis) return []

  return [
    {
      label: 'Doanh thu thanh toán',
      value: money(kpis.revenue.value),
      caption: `${number(kpis.revenue.paid_orders)} giao dịch paid`,
      growth: kpis.revenue.growth_rate,
      icon: WalletOutline,
      tone: 'green',
    },
    {
      label: 'Vé đã bán',
      value: number(kpis.tickets_sold.value),
      caption: 'Booking đã xác nhận',
      growth: kpis.tickets_sold.growth_rate,
      icon: TicketOutline,
      tone: 'blue',
    },
    {
      label: 'Tỉ lệ lấp đầy',
      value: percent(kpis.occupancy_rate.value),
      caption: `${number(kpis.occupancy_rate.sold_seats)} / ${number(kpis.occupancy_rate.available_seats)} ghế`,
      icon: PieChartOutline,
      tone: 'amber',
    },
    {
      label: 'Giá trị đơn TB',
      value: money(kpis.average_order_value.value),
      caption: 'Average order value',
      icon: CardOutline,
      tone: 'violet',
    },
  ]
})

function todayString() {
  return new Date().toISOString().slice(0, 10)
}

function daysAgoString(days: number) {
  const date = new Date()
  date.setDate(date.getDate() - days + 1)
  return date.toISOString().slice(0, 10)
}

function applyQuickRange(days: string) {
  quickRange.value = days
  filters.to_date = todayString()
  filters.from_date = daysAgoString(Number(days))
  filters.granularity = Number(days) > 62 ? 'month' : 'day'
  fetchDashboard()
}

async function fetchDashboard() {
  loading.value = true
  try {
    const response = await statisticService.getDashboard({
      from_date: filters.from_date || undefined,
      to_date: filters.to_date || undefined,
      granularity: filters.granularity,
    })
    dashboard.value = response.data
  } finally {
    loading.value = false
  }
}

function number(value?: number | null) {
  return Number(value ?? 0).toLocaleString('vi-VN')
}

function money(value?: number | null) {
  return Number(value ?? 0).toLocaleString('vi-VN') + ' đ'
}

function percent(value?: number | null) {
  return `${number(value)}%`
}

function growthLabel(value?: number) {
  if (value === undefined) return ''
  return `${value > 0 ? '+' : ''}${number(value)}%`
}

function providerLabel(provider: string) {
  if (provider === 'vnpay') return 'VNPay'
  if (provider === 'momo') return 'MoMo'
  if (provider === 'zalopay') return 'ZaloPay'
  return provider || 'Khác'
}

function periodLabel(period: string) {
  if (period.length === 7) return period
  const [, month, day] = period.split('-')
  return `${day}/${month}`
}

function moviePercent(row: TopMovie) {
  const max = Math.max(...(dashboard.value?.top_movies.map((item) => item.tickets) ?? [1]), 1)
  return `${Math.max((row.tickets / max) * 100, 6)}%`
}

function cinemaPercent(row: TopCinema) {
  const max = Math.max(...(dashboard.value?.top_cinemas.map((item) => item.revenue) ?? [1]), 1)
  return `${Math.max((row.revenue / max) * 100, 6)}%`
}

onMounted(() => applyQuickRange('30'))
</script>

<template>
  <div class="dashboard-page" :class="{ 'is-dark': themeStore.isDark }">
    <header class="dashboard-header">
      <div>
        <p class="eyebrow">Thống kê nghiệp vụ</p>
        <h1>Tổng quan vận hành rạp</h1>
      </div>

      <div class="dashboard-filters">
        <n-button-group>
          <n-button :type="quickRange === '7' ? 'primary' : 'default'" @click="applyQuickRange('7')">
            7 ngày
          </n-button>
          <n-button :type="quickRange === '30' ? 'primary' : 'default'" @click="applyQuickRange('30')">
            30 ngày
          </n-button>
          <n-button :type="quickRange === '90' ? 'primary' : 'default'" @click="applyQuickRange('90')">
            90 ngày
          </n-button>
        </n-button-group>

        <n-date-picker
          v-model:formatted-value="filters.from_date"
          value-format="yyyy-MM-dd"
          type="date"
          clearable
          placeholder="Từ ngày"
        />
        <n-date-picker
          v-model:formatted-value="filters.to_date"
          value-format="yyyy-MM-dd"
          type="date"
          clearable
          placeholder="Đến ngày"
        />
        <n-select
          v-model:value="filters.granularity"
          class="granularity-select"
          :options="[
            { label: 'Theo ngày', value: 'day' },
            { label: 'Theo tháng', value: 'month' },
          ]"
        />
        <n-button type="primary" :loading="loading" @click="fetchDashboard">
          <template #icon>
            <n-icon><RefreshOutline /></n-icon>
          </template>
          Cập nhật
        </n-button>
      </div>
    </header>

    <n-spin :show="loading">
      <section class="metric-grid">
        <article v-for="metric in metricCards" :key="metric.label" class="metric-card" :class="metric.tone">
          <span class="metric-icon">
            <n-icon :component="metric.icon" />
          </span>
          <div>
            <p>{{ metric.label }}</p>
            <strong>{{ metric.value }}</strong>
            <small>
              {{ metric.caption }}
              <span v-if="metric.growth !== undefined" :class="['growth', { down: metric.growth < 0 }]">
                {{ growthLabel(metric.growth) }}
              </span>
            </small>
          </div>
        </article>
      </section>

      <section class="dashboard-grid">
        <article class="panel wide">
          <div class="panel-heading">
            <div>
              <p class="eyebrow">Dòng tiền</p>
              <h2>Doanh thu theo kỳ</h2>
            </div>
            <n-icon><BarChartOutline /></n-icon>
          </div>
          <div class="bar-chart revenue-chart">
            <div v-for="item in dashboard?.revenue_series ?? []" :key="item.period" class="bar-item">
              <span class="bar-value">{{ money(item.revenue) }}</span>
              <div class="bar-track">
                <span :style="{ height: `${Math.max((item.revenue / maxRevenue) * 100, 4)}%` }" />
              </div>
              <small>{{ periodLabel(item.period) }}</small>
            </div>
          </div>
        </article>

        <article class="panel">
          <div class="panel-heading">
            <div>
              <p class="eyebrow">Funnel booking</p>
              <h2>Trạng thái đơn</h2>
            </div>
            <strong>{{ percent(dashboard?.booking_funnel.conversion_rate) }}</strong>
          </div>
          <div class="funnel-list">
            <div v-for="item in bookingFunnelItems" :key="item.label" class="funnel-row">
              <span :class="['dot', item.className]" />
              <span>{{ item.label }}</span>
              <strong>{{ number(item.value) }}</strong>
            </div>
          </div>
        </article>

        <article class="panel">
          <div class="panel-heading">
            <div>
              <p class="eyebrow">Sức bán</p>
              <h2>Vé bán theo kỳ</h2>
            </div>
            <n-icon><TicketOutline /></n-icon>
          </div>
          <div class="line-list">
            <div v-for="item in dashboard?.ticket_series ?? []" :key="item.period">
              <span>{{ periodLabel(item.period) }}</span>
              <div><i :style="{ width: `${Math.max((item.tickets / maxTickets) * 100, 6)}%` }" /></div>
              <strong>{{ number(item.tickets) }}</strong>
            </div>
          </div>
        </article>

        <article class="panel">
          <div class="panel-heading">
            <div>
              <p class="eyebrow">Phim</p>
              <h2>Top phim bán vé</h2>
            </div>
            <n-icon><FilmOutline /></n-icon>
          </div>
          <div class="rank-list">
            <div v-for="movie in dashboard?.top_movies ?? []" :key="movie.id" class="rank-row">
              <div>
                <strong>{{ movie.title }}</strong>
                <span>{{ number(movie.tickets) }} vé · {{ money(movie.ticket_revenue) }}</span>
              </div>
              <i :style="{ width: moviePercent(movie) }" />
            </div>
          </div>
        </article>

        <article class="panel">
          <div class="panel-heading">
            <div>
              <p class="eyebrow">Rạp</p>
              <h2>Top rạp doanh thu</h2>
            </div>
            <n-icon><CalendarOutline /></n-icon>
          </div>
          <div class="rank-list cinema">
            <div v-for="cinema in dashboard?.top_cinemas ?? []" :key="cinema.id" class="rank-row">
              <div>
                <strong>{{ cinema.name }}</strong>
                <span>{{ number(cinema.bookings) }} booking · {{ money(cinema.revenue) }}</span>
              </div>
              <i :style="{ width: cinemaPercent(cinema) }" />
            </div>
          </div>
        </article>

        <article class="panel">
          <div class="panel-heading">
            <div>
              <p class="eyebrow">Thanh toán</p>
              <h2>Kênh ghi nhận tiền</h2>
            </div>
            <n-icon><WalletOutline /></n-icon>
          </div>
          <div class="payment-list">
            <div v-for="method in dashboard?.payment_methods ?? []" :key="method.provider">
              <span>{{ providerLabel(method.provider) }}</span>
              <strong>{{ money(method.revenue) }}</strong>
              <small>{{ number(method.orders) }} giao dịch</small>
            </div>
          </div>
        </article>
      </section>
    </n-spin>
  </div>
</template>

<style scoped>
.dashboard-page {
  --dashboard-surface: #ffffff;
  --dashboard-surface-soft: #f8fafc;
  --dashboard-surface-muted: #f1f5f9;
  --dashboard-border: #e5e7eb;
  --dashboard-border-soft: #eef2f7;
  --dashboard-text: #0f172a;
  --dashboard-heading: #111827;
  --dashboard-muted: #64748b;
  --dashboard-body: #334155;
  --dashboard-shadow: 0 8px 22px rgba(15, 23, 42, 0.04);

  display: flex;
  flex-direction: column;
  gap: 20px;
}

.dashboard-page.is-dark {
  --dashboard-surface: #18181c;
  --dashboard-surface-soft: #222228;
  --dashboard-surface-muted: #2a2a31;
  --dashboard-border: rgba(255, 255, 255, 0.1);
  --dashboard-border-soft: rgba(255, 255, 255, 0.08);
  --dashboard-text: #f4f4f5;
  --dashboard-heading: #fafafa;
  --dashboard-muted: #a1a1aa;
  --dashboard-body: #d4d4d8;
  --dashboard-shadow: 0 8px 22px rgba(0, 0, 0, 0.22);
}

.dashboard-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 16px;
}

.dashboard-header h1,
.panel-heading h2 {
  margin: 0;
  color: var(--dashboard-heading);
  font-weight: 700;
}

.dashboard-header h1 {
  font-size: 26px;
}

.eyebrow {
  margin: 0 0 4px;
  color: var(--dashboard-muted);
  font-size: 12px;
  font-weight: 700;
  letter-spacing: 0;
  text-transform: uppercase;
}

.dashboard-filters {
  display: flex;
  flex-wrap: wrap;
  justify-content: flex-end;
  gap: 10px;
}

.dashboard-filters :deep(.n-date-picker) {
  width: 150px;
}

.granularity-select {
  width: 130px;
}

.metric-grid {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 14px;
  margin-bottom: 20px;
}

.metric-card,
.panel {
  border: 1px solid #e5e7eb;
  border-color: var(--dashboard-border);
  border-radius: 8px;
  background: var(--dashboard-surface);
  box-shadow: var(--dashboard-shadow);
}

.metric-card {
  display: flex;
  gap: 14px;
  padding: 18px;
}

.metric-icon {
  display: grid;
  width: 42px;
  height: 42px;
  flex: 0 0 auto;
  place-items: center;
  border-radius: 8px;
  font-size: 24px;
}

.metric-card.green .metric-icon {
  background: #dcfce7;
  color: #15803d;
}

.metric-card.blue .metric-icon {
  background: #dbeafe;
  color: #1d4ed8;
}

.metric-card.amber .metric-icon {
  background: #fef3c7;
  color: #b45309;
}

.metric-card.violet .metric-icon {
  background: #ede9fe;
  color: #7c3aed;
}

.metric-card p,
.metric-card small {
  margin: 0;
  color: var(--dashboard-muted);
}

.metric-card strong {
  display: block;
  margin: 4px 0;
  color: var(--dashboard-text);
  font-size: 24px;
}

.growth {
  margin-left: 6px;
  color: #16a34a;
  font-weight: 700;
}

.growth.down {
  color: #dc2626;
}

.dashboard-grid {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 16px;
}

.panel {
  min-height: 280px;
  padding: 18px;
}

.panel.wide {
  grid-column: span 2;
}

.panel-heading {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  margin-bottom: 16px;
}

.panel-heading h2 {
  font-size: 17px;
}

.panel-heading > .n-icon {
  color: var(--dashboard-muted);
  font-size: 24px;
}

.bar-chart {
  display: flex;
  align-items: end;
  gap: 8px;
  height: 225px;
  overflow-x: auto;
  padding-bottom: 4px;
}

.bar-item {
  display: grid;
  min-width: 44px;
  height: 100%;
  grid-template-rows: 26px 1fr 20px;
  text-align: center;
}

.bar-value {
  color: var(--dashboard-muted);
  font-size: 10px;
  opacity: 0;
  transition: opacity 0.18s ease;
}

.bar-item:hover .bar-value {
  opacity: 1;
}

.bar-track {
  display: flex;
  align-items: end;
  justify-content: center;
  border-radius: 8px;
  background: var(--dashboard-surface-muted);
}

.bar-track span {
  width: 70%;
  border-radius: 8px 8px 0 0;
  background: linear-gradient(180deg, #22c55e, #0f766e);
}

.bar-item small {
  color: var(--dashboard-muted);
  font-size: 11px;
}

.funnel-list,
.payment-list,
.rank-list,
.line-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.funnel-row,
.payment-list > div {
  display: grid;
  grid-template-columns: auto 1fr auto;
  align-items: center;
  gap: 10px;
  color: var(--dashboard-body);
}

.dot {
  width: 10px;
  height: 10px;
  border-radius: 999px;
}

.dot.pending {
  background: #f59e0b;
}

.dot.confirmed {
  background: #22c55e;
}

.dot.cancelled {
  background: #ef4444;
}

.dot.expired {
  background: #94a3b8;
}

.dot.refunded {
  background: #38bdf8;
}

.line-list > div {
  display: grid;
  grid-template-columns: 56px 1fr 46px;
  align-items: center;
  gap: 10px;
  color: var(--dashboard-body);
  font-size: 12px;
}

.line-list div div {
  height: 8px;
  overflow: hidden;
  border-radius: 999px;
  background: var(--dashboard-surface-muted);
}

.line-list i,
.rank-row i {
  display: block;
  height: 100%;
  border-radius: inherit;
  background: #2563eb;
}

.rank-row {
  position: relative;
  overflow: hidden;
  border-radius: 8px;
  background: var(--dashboard-surface-soft);
  padding: 12px;
}

.rank-row div {
  position: relative;
  z-index: 1;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.rank-row strong {
  color: var(--dashboard-text);
}

.rank-row span,
.payment-list small {
  color: var(--dashboard-muted);
  font-size: 12px;
}

.rank-row i {
  position: absolute;
  inset: auto auto 0 0;
  height: 4px;
  background: #14b8a6;
}

.rank-list.cinema .rank-row i {
  background: #f97316;
}

.payment-list > div {
  grid-template-columns: 1fr auto;
  border-bottom: 1px solid var(--dashboard-border-soft);
  padding-bottom: 10px;
}

.payment-list small {
  grid-column: 1 / -1;
}

@media (max-width: 1200px) {
  .metric-grid,
  .dashboard-grid {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }

  .panel.wide {
    grid-column: span 2;
  }
}

@media (max-width: 760px) {
  .dashboard-header {
    flex-direction: column;
  }

  .dashboard-filters,
  .dashboard-filters :deep(.n-date-picker),
  .granularity-select {
    width: 100%;
  }

  .metric-grid,
  .dashboard-grid {
    grid-template-columns: 1fr;
  }

  .panel.wide {
    grid-column: auto;
  }
}
</style>
