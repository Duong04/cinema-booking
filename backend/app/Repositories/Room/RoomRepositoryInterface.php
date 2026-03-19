<?php 
namespace App\Repositories\Room;

use App\Repositories\Base\BaseRepositoryInterface;
interface RoomRepositoryInterface extends BaseRepositoryInterface
{
    public function paginate($limit = 15, $q);
}