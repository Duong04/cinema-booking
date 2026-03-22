<?php 
namespace App\Services;

use App\Repositories\SeatType\SeatTypeRepositoryInterface;

class SeatTypeService {
    private $seatTypeRepository;

    public function __construct(SeatTypeRepositoryInterface $seatTypeRepository) {
        $this->seatTypeRepository = $seatTypeRepository;
    }

    public function paginate($limit, $q) {
        $seatTypes = $this->seatTypeRepository->paginate($limit, $q);

        return $seatTypes;
    }

    public function create($data) {
        $seatType = $this->seatTypeRepository->create($data);

        return $seatType;
    }

    public function find($id) {
        $seatType = $this->seatTypeRepository->find($id);

        return $seatType;
    }

    public function update($id, $data) {
        $seatType = $this->seatTypeRepository->update($id, $data);

        return $seatType;
    }

    public function delete($id) {
        return $this->seatTypeRepository->delete($id);
    }

}