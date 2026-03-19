<?php 
namespace App\Services;

use App\Repositories\CinemaChain\CinemaChainRepositoryInterface;

class CinemaChainService {
    private $cinemaChainRepository;

    public function __construct(CinemaChainRepositoryInterface $cinemaChainRepository) {
        $this->cinemaChainRepository = $cinemaChainRepository;
    }

    public function paginate($limit, $q) {
        $cinemaChains = $this->cinemaChainRepository->paginate($limit, $q);

        return $cinemaChains;
    }

    public function create($data) {
        $cinemaChain = $this->cinemaChainRepository->create($data);

        return $cinemaChain;
    }

    public function find($id) {
        $cinemaChain = $this->cinemaChainRepository->find($id);

        return $cinemaChain;
    }

    public function update($id, $data) {
        $cinemaChain = $this->cinemaChainRepository->update($id, $data);

        return $cinemaChain;
    }

    public function delete($id) {
        return $this->cinemaChainRepository->delete($id);
    }

}