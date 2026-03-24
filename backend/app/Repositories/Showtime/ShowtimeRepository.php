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

}