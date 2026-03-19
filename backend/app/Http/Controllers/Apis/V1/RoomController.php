<?php

namespace App\Http\Controllers\Apis\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\QueryRequest;
use App\Http\Requests\RoomRequest;
use Illuminate\Http\Request;
use App\Services\RoomService;
use App\Traits\ResponseHelper;
use OpenApi\Attributes as OA;

class RoomController extends Controller
{
    use ResponseHelper;

    private $roomService;

    public function __construct(RoomService $roomService)
    {
        $this->roomService = $roomService;
    }

    public function paginate(QueryRequest $requets)
    {
        $query = $requets->validated();
        $limit = $query['limit'] ?? 15;
        $q = $query['q'] ?? null;

        $rooms = $this->roomService->paginate($limit, $q);

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

    public function create(RoomRequest $request)
    {
        $data = $request->validated();

        $room = $this->roomService->create($data);

        return $this->success($room, 'Tạo phòng thành công!', 201);
    }

    public function show($id)
    {
        $room = $this->roomService->find($id);

        return $this->success($room);
    }

    public function update(RoomRequest $request, $id)
    {
        $data = $request->validated();

        $room = $this->roomService->update($id, $data);

        return $this->success($room, 'Cập nhật phòng thành công!', 200);
    }

    public function delete($id)
    {
        $this->roomService->delete($id);

        return $this->success(null, 'Xóa phòng thành công!');
    }
}
