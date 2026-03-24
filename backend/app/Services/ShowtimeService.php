<?php 
namespace App\Services;

use App\Repositories\Showtime\ShowtimeRepositoryInterface;
use App\Repositories\Movie\MovieRepositoryInterface;
use App\Repositories\ShowtimePrice\ShowtimePriceRepositoryInterface;
use Carbon\Carbon;
use DB;
use Str;

class ShowtimeService {
    private $showtimeRepository;
    private $showtimePriceRepository;
    private $movieRepository;

    public function __construct(ShowtimeRepositoryInterface $showtimeRepository, ShowtimePriceRepositoryInterface $showtimePriceRepository, MovieRepositoryInterface $movieRepository) {
        $this->showtimeRepository = $showtimeRepository;
        $this->showtimePriceRepository = $showtimePriceRepository;
        $this->movieRepository = $movieRepository;
    }

    public function paginate($limit, $movieId, $roomId, $showDate) {
        $showtimes = $this->showtimeRepository->paginate($limit, $movieId, $roomId, $showDate);

        return $showtimes;
    }

    public function create($data) {
        return DB::transaction(function () use ($data) {
            $movie    = $this->movieRepository->find($data['movie_id']);
            $endTime  = Carbon::parse($data['start_time'])->addMinutes($movie->duration_minutes);

            $showtime = $this->showtimeRepository->create([
                ...$data,
                'end_time' => $endTime,
                'status'   => 'scheduled',
            ]);

            $prices = array_map(fn($p) => [
                'id'           => Str::uuid7(),
                'showtime_id'  => $showtime->id,
                'seat_type_id' => $p['seat_type_id'],
                'price'        => $p['price'],
            ], $data['prices']);

            $this->showtimePriceRepository->insert($prices);

            return $showtime->load('prices.seatType');
        });
    }

    public function find($id) {
        $showtime = $this->showtimeRepository->find($id, ['*'], ['movie', 'room.cinema', 'prices.seatType']);

        return $showtime;
    }

    public function update($id, $data) {
        return DB::transaction(function () use ($id, $data) {
            $showtime = $this->showtimeRepository->update($id, $data);

            if (isset($data['prices'])) {
                $this->showtimePriceRepository->deleteByShowtime($id);

                $prices = array_map(fn($p) => [
                    'id'           => Str::uuid7(),
                    'showtime_id'  => $id,
                    'seat_type_id' => $p['seat_type_id'],
                    'price'        => $p['price'],
                ], $data['prices']);

                $this->showtimePriceRepository->insert($prices);
            }

            return $showtime->load('prices.seatType');
        });
    }

    public function delete($id) {
        return $this->showtimeRepository->delete($id);
    }

}