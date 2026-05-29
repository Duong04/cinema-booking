<?php 
namespace App\Services;

use App\Repositories\Room\RoomRepositoryInterface;

class RoomService {
    public function __construct(
        private RoomRepositoryInterface $roomRepository
    ) {
    }

    public function paginate($limit, $q) {
        $rooms = $this->roomRepository->paginate($limit, $q);

        return $rooms;
    }

    public function create($data) {
        $room = $this->roomRepository->create($data);

        return $room;
    }

    public function find($id) {
        $room = $this->roomRepository->find($id);

        return $room;
    }

    public function update($id, $data) {
        $room = $this->roomRepository->update($id, $data);

        return $room;
    }

    public function delete($id) {
        return $this->roomRepository->delete($id);
    }

}