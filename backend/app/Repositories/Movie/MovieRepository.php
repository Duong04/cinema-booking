<?php
namespace App\Repositories\Movie;

use App\Repositories\Base\BaseRepository;
use App\Models\Movie;
use App\Repositories\Movie\MovieRepositoryInterface;

class MovieRepository extends BaseRepository implements MovieRepositoryInterface
{
    public function __construct(Movie $model)
    {
        $this->model = $model;
    }

    public function paginate($limit = 15, $q, $status)
    {
        $movies = $this->model->with('genres:id,name')
            ->when($q, fn($query) => $query->where('title', 'like', "%$q%"))
            ->when($status, fn($query) => $query->where('status', $status));

        return $movies->orderByDesc('created_at')->paginate($limit);
    }

    public function getPublicMovies($limit = 15, $q = null, $status = null, $sort = null, $period = null, $genreId = null)
    {
        $movies = $this->model->with('genres:id,name')
            ->whereIn('status', ['now_showing', 'coming_soon'])
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($genreId, fn ($query) => $query->whereHas('genres', fn ($genreQuery) => $genreQuery->where('genres.id', $genreId)))
            ->when($q, fn ($query) => $query->where('title', 'like', "%$q%"));

        if ($sort === 'best_selling') {
            $period = $period ?? '30d';
            $periodDays = match ($period) {
                '7d' => 7,
                '30d' => 30,
                default => null,
            };

            $movies->select('movies.*')
                ->selectSub(function ($query) use ($periodDays) {
                    $query->from('booking_items')
                        ->join('bookings', 'booking_items.booking_id', '=', 'bookings.id')
                        ->join('showtimes', 'bookings.showtime_id', '=', 'showtimes.id')
                        ->whereColumn('showtimes.movie_id', 'movies.id')
                        ->where('bookings.status', 'confirmed')
                        ->when($periodDays, fn ($query) => $query->where('bookings.confirmed_at', '>=', now()->subDays($periodDays)))
                        ->selectRaw('COUNT(booking_items.id)');
                }, 'sold_tickets_count')
                ->orderByDesc('sold_tickets_count')
                ->orderByDesc('movies.created_at');

            return $movies->paginate($limit);
        }

        if ($sort === 'top_rated') {
            return $movies->orderByDesc('rating_score')
                ->orderByDesc('rating_count')
                ->orderByDesc('created_at')
                ->paginate($limit);
        }

        if ($sort === 'release_date_desc') {
            return $movies->orderByDesc('release_date')
                ->orderByDesc('created_at')
                ->paginate($limit);
        }

        if ($sort === 'duration_desc') {
            return $movies->orderByDesc('duration_minutes')
                ->orderByDesc('created_at')
                ->paginate($limit);
        }

        return $movies->orderByDesc('created_at')->paginate($limit);
    }

    public function findBySlug($slug)
    {
        return $this->model->with('genres:id,name')
            ->whereIn('status', ['now_showing', 'coming_soon'])
            ->where('slug', $slug)
            ->firstOrFail();
    }

}
