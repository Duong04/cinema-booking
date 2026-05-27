<?php 
namespace App\Services;

use App\Repositories\Cinema\CinemaRepositoryInterface;

class CinemaService {
    public function __construct(
        private CinemaRepositoryInterface $cinemaRepository
    ) {
    }

    public function paginate($limit, $q, $cityId, $cinemaChainId) {
        $cinemas = $this->cinemaRepository->paginate($limit, $q, $cityId, $cinemaChainId);

        return $cinemas;
    }

    public function create($data) {
        $cinema = $this->cinemaRepository->create($data);

        return $cinema;
    }

    public function find($id) {
        $cinema = $this->cinemaRepository->find($id);

        return $cinema;
    }

    public function update($id, $data) {
        $cinema = $this->cinemaRepository->update($id, $data);

        return $cinema;
    }

    public function delete($id) {
        return $this->cinemaRepository->delete($id);
    }

}