<?php

namespace App\Http\Controllers\Apis\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\QueryRequest;
use App\Http\Requests\BookingRequest;
use Illuminate\Http\Request;
use App\Services\BookingService;
use App\Traits\ResponseHelper;
use App\Traits\PaginationTrait;
use OpenApi\Attributes as OA;

class BookingController extends Controller
{
    use ResponseHelper, PaginationTrait;

    private $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }


    public function paginate(QueryRequest $request)
    {
        $query = $request->validated();
        $limit = $query['limit'] ?? 15;
        $q = $query['q'] ?? null;
        $status = $query['status'] ?? null;

        $bookings = $this->bookingService->paginate($limit, $q, $status);

        return $this->successList($bookings->items(), $this->paginationMeta($bookings));
    }

    public function show($id)
    {
        $booking = $this->bookingService->find($id);

        return $this->success($booking);

    }

    public function create(BookingRequest $request)
    {
        $data = $request->validated();

        $booking = $this->bookingService->create($data);

        return $this->success($booking, 'Đặt vé thành công!');
    }

    public function cancel(BookingRequest $request, string $id)
    {
        $booking = $this->bookingService->cancel($request->all(), $id);

        return $this->success($booking, 'Đã hủy đặt vé thành công!');
    }
}
