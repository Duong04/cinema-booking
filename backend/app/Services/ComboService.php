<?php 
namespace App\Services;

use App\Repositories\Combo\ComboRepositoryInterface;

class ComboService
{
    private $comboRepository;

    public function __construct(ComboRepositoryInterface $comboRepository)
    {
        $this->comboRepository = $comboRepository;
    }

    public function paginate($limit, $q, $cinema) {
        $combos = $this->comboRepository->paginate($limit, $q, $cinema);

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