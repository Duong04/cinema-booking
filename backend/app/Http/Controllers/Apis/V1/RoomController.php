<?php

namespace App\Http\Controllers\Apis\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\QueryRequest;
use App\Http\Requests\RoomRequest;
use App\Traits\PaginationTrait;
use Illuminate\Http\Request;
use App\Services\RoomService;
use App\Traits\ResponseHelper;
use OpenApi\Attributes as OA;

class RoomController extends Controller
{
    use ResponseHelper, PaginationTrait;

    private $roomService;

    public function __construct(RoomService $roomService)
    {
        $this->roomService = $roomService;
    }

    #[OA\Get(
        path: "/api/v1/rooms",
        summary: "Get list of rooms",
        tags: ["Room"],
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
                description: "Search by name",
                required: false,
                schema: new OA\Schema(type: "string", example: "ROOM 1")
            ),
            new OA\Parameter(
                name: "cinema_id",
                in: "query",
                description: "Filter by cinema",
                required: false,
                schema: new OA\Schema(type: "string", format: "uuid", example: "019d1501-2b7e-7386-8e76-79a7470c3a2c")
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
                                    new OA\Property(property: "id", type: "string", format: "uuid", example: "019d150a-1172-7282-867e-c9a1ba9b3e7a"),
                                    new OA\Property(property: "cinema_id", type: "string", format: "uuid", example: "019d1501-2b7e-7386-8e76-79a7470c3a2c"),
                                    new OA\Property(property: "name", type: "string", example: "ROOM 1"),
                                    new OA\Property(property: "type", type: "string", example: "VIP"),
                                    new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-03-22T10:14:34.000000Z"),
                                    new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2026-03-22T10:14:34.000000Z"),
                                    new OA\Property(property: "deleted_at", type: "string", format: "date-time", nullable: true, example: null),
                                    new OA\Property(
                                        property: "cinema",
                                        type: "object",
                                        properties: [
                                            new OA\Property(property: "id", type: "string", format: "uuid", example: "019d1501-2b7e-7386-8e76-79a7470c3a2c"),
                                            new OA\Property(property: "name", type: "string", example: "CGV Đà Nẵng"),
                                            new OA\Property(property: "address", type: "string", nullable: true, example: "Liên Chiểu"),
                                        ]
                                    ),
                                ]
                            )
                        ),
                        new OA\Property(
                            property: "meta",
                            type: "object",
                            properties: [
                                new OA\Property(property: "total", type: "integer", example: 2),
                                new OA\Property(property: "per_page", type: "integer", example: 15),
                                new OA\Property(property: "current_page", type: "integer", example: 1),
                                new OA\Property(property: "last_page", type: "integer", example: 1),
                                new OA\Property(property: "current_page_url", type: "string", format: "uri", example: "http://localhost/api/v1/rooms?page=1"),
                                new OA\Property(property: "first_page_url", type: "string", format: "uri", example: "http://localhost/api/v1/rooms?page=1"),
                                new OA\Property(property: "last_page_url", type: "string", format: "uri", example: "http://localhost/api/v1/rooms?page=1"),
                                new OA\Property(property: "next_page_url", type: "string", format: "uri", nullable: true, example: null),
                                new OA\Property(property: "prev_page_url", type: "string", format: "uri", nullable: true, example: null),
                            ]
                        ),
                    ]
                )
            ),
        ]
    )]
    public function paginate(QueryRequest $requet)
    {
        $query = $requet->validated();
        $limit = $query['limit'] ?? 15;
        $q = $query['q'] ?? null;

        $rooms = $this->roomService->paginate($limit, $q);

        return $this->successList($rooms->items(), $this->paginationMeta($rooms));
    }

    #[OA\Post(
        path: "/api/v1/rooms",
        summary: "Create a new room",
        tags: ["Room"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "type", "cinema_id"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "ROOM 1"),
                    new OA\Property(property: "type", type: "string", example: "VIP"),
                    new OA\Property(property: "cinema_id", type: "string", format: "uuid", example: "019d1501-2b7e-7386-8e76-79a7470c3a2c"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Room created successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Tạo phòng chiếu thành công!"),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(property: "id", type: "string", format: "uuid", example: "019d1506-609d-70cf-8bbe-3d3d21f36a66"),
                                new OA\Property(property: "cinema_id", type: "string", format: "uuid", example: "019d1501-2b7e-7386-8e76-79a7470c3a2c"),
                                new OA\Property(property: "name", type: "string", example: "ROOM 1"),
                                new OA\Property(property: "type", type: "string", example: "VIP"),
                                new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-03-22T10:10:32.000000Z"),
                                new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2026-03-22T10:10:32.000000Z"),
                                new OA\Property(property: "deleted_at", type: "string", format: "date-time", nullable: true, example: null),
                            ]
                        ),
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
                                new OA\Property(property: "name", type: "array", items: new OA\Items(type: "string", example: "The name field is required.")),
                                new OA\Property(property: "type", type: "array", items: new OA\Items(type: "string", example: "The type field is required.")),
                                new OA\Property(property: "cinema_id", type: "array", items: new OA\Items(type: "string", example: "The cinema id field is required.")),
                            ]
                        ),
                    ]
                )
            ),
        ]
    )]
    public function create(RoomRequest $request)
    {
        $data = $request->validated();

        $room = $this->roomService->create($data);

        return $this->success($room, 'Tạo phòng thành công!', 201);
    }

    #[OA\Get(
        path: "/api/v1/rooms/{id}",
        summary: "Get room by ID",
        tags: ["Room"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "Room ID",
                schema: new OA\Schema(type: "string", format: "uuid", example: "019d1506-609d-70cf-8bbe-3d3d21f36a66")
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Room retrieved successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Success"),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(property: "id", type: "string", format: "uuid", example: "019d1506-609d-70cf-8bbe-3d3d21f36a66"),
                                new OA\Property(property: "cinema_id", type: "string", format: "uuid", example: "019d1501-2b7e-7386-8e76-79a7470c3a2c"),
                                new OA\Property(property: "name", type: "string", example: "ROOM 1"),
                                new OA\Property(property: "type", type: "string", example: "VIP"),
                                new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-03-22T10:10:32.000000Z"),
                                new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2026-03-22T10:10:32.000000Z"),
                                new OA\Property(property: "deleted_at", type: "string", format: "date-time", nullable: true, example: null),
                            ]
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
    public function show($id)
    {
        $room = $this->roomService->find($id);

        return $this->success($room);
    }

    #[OA\Put(
        path: "/api/v1/rooms/{id}",
        summary: "Update room by ID",
        tags: ["Room"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "Room ID",
                schema: new OA\Schema(type: "string", format: "uuid", example: "019d1506-609d-70cf-8bbe-3d3d21f36a66")
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "type", "cinema_id"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "ROOM 1"),
                    new OA\Property(property: "type", type: "string", example: "VIP"),
                    new OA\Property(property: "cinema_id", type: "string", format: "uuid", example: "019d1501-2b7e-7386-8e76-79a7470c3a2c"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Room updated successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Cập nhật phòng chiếu thành công!"),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(property: "id", type: "string", format: "uuid", example: "019d1506-609d-70cf-8bbe-3d3d21f36a66"),
                                new OA\Property(property: "cinema_id", type: "string", format: "uuid", example: "019d1501-2b7e-7386-8e76-79a7470c3a2c"),
                                new OA\Property(property: "name", type: "string", example: "ROOM 1"),
                                new OA\Property(property: "type", type: "string", example: "VIP"),
                                new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-03-22T10:10:32.000000Z"),
                                new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2026-03-22T10:10:32.000000Z"),
                                new OA\Property(property: "deleted_at", type: "string", format: "date-time", nullable: true, example: null),
                            ]
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
                                new OA\Property(property: "name", type: "array", items: new OA\Items(type: "string", example: "The name field is required.")),
                                new OA\Property(property: "type", type: "array", items: new OA\Items(type: "string", example: "The type field is required.")),
                                new OA\Property(property: "cinema_id", type: "array", items: new OA\Items(type: "string", example: "The cinema id field is required.")),
                            ]
                        ),
                    ]
                )
            ),
        ]
    )]
    public function update(RoomRequest $request, $id)
    {
        $data = $request->validated();

        $room = $this->roomService->update($id, $data);

        return $this->success($room, 'Cập nhật phòng thành công!', 200);
    }

    #[OA\Delete(
        path: "/api/v1/rooms/{id}",
        summary: "Delete room by ID",
        tags: ["Room"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "Room ID",
                schema: new OA\Schema(type: "string", format: "uuid", example: "019d1506-609d-70cf-8bbe-3d3d21f36a66")
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Room deleted successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Xóa phòng chiếu thành công!"),
                        new OA\Property(property: "data", type: "null", example: null),
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
    public function delete($id)
    {
        $this->roomService->delete($id);

        return $this->success(null, 'Xóa phòng thành công!');
    }
}
