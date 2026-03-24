<?php 
namespace App\Repositories\Showtime;

use App\Repositories\Base\BaseRepositoryInterface;
interface ShowtimeRepositoryInterface extends BaseRepositoryInterface
{
    public function paginate($limit = 15, $movieId, $roomId, $showDate);
}