export type StatisticGranularity = 'day' | 'month'

export interface StatisticKpi {
  value: number
  growth_rate?: number
  paid_orders?: number
  sold_seats?: number
  available_seats?: number
}

export interface BookingFunnel {
  total: number
  pending: number
  confirmed: number
  cancelled: number
  expired: number
  refunded: number
  conversion_rate: number
}

export interface RevenuePoint {
  period: string
  revenue: number
}

export interface TicketPoint {
  period: string
  tickets: number
}

export interface TopMovie {
  id: string
  title: string
  tickets: number
  ticket_revenue: number
}

export interface TopCinema {
  id: string
  name: string
  bookings: number
  revenue: number
}

export interface PaymentMethodStatistic {
  provider: string
  orders: number
  revenue: number
}

export interface DashboardStatistics {
  period: {
    from_date: string
    to_date: string
    granularity: StatisticGranularity
  }
  kpis: {
    revenue: StatisticKpi
    tickets_sold: StatisticKpi
    occupancy_rate: StatisticKpi
    average_order_value: StatisticKpi
  }
  booking_funnel: BookingFunnel
  revenue_series: RevenuePoint[]
  ticket_series: TicketPoint[]
  top_movies: TopMovie[]
  top_cinemas: TopCinema[]
  payment_methods: PaymentMethodStatistic[]
}
