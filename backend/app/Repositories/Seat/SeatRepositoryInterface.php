<?php 
namespace App\Repositories\Seat;

use App\Repositories\Base\BaseRepositoryInterface;
interface SeatRepositoryInterface extends BaseRepositoryInterface
{
    public function paginate($limit = 15, $q);
    public function getSeatByRoom($roomId);
    public function insert($data);
    public function checkRoomId($roomId);
}