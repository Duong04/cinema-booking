<?php

namespace App\Http\Controllers\Apis\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\QueryRequest;
use App\Http\Requests\SeatTypeRequest;
use App\Services\SeatTypeService;
use App\Traits\PaginationTrait;
use App\Traits\ResponseHelper;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class SeatTypeController extends Controller
{
    use ResponseHelper, PaginationTrait;

    private $seatTypeService;

    public function __construct(SeatTypeService $seatTypeService)
    {
        $this->seatTypeService = $seatTypeService;
    }
    #[OA\Get(
        path: "/api/v1/seat-types",
        summary: "Get list of seat types",
        tags: ["Seat Type"],
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
                schema: new OA\Schema(type: "string", example: "Standard")
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
                                    new OA\Property(property: "id", type: "string", format: "uuid", example: "019d1666-27cd-7089-8240-8d92a425dfd1"),
                                    new OA\Property(property: "name", type: "string", example: "Standard"),
                                    new OA\Property(property: "base_multiplier", type: "string", example: "1.00"),
                                    new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-03-22T16:34:47.000000Z"),
                                    new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2026-03-22T16:36:34.000000Z"),
                                ]
                            )
                        ),
                        new OA\Property(
                            property: "meta",
                            type: "object",
                            properties: [
                                new OA\Property(property: "total", type: "integer", example: 1),
                                new OA\Property(property: "per_page", type: "integer", example: 15),
                                new OA\Property(property: "current_page", type: "integer", example: 1),
                                new OA\Property(property: "last_page", type: "integer", example: 1),
                                new OA\Property(property: "current_page_url", type: "string", format: "uri", example: "http://localhost/api/v1/seat-types?page=1"),
                                new OA\Property(property: "first_page_url", type: "string", format: "uri", example: "http://localhost/api/v1/seat-types?page=1"),
                                new OA\Property(property: "last_page_url", type: "string", format: "uri", example: "http://localhost/api/v1/seat-types?page=1"),
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

        $seatTypes = $this->seatTypeService->paginate($limit, $q);

        return $this->successList($seatTypes->items(), $this->paginationMeta($seatTypes));
    }
    #[OA\Post(
        path: "/api/v1/seat-types",
        summary: "Create a new seat type",
        tags: ["Seat Type"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "base_multiplier"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "VIP"),
                    new OA\Property(property: "base_multiplier", type: "number", format: "float", example: 1.0),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Seat type created successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Tạo loại ghế thành công!"),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(property: "id", type: "string", format: "uuid", example: "019d1666-27cd-7089-8240-8d92a425dfd1"),
                                new OA\Property(property: "name", type: "string", example: "VIP"),
                                new OA\Property(property: "base_multiplier", type: "string", example: "1.00"),
                                new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-03-22T16:34:47.000000Z"),
                                new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2026-03-22T16:34:47.000000Z"),
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
                                new OA\Property(property: "base_multiplier", type: "array", items: new OA\Items(type: "string", example: "The base multiplier field is required.")),
                            ]
                        ),
                    ]
                )
            ),
        ]
    )]
    public function create(SeatTypeRequest $request)
    {
        $data = $request->validated();

        $seatType = $this->seatTypeService->create($data);

        return $this->success($seatType, 'Tạo loại ghế thành công!', 201);
    }
    #[OA\Get(
        path: "/api/v1/seat-types/{id}",
        summary: "Get seat type by ID",
        tags: ["Seat Type"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "Seat Type ID",
                schema: new OA\Schema(type: "string", format: "uuid", example: "019d1666-27cd-7089-8240-8d92a425dfd1")
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Seat type retrieved successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Success"),
                        new OA\Property(
                            property: "data",
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
            new OA\Response(
                response: 404,
                description: "Seat type not found",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Không tìm thấy loại ghế!"),
                    ]
                )
            ),
        ]
    )]
    public function show($id)
    {
        $seatType = $this->seatTypeService->find($id);

        return $this->success($seatType);
    }
    #[OA\Put(
        path: "/api/v1/seat-types/{id}",
        summary: "Update seat type by ID",
        tags: ["Seat Type"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "Seat Type ID",
                schema: new OA\Schema(type: "string", format: "uuid", example: "019d1666-27cd-7089-8240-8d92a425dfd1")
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "base_multiplier"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "VIP"),
                    new OA\Property(property: "base_multiplier", type: "number", format: "float", example: 1.0),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Seat type updated successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Cập nhật loại ghế thành công!"),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(property: "id", type: "string", format: "uuid", example: "019d1666-27cd-7089-8240-8d92a425dfd1"),
                                new OA\Property(property: "name", type: "string", example: "VIP"),
                                new OA\Property(property: "base_multiplier", type: "string", example: "1.00"),
                                new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-03-22T16:34:47.000000Z"),
                                new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2026-03-22T16:36:34.000000Z"),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Seat type not found",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Không tìm thấy loại ghế!"),
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
                                new OA\Property(property: "base_multiplier", type: "array", items: new OA\Items(type: "string", example: "The base multiplier field is required.")),
                            ]
                        ),
                    ]
                )
            ),
        ]
    )]
    public function update(SeatTypeRequest $request, $id)
    {
        $data = $request->validated();

        $seatType = $this->seatTypeService->update($id, $data);

        return $this->success($seatType, 'Cập nhật loại ghế thành công!', 200);
    }
    #[OA\Delete(
        path: "/api/v1/seat-types/{id}",
        summary: "Delete seat type by ID",
        tags: ["Seat Type"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "Seat Type ID",
                schema: new OA\Schema(type: "string", format: "uuid", example: "019d1666-27cd-7089-8240-8d92a425dfd1")
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Seat type deleted successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Xóa loại ghế thành công!"),
                        new OA\Property(property: "data", type: "null", example: null),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Seat type not found",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Không tìm thấy loại ghế!"),
                    ]
                )
            ),
        ]
    )]
    public function delete($id)
    {
        $this->seatTypeService->delete($id);

        return $this->success(null, 'Xóa loại ghế thành công!');
    }
}
