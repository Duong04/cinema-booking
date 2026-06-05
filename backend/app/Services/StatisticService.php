<?php

namespace App\Services;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

class StatisticService
{
    public function dashboard(array $filters = []): array
    {
        [$startDate, $endDate] = $this->resolveDateRange($filters);
        $granularity = $filters['granularity'] ?? 'day';
        $previousRange = $this->previousRange($startDate, $endDate);

        $revenue = $this->revenueSummary($startDate, $endDate);
        $previousRevenue = $this->revenueSummary($previousRange[0], $previousRange[1]);
        $booking = $this->bookingSummary($startDate, $endDate);
        $tickets = $this->ticketSummary($startDate, $endDate);
        $occupancy = $this->occupancySummary($startDate, $endDate, (int) $tickets['sold']);

        return [
            'period' => [
                'from_date' => $startDate->toDateString(),
                'to_date' => $endDate->toDateString(),
                'granularity' => $granularity,
            ],
            'kpis' => [
                'revenue' => [
                    'value' => $revenue['amount'],
                    'paid_orders' => $revenue['orders'],
                    'growth_rate' => $this->percentChange($revenue['amount'], $previousRevenue['amount']),
                ],
                'tickets_sold' => [
                    'value' => $tickets['sold'],
                    'growth_rate' => $this->percentChange($tickets['sold'], $this->ticketSummary($previousRange[0], $previousRange[1])['sold']),
                ],
                'occupancy_rate' => [
                    'value' => $occupancy['rate'],
                    'sold_seats' => $occupancy['sold_seats'],
                    'available_seats' => $occupancy['available_seats'],
                ],
                'average_order_value' => [
                    'value' => $revenue['orders'] > 0 ? round($revenue['amount'] / $revenue['orders'], 2) : 0,
                ],
            ],
            'booking_funnel' => $booking,
            'revenue_series' => $this->revenueSeries($startDate, $endDate, $granularity),
            'ticket_series' => $this->ticketSeries($startDate, $endDate, $granularity),
            'top_movies' => $this->topMovies($startDate, $endDate),
            'top_cinemas' => $this->topCinemas($startDate, $endDate),
            'payment_methods' => $this->paymentMethods($startDate, $endDate),
        ];
    }

    private function resolveDateRange(array $filters): array
    {
        $endDate = isset($filters['to_date'])
            ? Carbon::parse($filters['to_date'])->endOfDay()
            : now()->endOfDay();

        $startDate = isset($filters['from_date'])
            ? Carbon::parse($filters['from_date'])->startOfDay()
            : $endDate->copy()->subDays(29)->startOfDay();

        if ($startDate->greaterThan($endDate)) {
            [$startDate, $endDate] = [$endDate->copy()->startOfDay(), $startDate->copy()->endOfDay()];
        }

        return [$startDate, $endDate];
    }

    private function previousRange(Carbon $startDate, Carbon $endDate): array
    {
        $days = $startDate->diffInDays($endDate) + 1;
        $previousEnd = $startDate->copy()->subDay()->endOfDay();
        $previousStart = $previousEnd->copy()->subDays($days - 1)->startOfDay();

        return [$previousStart, $previousEnd];
    }

    private function revenueSummary(Carbon $startDate, Carbon $endDate): array
    {
        $row = DB::table('payments')
            ->where('status', 'paid')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->selectRaw('COALESCE(SUM(amount), 0) as amount, COUNT(*) as orders')
            ->first();

        return [
            'amount' => round((float) ($row->amount ?? 0), 2),
            'orders' => (int) ($row->orders ?? 0),
        ];
    }

    private function bookingSummary(Carbon $startDate, Carbon $endDate): array
    {
        $rows = DB::table('bookings')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $total = (int) $rows->sum();
        $confirmed = (int) ($rows['confirmed'] ?? 0);

        return [
            'total' => $total,
            'pending' => (int) ($rows['pending'] ?? 0),
            'confirmed' => $confirmed,
            'cancelled' => (int) ($rows['cancelled'] ?? 0),
            'expired' => (int) ($rows['expired'] ?? 0),
            'refunded' => (int) ($rows['refunded'] ?? 0),
            'conversion_rate' => $total > 0 ? round($confirmed / $total * 100, 2) : 0,
        ];
    }

    private function ticketSummary(Carbon $startDate, Carbon $endDate): array
    {
        $sold = DB::table('booking_items')
            ->join('bookings', 'booking_items.booking_id', '=', 'bookings.id')
            ->where('bookings.status', 'confirmed')
            ->whereBetween(DB::raw('COALESCE(bookings.confirmed_at, bookings.created_at)'), [$startDate, $endDate])
            ->count('booking_items.id');

        return ['sold' => (int) $sold];
    }

    private function occupancySummary(Carbon $startDate, Carbon $endDate, int $soldSeats): array
    {
        $availableSeats = DB::table('showtimes')
            ->join('rooms', 'showtimes.room_id', '=', 'rooms.id')
            ->join('seats', 'rooms.id', '=', 'seats.room_id')
            ->whereDate('showtimes.show_date', '>=', $startDate->toDateString())
            ->whereDate('showtimes.show_date', '<=', $endDate->toDateString())
            ->where('showtimes.status', '<>', 'cancelled')
            ->count('seats.id');

        return [
            'sold_seats' => $soldSeats,
            'available_seats' => (int) $availableSeats,
            'rate' => $availableSeats > 0 ? round($soldSeats / $availableSeats * 100, 2) : 0,
        ];
    }

