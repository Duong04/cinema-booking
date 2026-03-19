<?php 
namespace App\Services;

use App\Repositories\City\CityRepositoryInterface;

class CityService {
    private $cityRepository;

    public function __construct(CityRepositoryInterface $cityRepository) {
        $this->cityRepository = $cityRepository;
    }

    public function paginate($limit, $q) {
        $cities = $this->cityRepository->paginate($limit, $q);

        return $cities;
    }

    public function create($data) {
        $city = $this->cityRepository->create($data);

        return $city;
    }

    public function find($id) {
        $city = $this->cityRepository->find($id);

        return $city;
    }

    public function update($id, $data) {
        $city = $this->cityRepository->update($id, $data);

        return $city;
    }

    public function delete($id) {
        return $this->cityRepository->delete($id);
    }

}