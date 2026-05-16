<?php

namespace App\Http\Controllers\Apis\V1;

use App\Http\Controllers\Controller;
use App\Services\SeatHoldService;
use App\Traits\ResponseHelper;
use App\Http\Requests\SeatHoldRequest;
use OpenApi\Attributes as OA;

class SeatHoldController extends Controller
{
    use ResponseHelper;

    private $seatHoldService;

    public function __construct(SeatHoldService $seatHoldService)
    {
        $this->seatHoldService = $seatHoldService;
    }

    #[OA\Get(
        path: "/api/v1/seat-holds/showtimes/{showtimeId}",
        summary: "Get active seat holds by showtime",
        tags: ["Seat Holds"],
        parameters: [
            new OA\Parameter(
                name: "showtimeId",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "string", format: "uuid")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Success",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Success"),
                        new OA\Property(
                            property: "data",
                            type: "array",
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: "id", type: "string", format: "uuid"),
                                    new OA\Property(property: "user_id", type: "string", format: "uuid"),
                                    new OA\Property(property: "showtime_id", type: "string", format: "uuid"),
                                    new OA\Property(property: "seat_id", type: "string", format: "uuid"),
                                    new OA\Property(property: "expired_at", type: "string", format: "date-time"),
                                    new OA\Property(property: "created_at", type: "string", format: "date-time"),
                                    new OA\Property(property: "updated_at", type: "string", format: "date-time"),
                                    new OA\Property(
                                        property: "seat",
                                        type: "object",
                                        nullable: true,
                                        properties: [
                                            new OA\Property(property: "id", type: "string", format: "uuid"),
                                            new OA\Property(property: "row_label", type: "string", example: "A"),
                                            new OA\Property(property: "seat_number", type: "integer", example: 1),
                                        ]
                                    ),
                                    new OA\Property(
                                        property: "user",
                                        type: "object",
                                        nullable: true,
                                        properties: [
                                            new OA\Property(property: "id", type: "string", format: "uuid"),
                                            new OA\Property(property: "name", type: "string", example: "Nguyen Van A"),
                                            new OA\Property(property: "email", type: "string", example: "customer@example.com"),
                                            new OA\Property(property: "avatar", type: "string", nullable: true),
                                        ]
                                    ),
                                ]
                            )
                        ),
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Unauthenticated"),
            new OA\Response(response: 404, description: "Resource not found")
        ]
    )]
    public function getListShowtime($showtimeId)
    {
        $data = $this->seatHoldService->getListShowtime($showtimeId);

        return $this->success($data);
    }

    #[OA\Post(
        path: "/api/v1/seat-holds/hold",
        summary: "Hold seats",
        tags: ["Seat Holds"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["showtime_id", "seat_ids"],
                properties: [
                    new OA\Property(
                        property: "showtime_id",
                        type: "string",
                        format: "uuid",
                        example: "019d06bd-ff12-71c8-8234-aa2cc7733011"
                    ),
                    new OA\Property(
                        property: "seat_ids",
                        type: "array",
                        minItems: 1,
                        items: new OA\Items(type: "string", format: "uuid"),
                        example: [
                            "019d06bd-ff12-71c8-8234-aa2cc7733012",
                            "019d06bd-ff12-71c8-8234-aa2cc7733013",
                        ]
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Seats held successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Giu ghe thanh cong."),
                        new OA\Property(property: "data", type: "object", nullable: true, example: null),
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Unauthenticated"),
            new OA\Response(response: 422, description: "Validation error, seats already held, or seats already booked")
        ]
    )]
    public function hold(SeatHoldRequest $request)
    {
        $data = $request->validated();
        $this->seatHoldService->hold($data);
        
        return $this->success(null, 'Giữ ghế thành công.');
    }

    #[OA\Post(
        path: "/api/v1/seat-holds/release",
        summary: "Release held seats by showtime",
        tags: ["Seat Holds"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["showtime_id"],
                properties: [
                    new OA\Property(
                        property: "showtime_id",
                        type: "string",
                        format: "uuid",
                        example: "019d06bd-ff12-71c8-8234-aa2cc7733011"
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Seats released successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Huy giu ghe thanh cong."),
                        new OA\Property(property: "data", type: "object", nullable: true, example: null),
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Unauthenticated"),
            new OA\Response(response: 422, description: "Validation error")
        ]
    )]
    public function release(SeatHoldRequest $request)
    {
        $data = $request->validated();
        $this->seatHoldService->release($data);

        return $this->success(null, 'Hủy giữ ghế thành công.');
    }

}