    private function revenueSeries(Carbon $startDate, Carbon $endDate, string $granularity): array
    {
        $rows = DB::table('payments')
            ->where('status', 'paid')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->selectRaw($this->periodExpression('paid_at', $granularity) . ' as period, COALESCE(SUM(amount), 0) as value')
            ->groupBy('period')
            ->orderBy('period')
            ->pluck('value', 'period');

        return $this->fillSeries($startDate, $endDate, $granularity, $rows->all(), 'revenue');
    }

    private function ticketSeries(Carbon $startDate, Carbon $endDate, string $granularity): array
    {
        $periodColumn = 'COALESCE(bookings.confirmed_at, bookings.created_at)';
        $rows = DB::table('booking_items')
            ->join('bookings', 'booking_items.booking_id', '=', 'bookings.id')
            ->where('bookings.status', 'confirmed')
            ->whereBetween(DB::raw($periodColumn), [$startDate, $endDate])
            ->selectRaw($this->periodExpression($periodColumn, $granularity) . ' as period, COUNT(booking_items.id) as value')
            ->groupBy('period')
            ->orderBy('period')
            ->pluck('value', 'period');

        return $this->fillSeries($startDate, $endDate, $granularity, $rows->all(), 'tickets');
    }

    private function topMovies(Carbon $startDate, Carbon $endDate): array
    {
        return DB::table('booking_items')
            ->join('bookings', 'booking_items.booking_id', '=', 'bookings.id')
            ->join('showtimes', 'bookings.showtime_id', '=', 'showtimes.id')
            ->join('movies', 'showtimes.movie_id', '=', 'movies.id')
            ->where('bookings.status', 'confirmed')
            ->whereBetween(DB::raw('COALESCE(bookings.confirmed_at, bookings.created_at)'), [$startDate, $endDate])
            ->selectRaw('movies.id, movies.title, COUNT(booking_items.id) as tickets, COALESCE(SUM(booking_items.price), 0) as ticket_revenue')
            ->groupBy('movies.id', 'movies.title')
            ->orderByDesc('tickets')
            ->limit(5)
            ->get()
            ->map(fn ($row) => [
                'id' => $row->id,
                'title' => $row->title,
                'tickets' => (int) $row->tickets,
                'ticket_revenue' => round((float) $row->ticket_revenue, 2),
            ])
            ->all();
    }

    private function topCinemas(Carbon $startDate, Carbon $endDate): array
    {
        return DB::table('bookings')
            ->join('showtimes', 'bookings.showtime_id', '=', 'showtimes.id')
            ->join('rooms', 'showtimes.room_id', '=', 'rooms.id')
            ->join('cinemas', 'rooms.cinema_id', '=', 'cinemas.id')
            ->leftJoin('payments', function ($join) {
                $join->on('payments.booking_id', '=', 'bookings.id')->where('payments.status', '=', 'paid');
            })
            ->where('bookings.status', 'confirmed')
            ->whereBetween(DB::raw('COALESCE(bookings.confirmed_at, bookings.created_at)'), [$startDate, $endDate])
            ->selectRaw('cinemas.id, cinemas.name, COUNT(DISTINCT bookings.id) as bookings, COALESCE(SUM(payments.amount), 0) as revenue')
            ->groupBy('cinemas.id', 'cinemas.name')
            ->orderByDesc('revenue')
            ->limit(5)
            ->get()
            ->map(fn ($row) => [
                'id' => $row->id,
                'name' => $row->name,
                'bookings' => (int) $row->bookings,
                'revenue' => round((float) $row->revenue, 2),
            ])
            ->all();
    }

    private function paymentMethods(Carbon $startDate, Carbon $endDate): array
    {
        return DB::table('payments')
            ->where('status', 'paid')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->selectRaw('provider, COUNT(*) as orders, COALESCE(SUM(amount), 0) as revenue')
            ->groupBy('provider')
            ->orderByDesc('revenue')
            ->get()
            ->map(fn ($row) => [
                'provider' => $row->provider,
                'orders' => (int) $row->orders,
                'revenue' => round((float) $row->revenue, 2),
            ])
            ->all();
    }

    private function periodExpression(string $column, string $granularity): string
    {
        $driver = DB::connection()->getDriverName();

        if ($granularity === 'month') {
            return $driver === 'sqlite'
                ? "strftime('%Y-%m', {$column})"
                : "DATE_FORMAT({$column}, '%Y-%m')";
        }

        return $driver === 'sqlite'
            ? "date({$column})"
            : "DATE({$column})";
    }

    private function fillSeries(Carbon $startDate, Carbon $endDate, string $granularity, array $rows, string $valueKey): array
    {
        $series = [];

        if ($granularity === 'month') {
            $cursor = $startDate->copy()->startOfMonth();
            $last = $endDate->copy()->startOfMonth();

            while ($cursor->lessThanOrEqualTo($last)) {
                $period = $cursor->format('Y-m');
                $series[] = ['period' => $period, $valueKey => round((float) ($rows[$period] ?? 0), 2)];
                $cursor->addMonth();
            }

            return $series;
        }

        foreach (CarbonPeriod::create($startDate->copy()->startOfDay(), $endDate->copy()->startOfDay()) as $date) {
            $period = $date->format('Y-m-d');
            $series[] = ['period' => $period, $valueKey => round((float) ($rows[$period] ?? 0), 2)];
        }

        return $series;
    }

    private function percentChange(float|int $current, float|int $previous): float
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }

        return round(($current - $previous) / $previous * 100, 2);
    }
}
