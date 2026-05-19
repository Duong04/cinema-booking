<?php 
namespace App\Services;

use App\Repositories\Movie\MovieRepositoryInterface;
use App\Repositories\MovieGenre\MovieGenreRepositoryInterface;
use Illuminate\Support\Str;

class MovieService {
    private $movieRepository;
    private $movieGenreRepository;

    public function __construct(MovieRepositoryInterface $movieRepository, MovieGenreRepositoryInterface $movieGenreRepository) {
        $this->movieRepository = $movieRepository;
        $this->movieGenreRepository = $movieGenreRepository;
    }

    public function paginate($limit, $q, $status) {
        $movies = $this->movieRepository->paginate($limit, $q, $status);

        return $movies;
    }

    public function getPublicMovies($limit, $q, $status, $sort, $period, $genreId) {
        return $this->movieRepository->getPublicMovies($limit, $q, $status, $sort, $period, $genreId);
    }

    public function findPublicBySlug($slug) {
        return $this->movieRepository->findBySlug($slug);
    }

    public function create($data) {
        $data['slug'] = Str::slug($data['title']);
        $movie = $this->movieRepository->create($data);

        $movieGenres = [];
        foreach ($data['genres'] as $genre) {
            $movieGenres[] = [
                'genre_id' => $genre,
                'movie_id' => $movie->id
            ];
        }

        $this->movieGenreRepository->insert($movieGenres);
        return $movie;
    }

    public function find($id) {
        $movie = $this->movieRepository->find($id, ['*'], ['genres:id,name']);

        return $movie;
    }

    public function update($id, $data) {
        $movie = $this->movieRepository->update($id, $data);

        if (isset($data['genres'])) {
            $this->movieGenreRepository->deleteByMovie($id);
            $movieGenres = [];
            foreach ($data['genres'] as $genre) {
                $movieGenres[] = [
                    'genre_id' => $genre,
                    'movie_id' => $id
                ];
            } 
            $this->movieGenreRepository->insert($movieGenres);
        }
        
        return $movie;
    }

    public function delete($id) {
        return $this->movieRepository->delete($id);
    }

}
