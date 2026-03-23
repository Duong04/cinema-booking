<?php 
namespace App\Services;

use App\Repositories\Genre\GenreRepositoryInterface;

class GenreService {
    private $genreRepository;

    public function __construct(GenreRepositoryInterface $genreRepository) {
        $this->genreRepository = $genreRepository;
    }

    public function paginate($limit, $q) {
        $genres = $this->genreRepository->paginate($limit, $q);

        return $genres;
    }

    public function create($data) {
        $genre = $this->genreRepository->create($data);

        return $genre;
    }

    public function find($id) {
        $genre = $this->genreRepository->find($id);

        return $genre;
    }

    public function update($id, $data) {
        $genre = $this->genreRepository->update($id, $data);

        return $genre;
    }

    public function delete($id) {
        return $this->genreRepository->delete($id);
    }

}