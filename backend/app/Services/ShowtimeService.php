<?php 
namespace App\Services;

use App\Repositories\Showtime\ShowtimeRepositoryInterface;
use App\Repositories\Movie\MovieRepositoryInterface;
use App\Repositories\ShowtimePrice\ShowtimePriceRepositoryInterface;
use App\Models\BookingItem;
use App\Models\Seat;
use App\Models\SeatHold;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ShowtimeService {
    private $showtimeRepository;
    private $showtimePriceRepository;
    private $movieRepository;

    public function __construct(ShowtimeRepositoryInterface $showtimeRepository, ShowtimePriceRepositoryInterface $showtimePriceRepository, MovieRepositoryInterface $movieRepository) {
        $this->showtimeRepository = $showtimeRepository;
        $this->showtimePriceRepository = $showtimePriceRepository;
        $this->movieRepository = $movieRepository;
    }

    public function paginate($limit, $movieId, $roomId, $showDate, $status) {
        $showtimes = $this->showtimeRepository->paginate($limit, $movieId, $roomId, $showDate, $status);

        return $showtimes;
    }

    public function getPublicShowtimes($limit, $movieId, $cinemaId, $cityId, $cinemaChainId, $showDate, $fromDate, $toDate, $status) {
        return $this->showtimeRepository->getPublicShowtimes($limit, $movieId, $cinemaId, $cityId, $cinemaChainId, $showDate, $fromDate, $toDate, $status);
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

    public function seatOverview($id, bool $includePrivateDetails = true) {
        $showtime = $this->showtimeRepository->find($id, ['*'], ['movie', 'room.cinema']);

        $seats = Seat::query()
            ->with('seatType')
            ->where('room_id', $showtime->room_id)
            ->orderBy('row_label')
            ->orderBy('seat_number')
            ->get();

        $bookingItems = BookingItem::query()
            ->with(['booking:id,user_id,showtime_id,booking_code,status,total_amount', 'booking.user:id,name,email'])
            ->whereIn('seat_id', $seats->pluck('id'))
            ->whereHas(
                'booking',
                fn($query) => $query
                    ->where('showtime_id', $id)
                    ->whereIn('status', ['pending', 'confirmed'])
            )
            ->get()
            ->keyBy('seat_id');

        $seatHolds = SeatHold::query()
            ->with('user:id,name,email')
            ->where('showtime_id', $id)
            ->where('expired_at', '>', now())
            ->get()
            ->keyBy('seat_id');

        $items = $seats->map(function ($seat) use ($bookingItems, $seatHolds, $includePrivateDetails) {
            $bookingItem = $bookingItems->get($seat->id);
            $seatHold = $seatHolds->get($seat->id);

            $status = 'available';
            if ($bookingItem) {
                $status = 'booked';
            } elseif ($seatHold) {
                $status = 'held';
            }

            return [
                'id' => $seat->id,
                'room_id' => $seat->room_id,
                'seat_type_id' => $seat->seat_type_id,
                'row_label' => $seat->row_label,
                'seat_number' => $seat->seat_number,
                'label' => $seat->row_label . $seat->seat_number,
                'seat_type' => $seat->seatType,
                'status' => $status,
                'booking' => $includePrivateDetails && $bookingItem ? [
                    'id' => $bookingItem->booking_id,
                    'booking_code' => $bookingItem->booking?->booking_code,
                    'status' => $bookingItem->booking?->status,
                    'user' => $bookingItem->booking?->user,
                ] : null,
                'hold' => $includePrivateDetails && $seatHold ? [
                    'id' => $seatHold->id,
                    'expired_at' => $seatHold->expired_at,
                    'user' => $seatHold->user,
                ] : null,
            ];
        });

        $summary = [
            'total' => $items->count(),
            'booked' => $items->where('status', 'booked')->count(),
            'held' => $items->where('status', 'held')->count(),
            'available' => $items->where('status', 'available')->count(),
        ];

        return [
            'showtime' => $showtime,
            'summary' => $summary,
            'seats' => $items->values(),
        ];
    }

    public function findPublic($id) {
        return $this->showtimeRepository->findPublic($id);
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
