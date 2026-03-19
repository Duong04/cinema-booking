<?php

namespace App\Http\Controllers\Apis\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\QueryRequest;
use App\Http\Requests\CinemaChainRequest;
use Illuminate\Http\Request;
use App\Services\CinemaChainService;
use App\Traits\ResponseHelper;
use OpenApi\Attributes as OA;

class CinemaChainController extends Controller
{
    use ResponseHelper;

    private $cinemaChainService;

    public function __construct(CinemaChainService $cinemaChainService)
    {
        $this->cinemaChainService = $cinemaChainService;
    }

    public function paginate(QueryRequest $requets)
    {
        $query = $requets->validated();
        $limit = $query['limit'] ?? 15;
        $q = $query['q'] ?? null;

        $cinemaChains = $this->cinemaChainService->paginate($limit, $q);

        return $this->successList($cinemaChains->items(), [
            'total' => $cinemaChains->total(),
            'per_page' => $cinemaChains->perPage(),
            'current_page' => $cinemaChains->currentPage(),
            'last_page' => $cinemaChains->lastPage(),
            'current_page_url' => $cinemaChains->url($cinemaChains->currentPage()),
            'first_page_url' => $cinemaChains->url(1),
            'last_page_url' => $cinemaChains->url($cinemaChains->lastPage()),
            'next_page_url' => $cinemaChains->nextPageUrl(),
            'prev_page_url' => $cinemaChains->previousPageUrl(),
        ]);
    }

    public function create(CinemaChainRequest $request)
    {
        $data = $request->validated();

        $cinemaChain = $this->cinemaChainService->create($data);

        return $this->success($cinemaChain, 'Tạo chuỗi rạp phim thành công!', 201);
    }

    public function show($id)
    {
        $cinemaChain = $this->cinemaChainService->find($id);

        return $this->success($cinemaChain);
    }

    public function update(CinemaChainRequest $request, $id)
    {
        $data = $request->validated();

        $cinemaChain = $this->cinemaChainService->update($id, $data);

        return $this->success($cinemaChain, 'Cập nhật chuỗi rạp phim thành công!', 200);
    }

    public function delete($id)
    {
        $this->cinemaChainService->delete($id);

        return $this->success(null, 'Xóa chuỗi rạp phim thành công!');
    }
}
