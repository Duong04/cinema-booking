<?php 
namespace App\Services;

use App\Repositories\Combo\ComboRepositoryInterface;

class ComboService
{
    public function __construct(
        private ComboRepositoryInterface $comboRepository
    ) {
    }

    public function paginate($limit, $q, $cinema, $status) {
        $combos = $this->comboRepository->paginate($limit, $q, $cinema, $status);

        return $combos;
    }

    public function create($data) {
        $combo = $this->comboRepository->create($data);

        return $combo;
    }

    public function find($id) {
        $combo = $this->comboRepository->find($id);

        return $combo;
    }

    public function update($id, $data) {
        $combo = $this->comboRepository->update($id, $data);

        return $combo;
    }

    public function delete($id) {
        return $this->comboRepository->delete($id);
    }
}