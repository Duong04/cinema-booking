<?php

namespace App\Http\Controllers\Apis\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\QueryRequest;
use App\Http\Requests\CinemaRequest;
use Illuminate\Http\Request;
use App\Services\CinemaService;
use App\Traits\ResponseHelper;
use OpenApi\Attributes as OA;

class CinemaController extends Controller
{
    use ResponseHelper;

    private $cinemaService;

    public function __construct(CinemaService $cinemaService)
    {
        $this->cinemaService = $cinemaService;
    }

    public function paginate(QueryRequest $requets)
    {
        $query = $requets->validated();
        $limit = $query['limit'] ?? 15;
        $q = $query['q'] ?? null;

        $cinemas = $this->cinemaService->paginate($limit, $q);

        return $this->successList($cinemas->items(), [
            'total' => $cinemas->total(),
            'per_page' => $cinemas->perPage(),
            'current_page' => $cinemas->currentPage(),
            'last_page' => $cinemas->lastPage(),
            'current_page_url' => $cinemas->url($cinemas->currentPage()),
            'first_page_url' => $cinemas->url(1),
            'last_page_url' => $cinemas->url($cinemas->lastPage()),
            'next_page_url' => $cinemas->nextPageUrl(),
            'prev_page_url' => $cinemas->previousPageUrl(),
        ]);
    }

    public function create(CinemaRequest $request)
    {
        $data = $request->validated();

        $cinema = $this->cinemaService->create($data);

        return $this->success($cinema, 'Tạo rạp phim thành công!', 201);
    }

    public function show($id)
    {
        $cinema = $this->cinemaService->find($id);

        return $this->success($cinema);
    }

    public function update(CinemaRequest $request, $id)
    {
        $data = $request->validated();

        $cinema = $this->cinemaService->update($id, $data);

        return $this->success($cinema, 'Cập nhật rạp phim thành công!', 200);
    }

    public function delete($id)
    {
        $this->cinemaService->delete($id);

        return $this->success(null, 'Xóa rạp phim thành công!');
    }
}
