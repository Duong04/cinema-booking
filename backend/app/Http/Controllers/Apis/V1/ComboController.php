<?php

namespace App\Http\Controllers\Apis\V1;

use App\Http\Controllers\Controller;
use App\Services\ComboService;
use App\Http\Requests\ComboRequest;
use App\Http\Requests\QueryRequest;
use App\Traits\ResponseHelper;
use App\Traits\PaginationTrait;
use OpenApi\Attributes as OA;

class ComboController extends Controller
{
    use ResponseHelper, PaginationTrait;

    public function __construct(
        private ComboService $comboService
    ) {
    }

    #[OA\Get(
        path: "/api/v1/combos",
        summary: "Get list of combos",
        tags: ["Combos"],
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
                schema: new OA\Schema(type: "string", example: "admin")
            ),
            new OA\Parameter(
                name: "cinema_id",
                in: "query",
                description: "Search by cinema",
                required: false,
                schema: new OA\Schema(type: "string", example: "019d14e8-de20-7178-acf2-5850afc288f1")
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
                                    new OA\Property(property: "id", type: "string"),
                                    new OA\Property(property: "name", type: "string", example: "Đà Nẵng"),
                                    new OA\Property(property: "price", type: "string", example: "200.00"),
                                    new OA\Property(property: "status", type: "string", enum: ["active", "inactive"], example: "inactive"),
                                    new OA\Property(property: "image", type: "string", example: "https://example.com/image.jpg"),
                                     new OA\Property(
                                        property: "cinema",
                                        type: "object",
                                        properties: [
                                            new OA\Property(property: "id", type: "string"),
                                            new OA\Property(property: "name", type: "string", example: "CGV Đà Nẵng"),
                                            new OA\Property(property: "address", type: "string", example: "Đà Nẵng"),
                                        ]
                                    ),
                                    new OA\Property(
                                        property: "created_at",
                                        type: "string",
                                        format: "date-time",
                                        example: "2024-06-06 18:09:22"
                                    ),
                                    new OA\Property(
                                        property: "updated_at",
                                        type: "string",
                                        format: "date-time",
                                        example: "2024-06-06 18:09:22"
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
            )
        ]
    )]
    public function paginate(QueryRequest $request)
    {
        $data = $request->validated();
        $limit = $data['limit'] ?? 15;
        $q = $data['q'] ?? null;
        $cinemaId = $data['cinema_id'] ?? null;
        $status = $data['status'] ?? null;

        $combos = $this->comboService->paginate($limit, $q, $cinemaId, $status);

        return $this->successList($combos->items(), $this->paginationMeta($combos));
    }

    #[OA\Post(
        path: "/api/v1/combos",
        summary: "Create new combo",
        tags: ["Combos"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Combo 1"),
                    new OA\Property(property: "description", type: "string", example: "Combo 1 description"),
                    new OA\Property(property: "price", type: "string", example: "200.00"),
                    new OA\Property(property: "status", type: "string", enum: ["active", "inactive"], example: "active"),
                    new OA\Property(property: "image", type: "string", example: "https://example.com/image.jpg"),
                    new OA\Property(property: "cinema_id", type: "string", example: "019d14e8-de20-7178-acf2-5850afc288f1"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Created successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Tạo combo thành công!"),
                        new OA\Property(property: "data", type: "object"),
                    ]
                )
            )
        ]
    )]
    public function create(ComboRequest $request)
    {
        $data = $request->all();

        $combo = $this->comboService->create($data);

        return $this->success($combo, 'Tạo combo thành công!', 201);
    }

    #[OA\Get(
        path: "/api/v1/public/combos",
        summary: "Get active public combos",
        description: "Return only active combos for the booking flow. Optionally filter by cinema.",
        tags: ["Combos"],
        parameters: [
            new OA\Parameter(
                name: "limit",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "integer", default: 100, example: 100)
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
                description: "Search active combos by name",
                required: false,
                schema: new OA\Schema(type: "string", example: "popcorn")
            ),
            new OA\Parameter(
                name: "cinema_id",
                in: "query",
                description: "Filter active combos by cinema",
                required: false,
                schema: new OA\Schema(type: "string", format: "uuid", example: "019d14e8-de20-7178-acf2-5850afc288f1")
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Active combos retrieved successfully",
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
                                    new OA\Property(property: "name", type: "string", example: "Combo Popcorn Couple"),
                                    new OA\Property(property: "description", type: "string", nullable: true, example: "Large popcorn and two soft drinks."),
                                    new OA\Property(property: "price", type: "string", example: "129000.00"),
                                    new OA\Property(property: "status", type: "string", enum: ["active"], example: "active"),
                                    new OA\Property(property: "image", type: "string", nullable: true, example: "https://example.com/combo.jpg"),
                                    new OA\Property(property: "cinema_id", type: "string", format: "uuid"),
                                    new OA\Property(
                                        property: "cinema",
                                        type: "object",
                                        nullable: true,
                                        properties: [
                                            new OA\Property(property: "id", type: "string", format: "uuid"),
                                            new OA\Property(property: "name", type: "string", example: "CGV Đà Nẵng"),
                                            new OA\Property(property: "address", type: "string", example: "Đà Nẵng"),
                                        ]
                                    ),
                                    new OA\Property(property: "created_at", type: "string", format: "date-time"),
                                    new OA\Property(property: "updated_at", type: "string", format: "date-time"),
                                ]
                            )
                        ),
                        new OA\Property(
                            property: "meta",
                            type: "object",
                            properties: [
                                new OA\Property(property: "total", type: "integer", example: 3),
                                new OA\Property(property: "per_page", type: "integer", example: 100),
                                new OA\Property(property: "current_page", type: "integer", example: 1),
                                new OA\Property(property: "last_page", type: "integer", example: 1),
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
            new OA\Response(response: 422, description: "Validation error")
        ]
    )]
    public function active(QueryRequest $request)
    {
        $data = $request->validated();
        $limit = $data['limit'] ?? 100;
        $q = $data['q'] ?? null;
        $cinemaId = $data['cinema_id'] ?? null;

        $combos = $this->comboService->paginate($limit, $q, $cinemaId, 'active');

        return $this->successList($combos->items(), $this->paginationMeta($combos));
    }

    #[OA\Get(
        path: "/api/v1/combos/{id}",
        summary: "Get combo detail",
        tags: ["Combos"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "string")
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
                            properties: [
                                new OA\Property(property: "id", type: "string"),
                                new OA\Property(property: "name", type: "string"),
                                new OA\Property(property: "description", type: "string"),
                                new OA\Property(property: "price", type: "string"),
                                new OA\Property(property: "status", type: "string"),
                                new OA\Property(property: "image", type: "string"),
                                new OA\Property(property: "cinema_id", type: "string"),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(response: 404, description: "Resource not found")
        ]
    )]
    public function show($id)
    {
        $combo = $this->comboService->find($id);

        return $this->success($combo);
    }

    #[OA\Put(
        path: "/api/v1/combos/{id}",
        summary: "Update combo",
        tags: ["Combos"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "string")
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "name", type: "string"),
                    new OA\Property(property: "description", type: "string"),
                    new OA\Property(property: "price", type: "string"),
                    new OA\Property(property: "status", type: "string"),
                    new OA\Property(property: "image", type: "string"),
                    new OA\Property(property: "cinema_id", type: "string"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Updated successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Cập nhật combo thành công!"),
                        new OA\Property(property: "data", type: "object"),
                    ]
                )
            )
        ]
    )]
    public function update(ComboRequest $request, $id)
    {
        $data = $request->validated();

        $combo = $this->comboService->update($id, $data);

        return $this->success($combo, 'Cập nhật combo thành công!', 200);
    }

    #[OA\Delete(
        path: "/api/v1/combos/{id}",
        summary: "Delete combo",
        tags: ["Combos"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "string")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Deleted successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Xóa combo thành công!"),
                        new OA\Property(property: "data", type: "object", nullable: true, example: null),
                    ]
                )
            )
        ]
    )]
    public function delete($id)
    {
        $this->comboService->delete($id);

        return $this->success(null, 'Xóa combo thành công!');
    }
}
