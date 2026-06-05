import api from '@/shared/api/api'
import type { ApiResponse } from '@/shared/types/apiResponse'
import type { DashboardStatistics, StatisticGranularity } from '@/features/admin/types/statistic.type'

export const statisticService = {
  getDashboard: (params?: {
    from_date?: string
    to_date?: string
    granularity?: StatisticGranularity
  }) => api.get<ApiResponse<DashboardStatistics>>('/statistics/dashboard', { params }),
}
