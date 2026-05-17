<?php 
namespace App\Repositories\ShowtimePrice;

use App\Repositories\Base\BaseRepository;
use App\Models\ShowtimePrice;
use App\Repositories\ShowtimePrice\ShowtimePriceRepositoryInterface;

class ShowtimePriceRepository extends BaseRepository implements ShowtimePriceRepositoryInterface
{
    public function __construct(ShowtimePrice $model)
    {
        $this->model = $model;
    }

    public function paginate($limit = 15, $q) {
        $showtimePrices = $this->model->when($q, fn ($query) => $query->where('name', 'like', "%$q%"));

        return $showtimePrices->orderByDesc('created_at')->paginate($limit);
    }

    public function insert($data) {
        return $this->model->insert($data);
    }

    public function deleteByShowtime($showtimeId) {
        return $this->model->where('showtime_id', $showtimeId)->delete();
    }

    public function getKeyBySeatTypeId(string $showtimeId) {
        return $this->model->where('showtime_id', $showtimeId)
            ->get()
            ->keyBy('seat_type_id');
    }
}