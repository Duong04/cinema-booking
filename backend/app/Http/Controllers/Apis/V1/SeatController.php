<?php

namespace App\Http\Controllers\Apis\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\SeatRequest;
use App\Services\SeatService;
use App\Traits\ResponseHelper;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class SeatController extends Controller
{
    use ResponseHelper;
    private $seatService;

    public function __construct(SeatService $seatService)
    {
        $this->seatService = $seatService;
    }
    #[OA\Get(
        path: "/api/v1/rooms/{id}/seats",
        summary: "Get seats of a room grouped by row",
        tags: ["Seat"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "Room ID",
                schema: new OA\Schema(type: "string", format: "uuid", example: "019d150a-1172-7282-867e-c9a1ba9b3e7a")
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Seats retrieved successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Success"),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            description: "Seats grouped by row label (A, B, C, ...)",
                            additionalProperties: new OA\AdditionalProperties(
                                type: "array",
                                items: new OA\Items(
                                    properties: [
                                        new OA\Property(property: "id", type: "string", format: "uuid", example: "019d1690-1002-7017-807f-64d347458d4e"),
                                        new OA\Property(property: "room_id", type: "string", format: "uuid", example: "019d150a-1172-7282-867e-c9a1ba9b3e7a"),
                                        new OA\Property(property: "seat_type_id", type: "string", format: "uuid", example: "019d1666-27cd-7089-8240-8d92a425dfd1"),
                                        new OA\Property(property: "row_label", type: "string", example: "A"),
                                        new OA\Property(property: "seat_number", type: "integer", example: 1),
                                        new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-03-22T17:20:32.000000Z"),
                                        new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2026-03-22T17:20:32.000000Z"),
                                        new OA\Property(
                                            property: "seat_type",
                                            type: "object",
                                            properties: [
                                                new OA\Property(property: "id", type: "string", format: "uuid", example: "019d1666-27cd-7089-8240-8d92a425dfd1"),
                                                new OA\Property(property: "name", type: "string", example: "Standard"),
                                                new OA\Property(property: "base_multiplier", type: "string", example: "1.00"),
                                                new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-03-22T16:34:47.000000Z"),
                                                new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2026-03-22T16:36:34.000000Z"),
                                            ]
                                        ),
                                    ]
                                )
                            )
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Room not found",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Không tìm thấy phòng chiếu!"),
                    ]
                )
            ),
        ]
    )]
    public function getSeatByRoom($roomId)
    {
        $data = $this->seatService->getSeatByRoom($roomId);

        return $this->success($data);
    }

    #[OA\Post(
        path: "/api/v1/rooms/{id}/seats",
        summary: "Create seats for a room",
        tags: ["Seat"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "Room ID",
                schema: new OA\Schema(type: "string", format: "uuid", example: "019d150a-1172-7282-867e-c9a1ba9b3e7a")
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["rows"],
                properties: [
                    new OA\Property(
                        property: "rows",
                        type: "array",
                        items: new OA\Items(
                            required: ["label", "seats_per_row", "seat_type_id"],
                            properties: [
                                new OA\Property(property: "label", type: "string", example: "A"),
                                new OA\Property(property: "seats_per_row", type: "integer", example: 10),
                                new OA\Property(property: "seat_type_id", type: "string", format: "uuid", example: "019d1666-27cd-7089-8240-8d92a425dfd1"),
                            ]
                        ),
                        example: [
                            ["label" => "A", "seats_per_row" => 10, "seat_type_id" => "019d1666-27cd-7089-8240-8d92a425dfd1"],
                            ["label" => "B", "seats_per_row" => 8, "seat_type_id" => "019d1666-27cd-7089-8240-8d92a425dfd1"],
                        ]
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Seats created successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Tạo ghế thành công!"),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            description: "Seats grouped by row label (A, B, C, ...)",
                            additionalProperties: new OA\AdditionalProperties(
                                type: "array",
                                items: new OA\Items(
                                    properties: [
                                        new OA\Property(property: "id", type: "string", format: "uuid", example: "019d1690-1002-7017-807f-64d347458d4e"),
                                        new OA\Property(property: "room_id", type: "string", format: "uuid", example: "019d150a-1172-7282-867e-c9a1ba9b3e7a"),
                                        new OA\Property(property: "seat_type_id", type: "string", format: "uuid", example: "019d1666-27cd-7089-8240-8d92a425dfd1"),
                                        new OA\Property(property: "row_label", type: "string", example: "A"),
                                        new OA\Property(property: "seat_number", type: "integer", example: 1),
                                        new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-03-22T17:20:32.000000Z"),
                                        new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2026-03-22T17:20:32.000000Z"),
                                        new OA\Property(
                                            property: "seat_type",
                                            type: "object",
                                            properties: [
                                                new OA\Property(property: "id", type: "string", format: "uuid", example: "019d1666-27cd-7089-8240-8d92a425dfd1"),
                                                new OA\Property(property: "name", type: "string", example: "Standard"),
                                                new OA\Property(property: "base_multiplier", type: "string", example: "1.00"),
                                                new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-03-22T16:34:47.000000Z"),
                                                new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2026-03-22T16:36:34.000000Z"),
                                            ]
                                        ),
                                    ]
                                )
                            ),
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Room not found",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Không tìm thấy phòng chiếu!"),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Validation error",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "The given data was invalid."),
                        new OA\Property(
                            property: "errors",
                            type: "object",
                            properties: [
                                new OA\Property(property: "rows", type: "array", items: new OA\Items(type: "string", example: "The rows field is required.")),
                                new OA\Property(property: "rows.0.label", type: "array", items: new OA\Items(type: "string", example: "The rows.0.label field is required.")),
                                new OA\Property(property: "rows.0.seats_per_row", type: "array", items: new OA\Items(type: "string", example: "The rows.0.seats_per_row field is required.")),
                                new OA\Property(property: "rows.0.seat_type_id", type: "array", items: new OA\Items(type: "string", example: "The rows.0.seat_type_id field is required.")),
                            ]
                        ),
                    ]
                )
            ),
        ]
    )]
    public function create(SeatRequest $request, $roomId)
    {
        $data = $request->validated();

        $seats = $this->seatService->create($data, $roomId);

        return $this->success($seats, 'Generate ghế thành công', 201);
    }

    #[OA\Put(
        path: "/api/v1/rooms/{roomId}/seats/{rowLabel}",
        summary: "Update a specific seat row in a room",
        tags: ["Seat"],
        parameters: [
            new OA\Parameter(
                name: "roomId",
                in: "path",
                required: true,
                description: "Room ID",
                schema: new OA\Schema(type: "string", format: "uuid", example: "019d8785-d6c2-70c6-b875-3e13669b46b4")
            ),
            new OA\Parameter(
                name: "rowLabel",
                in: "path",
                required: true,
                description: "Row label (A, B, C...)",
                schema: new OA\Schema(type: "string", example: "A")
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["seat_type_id", "seats_per_row"],
                properties: [
                    new OA\Property(property: "seat_type_id", type: "string", format: "uuid", example: "019d9c3e-498c-7224-a8ca-ca1648b8b7a8"),
                    new OA\Property(property: "seats_per_row", type: "integer", example: 9),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Row updated successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Cập nhật hàng ghế thành công"),
                        new OA\Property(property: "data", type: "nullable", example: null),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Room or row not found",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Không tìm thấy hàng ghế hoặc phòng chiếu!"),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Validation error",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "The given data was invalid."),
                        new OA\Property(
                            property: "errors",
                            type: "object",
                            properties: [
                                new OA\Property(property: "seat_type_id", type: "array", items: new OA\Items(type: "string")),
                                new OA\Property(property: "seats_per_row", type: "array", items: new OA\Items(type: "string")),
                            ]
                        ),
                    ]
                )
            ),
        ]
    )]
    public function updateRow(SeatRequest $request, string $roomId, string $rowLabel)
    {
        $data = $request->validated();

        $this->seatService->update($roomId, $rowLabel, $data);

        return $this->success(null, 'Cập nhật hàng ghế thành công', 200);
    }

    #[OA\Delete(
        path: "/api/v1/rooms/{roomId}/seats/{rowLabel}",
        summary: "Delete a specific seat row in a room",
        tags: ["Seat"],
        parameters: [
            new OA\Parameter(
                name: "roomId",
                in: "path",
                required: true,
                description: "Room ID",
                schema: new OA\Schema(type: "string", format: "uuid", example: "019d8785-d6c2-70c6-b875-3e13669b46b4")
            ),
            new OA\Parameter(
                name: "rowLabel",
                in: "path",
                required: true,
                description: "Row label (A, B, C...)",
                schema: new OA\Schema(type: "string", example: "A")
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Row deleted successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Xóa hàng thành công"),
                        new OA\Property(property: "data", type: "nullable", example: null),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Room or row not found",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Không tìm thấy hàng ghế hoặc phòng chiếu!"),
                    ]
                )
            ),
        ]
    )]
    public function deleteRow(string $roomId, string $rowLabel)
    {
        $this->seatService->deleteRow($roomId, $rowLabel);

        return $this->success(null, 'Xóa hàng thành công', 200);
    }
}
