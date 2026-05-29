<?php 
namespace App\Repositories\Showtime;

use App\Repositories\Base\BaseRepositoryInterface;
interface ShowtimeRepositoryInterface extends BaseRepositoryInterface
{
    public function paginate($limit = 15, $movieId, $roomId, $showDate, $status);
    public function getPublicShowtimes($limit = 15, $movieId = null, $cinemaId = null, $cityId = null, $cinemaChainId = null, $showDate = null, $fromDate = null, $toDate = null, $status = null);
    public function findPublic($id);
}
