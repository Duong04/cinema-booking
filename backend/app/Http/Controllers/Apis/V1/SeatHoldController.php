<?php

namespace App\Http\Controllers\Apis\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\SeatHoldService;
use App\Traits\ApiResponse;
use App\Http\Requests\SeatHoldRequest;

class SeatHoldController extends Controller
{
    use ApiResponse;

    private $seatHoldService;

    public function __construct(SeatHoldService $seatHoldService)
    {
        $this->seatHoldService = $seatHoldService;
    }

    public function getListShowtime($showtimeId)
    {
        $data = $this->seatHoldService->getListShowtime($showtimeId);

        return $this->success($data);
    }

    public function hold(SeatHoldRequest $request)
    {
        $data = $request->validated();
        $this->seatHoldService->hold($data);
        
        return $this->success(null, 'Giữ ghế thành công.');
    }

    public function release(SeatHoldRequest $request)
    {
        $data = $request->validated();
        $this->seatHoldService->release($data);

        return $this->success(null, 'Hủy giữ ghế thành công.');
    }

}
