<?php

namespace App\Http\Controllers\Apis\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\QueryRequest;
use App\Http\Requests\ShowtimeRequest;
use App\Services\ShowtimeService;
use App\Traits\ResponseHelper;
use Illuminate\Http\Request;

class ShowtimeController extends Controller
{
    use ResponseHelper;

    private $showtimeService;

    public function __construct(ShowtimeService $showtimeService)
    {
        $this->showtimeService = $showtimeService;
    }
    
    public function paginate(QueryRequest $requets)
    {
        $query = $requets->validated();
        $limit = $query['limit'] ?? 15;
        $movieId = $query['movie_id'] ?? null;
        $roomId = $query['room_id'] ?? null;
        $showDate = $query['show_date'] ?? null;

        $showtimes = $this->showtimeService->paginate($limit, $movieId, $roomId, $showDate);

        return $this->successList($showtimes->items(), [
            'total' => $showtimes->total(),
            'per_page' => $showtimes->perPage(),
            'current_page' => $showtimes->currentPage(),
            'last_page' => $showtimes->lastPage(),
            'current_page_url' => $showtimes->url($showtimes->currentPage()),
            'first_page_url' => $showtimes->url(1),
            'last_page_url' => $showtimes->url($showtimes->lastPage()),
            'next_page_url' => $showtimes->nextPageUrl(),
            'prev_page_url' => $showtimes->previousPageUrl(),
        ]);
    }
    
    public function create(ShowtimeRequest $request)
    {
        $data = $request->validated();

        $showtime = $this->showtimeService->create($data);

        return $this->success($showtime, 'Tạo thời gian chiếu thành công!', 201);
    }
    
    public function show($id)
    {
        $showtime = $this->showtimeService->find($id);

        return $this->success($showtime);
    }
    
    public function update(ShowtimeRequest $request, $id)
    {
        $data = $request->validated();

        $showtime = $this->showtimeService->update($id, $data);

        return $this->success($showtime, 'Cập nhật thời gian chiếu thành công!', 200);
    }
    
    public function delete($id)
    {
        $this->showtimeService->delete($id);

        return $this->success(null, 'Xóa thời gian chiếu thành công!');
    }
}
