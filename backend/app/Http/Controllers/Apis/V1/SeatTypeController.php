<?php

namespace App\Http\Controllers\Apis\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\QueryRequest;
use App\Http\Requests\SeatTypeRequest;
use App\Services\SeatTypeService;
use App\Traits\ResponseHelper;
use Illuminate\Http\Request;

class SeatTypeController extends Controller
{
    use ResponseHelper;

    private $seatTypeService;

    public function __construct(SeatTypeService $seatTypeService)
    {
        $this->seatTypeService = $seatTypeService;
    }

    public function paginate(QueryRequest $requets)
    {
        $query = $requets->validated();
        $limit = $query['limit'] ?? 15;
        $q = $query['q'] ?? null;

        $rooms = $this->seatTypeService->paginate($limit, $q);

        return $this->successList($rooms->items(), [
            'total' => $rooms->total(),
            'per_page' => $rooms->perPage(),
            'current_page' => $rooms->currentPage(),
            'last_page' => $rooms->lastPage(),
            'current_page_url' => $rooms->url($rooms->currentPage()),
            'first_page_url' => $rooms->url(1),
            'last_page_url' => $rooms->url($rooms->lastPage()),
            'next_page_url' => $rooms->nextPageUrl(),
            'prev_page_url' => $rooms->previousPageUrl(),
        ]);
    }

    public function create(SeatTypeRequest $request)
    {
        $data = $request->validated();

        $room = $this->seatTypeService->create($data);

        return $this->success($room, 'Tạo loại ghế thành công!', 201);
    }

    public function show($id)
    {
        $room = $this->seatTypeService->find($id);

        return $this->success($room);
    }

    public function update(SeatTypeRequest $request, $id)
    {
        $data = $request->validated();

        $room = $this->seatTypeService->update($id, $data);

        return $this->success($room, 'Cập nhật loại ghế thành công!', 200);
    }

    public function delete($id)
    {
        $this->seatTypeService->delete($id);

        return $this->success(null, 'Xóa loại ghế thành công!');
    }
}
