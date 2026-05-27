<?php

namespace App\Http\Controllers\Apis\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\QueryRequest;
use App\Http\Requests\CityRequest;
use App\Traits\PaginationTrait;
use Illuminate\Http\Request;
use App\Services\CityService;
use App\Traits\ResponseHelper;
use OpenApi\Attributes as OA;

class CityController extends Controller
{
    use ResponseHelper, PaginationTrait;

    public function __construct(
        private CityService $cityService
    ) {
    }

    #[OA\Get(
        path: "/api/v1/cities",
        summary: "Get list of cities",
        tags: ["Cities"],
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
        $query = $request->validated();
        $limit = $query['limit'] ?? 15;
        $q = $query['q'] ?? null;

        $cities = $this->cityService->paginate($limit, $q);

        return $this->successList($cities->items(), $this->paginationMeta($cities));
    }

    #[OA\Get(
        path: "/api/v1/public/cities",
        summary: "Get public city list",
        tags: ["Public"],
        parameters: [
            new OA\Parameter(name: "limit", in: "query", required: false, schema: new OA\Schema(type: "integer", example: 100)),
            new OA\Parameter(name: "page", in: "query", required: false, schema: new OA\Schema(type: "integer", example: 1)),
            new OA\Parameter(name: "q", in: "query", required: false, description: "Search by city name", schema: new OA\Schema(type: "string", example: "Đà Nẵng")),
        ],
        responses: [
            new OA\Response(response: 200, description: "Public city list retrieved successfully"),
            new OA\Response(response: 422, description: "Validation error"),
        ]
    )]
    public function getAll(QueryRequest $request)
    {
        $query = $request->validated();
        $limit = $query['limit'] ?? 100;
        $q = $query['q'] ?? null;

        $cities = $this->cityService->paginate($limit, $q);

        return $this->successList($cities->items(), $this->paginationMeta($cities));
    }

    #[OA\Post(
        path: "/api/v1/cities",
        summary: "Create new city",
        tags: ["Cities"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Đà Nẵng"),
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
                        new OA\Property(property: "message", type: "string", example: "Tạo thành phố thành công!"),
                        new OA\Property(property: "data", type: "object"),
                    ]
                )
            )
        ]
    )]
    public function create(CityRequest $request)
    {
        $data = $request->validated();

        $city = $this->cityService->create($data);

        return $this->success($city, 'Tạo thành phố thành công!', 201);
    }

    #[OA\Get(
        path: "/api/v1/cities/{id}",
        summary: "Get city detail",
        tags: ["Cities"],
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
        $city = $this->cityService->find($id);

        return $this->success($city);
    }

    #[OA\Put(
        path: "/api/v1/cities/{id}",
        summary: "Update city",
        tags: ["Cities"],
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
                        new OA\Property(property: "message", type: "string", example: "Cập nhật thành phố thành công!"),
                        new OA\Property(property: "data", type: "object"),
                    ]
                )
            )
        ]
    )]
    public function update(CityRequest $request, $id)
    {
        $data = $request->validated();

        $city = $this->cityService->update($id, $data);

        return $this->success($city, 'Cập nhật thành phố thành công!', 200);
    }

    #[OA\Delete(
        path: "/api/v1/cities/{id}",
        summary: "Delete city",
        tags: ["Cities"],
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
                        new OA\Property(property: "message", type: "string", example: "Xóa thành phố thành công!"),
                        new OA\Property(property: "data", type: "object", nullable: true, example: null),
                    ]
                )
            )
        ]
    )]
    public function delete($id)
    {
        $this->cityService->delete($id);

        return $this->success(null, 'Xóa thành phố thành công!');
    }
}
