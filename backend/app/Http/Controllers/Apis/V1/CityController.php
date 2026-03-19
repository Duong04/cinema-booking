<?php

namespace App\Http\Controllers\Apis\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\QueryRequest;
use App\Http\Requests\CityRequest;
use Illuminate\Http\Request;
use App\Services\CityService;
use App\Traits\ResponseHelper;
use OpenApi\Attributes as OA;

class CityController extends Controller
{
    use ResponseHelper;

    private $cityService;

    public function __construct(CityService $cityService)
    {
        $this->cityService = $cityService;
    }

    public function paginate(QueryRequest $requets)
    {
        $query = $requets->validated();
        $limit = $query['limit'] ?? 15;
        $q = $query['q'] ?? null;

        $cities = $this->cityService->paginate($limit, $q);

        return $this->successList($cities->items(), [
            'total' => $cities->total(),
            'per_page' => $cities->perPage(),
            'current_page' => $cities->currentPage(),
            'last_page' => $cities->lastPage(),
            'current_page_url' => $cities->url($cities->currentPage()),
            'first_page_url' => $cities->url(1),
            'last_page_url' => $cities->url($cities->lastPage()),
            'next_page_url' => $cities->nextPageUrl(),
            'prev_page_url' => $cities->previousPageUrl(),
        ]);
    }

    public function create(CityRequest $request)
    {
        $data = $request->validated();

        $city = $this->cityService->create($data);

        return $this->success($city, 'Tạo thành phố thành công!', 201);
    }

    public function show($id)
    {
        $city = $this->cityService->find($id);

        return $this->success($city);
    }

    public function update(CityRequest $request, $id)
    {
        $data = $request->validated();

        $city = $this->cityService->update($id, $data);

        return $this->success($city, 'Cập nhật thành phố thành công!', 200);
    }

    public function delete($id)
    {
        $this->cityService->delete($id);

        return $this->success(null, 'Xóa thành phố thành công!');
    }
}
