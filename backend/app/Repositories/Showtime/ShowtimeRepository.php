<?php 
namespace App\Repositories\Showtime;

use App\Repositories\Base\BaseRepository;
use App\Models\Showtime;
use App\Repositories\Showtime\ShowtimeRepositoryInterface;

class ShowtimeRepository extends BaseRepository implements ShowtimeRepositoryInterface
{
    public function __construct(Showtime $model)
    {
        $this->model = $model;
    }

    public function paginate($limit = 15, $movieId, $roomId, $showDate) {
        $showtimes = $this->model->with(['movie', 'room', 'prices.seatType'])
            ->when($movieId,  fn($q) => $q->where('movie_id', $movieId))
            ->when($roomId,   fn($q) => $q->where('room_id', $roomId))
            ->when($showDate, fn($q) => $q->where('show_date', $showDate))
            ->orderBy('start_time');

        return $showtimes->paginate($limit);
    }

    public function getPublicShowtimes($limit = 15, $movieId = null, $cinemaId = null, $cityId = null, $cinemaChainId = null, $showDate = null, $fromDate = null, $toDate = null, $status = null)
    {
        $publicStatuses = ['scheduled', 'ongoing'];

        $showtimes = $this->model->with([
                'movie.genres:id,name',
                'room.cinema.city:id,name',
                'room.cinema.cinemaChain:id,name,logo',
                'prices.seatType',
            ])
            ->whereHas('movie', fn ($query) => $query->whereIn('status', ['now_showing', 'coming_soon']))
            ->whereIn('status', $publicStatuses)
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($movieId, fn ($query) => $query->where('movie_id', $movieId))
            ->when($cinemaId, fn ($query) => $query->whereHas('room', fn ($roomQuery) => $roomQuery->where('cinema_id', $cinemaId)))
            ->when($cityId, fn ($query) => $query->whereHas('room.cinema', fn ($cinemaQuery) => $cinemaQuery->where('city_id', $cityId)))
            ->when($cinemaChainId, fn ($query) => $query->whereHas('room.cinema', fn ($cinemaQuery) => $cinemaQuery->where('cinema_chain_id', $cinemaChainId)))
            ->when($showDate, fn ($query) => $query->where('show_date', $showDate))
            ->when(! $showDate, fn ($query) => $query->where('show_date', '>=', now()->toDateString()))
            ->when($fromDate, fn ($query) => $query->where('show_date', '>=', $fromDate))
            ->when($toDate, fn ($query) => $query->where('show_date', '<=', $toDate))
            ->orderBy('show_date')
            ->orderBy('start_time');

        return $showtimes->paginate($limit);
    }

    public function findPublic($id)
    {
        return $this->model->with([
                'movie.genres:id,name',
                'room.cinema.city:id,name',
                'room.cinema.cinemaChain:id,name,logo',
                'prices.seatType',
            ])
            ->whereHas('movie', fn ($query) => $query->whereIn('status', ['now_showing', 'coming_soon']))
            ->whereIn('status', ['scheduled', 'ongoing'])
            ->findOrFail($id);
    }

}
