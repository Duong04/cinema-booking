<?php

namespace App\Http\Controllers\Apis\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\SeatRequest;
use App\Services\SeatService;
use App\Traits\ResponseHelper;
use Illuminate\Http\Request;

class SeatController extends Controller
{
    use ResponseHelper;
    private $seatService;

    public function __construct(SeatService $seatService) {
        $this->seatService = $seatService;
    }

    public function getSeatByRoom($roomId) {
        $data = $this->seatService->getSeatByRoom($roomId);

        return $this->success($data);
    }

    public function create(SeatRequest $request, $roomId) {
        $data = $request->validated();

        $seats = $this->seatService->create($data, $roomId);

        return $this->success($seats, 'Generate ghế thành công', 201);
    }

}
