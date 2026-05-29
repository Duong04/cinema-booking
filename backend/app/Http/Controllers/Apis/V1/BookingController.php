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

    public function __construct(
        private BookingService $bookingService
    ) {
    }

    #[OA\Get(
        path: "/api/v1/bookings",
        summary: "Get list of bookings",
        tags: ["Bookings"],
        parameters: [
            new OA\Parameter(
                name: "limit",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "integer", example: 15)
            ),
            new OA\Parameter(
                name: "page",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "integer", example: 1)
            ),
            new OA\Parameter(
                name: "q",
                in: "query",
                description: "Search by booking code, customer name, or movie title",
                required: false,
                schema: new OA\Schema(type: "string", example: "BK-20240606")
            ),
            new OA\Parameter(
                name: "status",
                in: "query",
                description: "Filter by booking status",
                required: false,
                schema: new OA\Schema(
                    type: "string",
                    enum: ["pending", "confirmed", "cancelled", "refunded", "expired"],
                    example: "pending"
                )
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "List retrieved successfully",
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
                                    new OA\Property(property: "booking_code", type: "string", example: "BK-20240606-ABC123"),
                                    new OA\Property(property: "total_amount", type: "number", format: "float", example: 180000),
                                    new OA\Property(property: "status", type: "string", example: "pending"),
                                    new OA\Property(property: "cancellation_reason", type: "string", nullable: true),
                                    new OA\Property(property: "cancelled_at", type: "string", format: "date-time", nullable: true),
                                    new OA\Property(property: "expired_at", type: "string", format: "date-time", nullable: true),
                                    new OA\Property(property: "confirmed_at", type: "string", format: "date-time", nullable: true),
                                    new OA\Property(property: "created_at", type: "string", format: "date-time"),
                                    new OA\Property(property: "updated_at", type: "string", format: "date-time"),
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
                                    new OA\Property(property: "showtime", type: "object", nullable: true),
                                    new OA\Property(
                                        property: "items",
                                        type: "array",
                                        items: new OA\Items(
                                            properties: [
                                                new OA\Property(property: "id", type: "string", format: "uuid"),
                                                new OA\Property(property: "booking_id", type: "string", format: "uuid"),
                                                new OA\Property(property: "seat_id", type: "string", format: "uuid"),
                                                new OA\Property(property: "price", type: "number", format: "float", example: 90000),
                                                new OA\Property(property: "seat_type_name", type: "string", example: "Standard"),
                                                new OA\Property(property: "movie_title", type: "string", example: "Inside Out 2"),
                                                new OA\Property(property: "room_name", type: "string", example: "Room 1"),
                                                new OA\Property(property: "seat_label", type: "string", example: "A1"),
                                            ]
                                        )
                                    ),
                                ]
                            )
                        ),
                        new OA\Property(
                            property: "meta",
                            type: "object",
                            properties: [
                                new OA\Property(property: "total", type: "integer"),
                                new OA\Property(property: "per_page", type: "integer"),
                                new OA\Property(property: "current_page", type: "integer"),
                                new OA\Property(property: "last_page", type: "integer"),
                                new OA\Property(property: "current_page_url", type: "string"),
                                new OA\Property(property: "first_page_url", type: "string"),
                                new OA\Property(property: "last_page_url", type: "string"),
                                new OA\Property(property: "next_page_url", type: "string", nullable: true),
                                new OA\Property(property: "prev_page_url", type: "string", nullable: true),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Unauthenticated")
        ]
    )]
    public function paginate(QueryRequest $request)
    {
        $query = $request->validated();
        $limit = $query['limit'] ?? 15;
        $q = $query['q'] ?? null;
        $status = $query['status'] ?? null;

        $bookings = $this->bookingService->paginate($limit, $q, $status);

        return $this->successList($bookings->items(), $this->paginationMeta($bookings));
    }

    #[OA\Get(
        path: "/api/v1/bookings/{id}",
        summary: "Get booking detail",
        tags: ["Bookings"],
        parameters: [
            new OA\Parameter(
                name: "id",
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
                            type: "object",
                            properties: [
                                new OA\Property(property: "id", type: "string", format: "uuid"),
                                new OA\Property(property: "user_id", type: "string", format: "uuid"),
                                new OA\Property(property: "showtime_id", type: "string", format: "uuid"),
                                new OA\Property(property: "booking_code", type: "string", example: "BK-20240606-ABC123"),
                                new OA\Property(property: "total_amount", type: "number", format: "float", example: 180000),
                                new OA\Property(property: "status", type: "string", example: "pending"),
                                new OA\Property(property: "cancellation_reason", type: "string", nullable: true),
                                new OA\Property(property: "cancelled_at", type: "string", format: "date-time", nullable: true),
                                new OA\Property(property: "expired_at", type: "string", format: "date-time", nullable: true),
                                new OA\Property(property: "confirmed_at", type: "string", format: "date-time", nullable: true),
                                new OA\Property(property: "user", type: "object", nullable: true),
                                new OA\Property(property: "showtime", type: "object", nullable: true),
                                new OA\Property(
                                    property: "items",
                                    type: "array",
                                    items: new OA\Items(type: "object")
                                ),
                                new OA\Property(
                                    property: "status_logs",
                                    type: "array",
                                    items: new OA\Items(type: "object")
                                ),
                                new OA\Property(property: "payment", type: "object", nullable: true),
                                new OA\Property(
                                    property: "combos",
                                    type: "array",
                                    items: new OA\Items(type: "object")
                                ),
                                new OA\Property(
                                    property: "promotions",
                                    type: "array",
                                    items: new OA\Items(type: "object")
                                ),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Unauthenticated"),
            new OA\Response(response: 404, description: "Resource not found")
        ]
    )]
    public function show($id)
    {
        $booking = $this->bookingService->find($id);

        return $this->success($booking);

    }

    #[OA\Post(
        path: "/api/v1/bookings",
        summary: "Create new booking",
        tags: ["Bookings"],
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
                        maxItems: 8,
                        items: new OA\Items(type: "string", format: "uuid"),
                        example: [
                            "019d06bd-ff12-71c8-8234-aa2cc7733012",
                            "019d06bd-ff12-71c8-8234-aa2cc7733013",
                        ]
                    ),
                    new OA\Property(
                        property: "combos",
                        type: "array",
                        nullable: true,
                        items: new OA\Items(
                            required: ["combo_id", "quantity"],
                            properties: [
                                new OA\Property(property: "combo_id", type: "string", format: "uuid"),
                                new OA\Property(property: "quantity", type: "integer", minimum: 1, maximum: 20, example: 2),
                            ]
                        )
                    ),
                    new OA\Property(
                        property: "promotion_code",
                        type: "string",
                        nullable: true,
                        example: "SALE10"
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Created successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Dat ve thanh cong!"),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(property: "id", type: "string", format: "uuid"),
                                new OA\Property(property: "user_id", type: "string", format: "uuid"),
                                new OA\Property(property: "showtime_id", type: "string", format: "uuid"),
                                new OA\Property(property: "booking_code", type: "string", example: "BK-20240606-ABC123"),
                                new OA\Property(property: "total_amount", type: "number", format: "float", example: 180000),
                                new OA\Property(property: "status", type: "string", example: "pending"),
                                new OA\Property(property: "expired_at", type: "string", format: "date-time", nullable: true),
                                new OA\Property(
                                    property: "items",
                                    type: "array",
                                    items: new OA\Items(
                                        properties: [
                                            new OA\Property(property: "id", type: "string", format: "uuid"),
                                            new OA\Property(property: "booking_id", type: "string", format: "uuid"),
                                            new OA\Property(property: "seat_id", type: "string", format: "uuid"),
                                            new OA\Property(property: "price", type: "number", format: "float", example: 90000),
                                            new OA\Property(property: "seat_type_name", type: "string", example: "Standard"),
                                            new OA\Property(property: "movie_title", type: "string", example: "Inside Out 2"),
                                            new OA\Property(property: "room_name", type: "string", example: "Room 1"),
                                            new OA\Property(property: "seat_label", type: "string", example: "A1"),
                                        ]
                                    )
                                ),
                                new OA\Property(
                                    property: "combos",
                                    type: "array",
                                    items: new OA\Items(type: "object")
                                ),
                                new OA\Property(
                                    property: "promotions",
                                    type: "array",
                                    items: new OA\Items(type: "object")
                                ),
                                new OA\Property(property: "payment", type: "object", nullable: true),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Unauthenticated"),
            new OA\Response(response: 422, description: "Validation error or selected seats are no longer held")
        ]
    )]
    public function create(BookingRequest $request)
    {
        $data = $request->validated();

        $booking = $this->bookingService->create($data);

        return $this->success($booking, 'Đặt vé thành công!', 201);
    }

    #[OA\Put(
        path: "/api/v1/bookings/{id}",
        summary: "Update booking status and timestamps",
        tags: ["Bookings"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "string", format: "uuid")
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        property: "status",
                        type: "string",
                        enum: ["pending", "confirmed", "cancelled", "expired", "refunded"],
                        nullable: true,
                        example: "confirmed"
                    ),
                    new OA\Property(property: "cancellation_reason", type: "string", nullable: true, maxLength: 500),
                    new OA\Property(property: "expired_at", type: "string", format: "date-time", nullable: true),
                    new OA\Property(property: "confirmed_at", type: "string", format: "date-time", nullable: true),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Updated successfully"),
            new OA\Response(response: 401, description: "Unauthenticated"),
            new OA\Response(response: 404, description: "Resource not found"),
            new OA\Response(response: 422, description: "Validation error")
        ]
    )]
    public function update(BookingRequest $request, string $id)
    {
        $booking = $this->bookingService->update($request->validated(), $id);

        return $this->success($booking, 'Cập nhật đặt vé thành công!');
    }

    #[OA\Put(
        path: "/api/v1/bookings/{id}/cancel",
        summary: "Cancel booking",
        tags: ["Bookings"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "string", format: "uuid")
            )
        ],
        requestBody: new OA\RequestBody(
            required: false,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        property: "cancellation_reason",
                        type: "string",
                        maxLength: 500,
                        nullable: true,
                        example: "Customer changed plans"
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Cancelled successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Da huy dat ve thanh cong!"),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(property: "id", type: "string", format: "uuid"),
                                new OA\Property(property: "booking_code", type: "string", example: "BK-20240606-ABC123"),
                                new OA\Property(property: "status", type: "string", example: "cancelled"),
                                new OA\Property(property: "cancellation_reason", type: "string", nullable: true),
                                new OA\Property(property: "cancelled_at", type: "string", format: "date-time", nullable: true),
                                new OA\Property(property: "updated_at", type: "string", format: "date-time"),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Unauthenticated"),
            new OA\Response(response: 404, description: "Resource not found"),
            new OA\Response(response: 422, description: "Booking cannot be cancelled in its current status")
        ]
    )]
    public function cancel(BookingRequest $request, string $id)
    {
        $booking = $this->bookingService->cancel($request->all(), $id);

        return $this->success($booking, 'Đã hủy đặt vé thành công!');
    }
}
