<?php

namespace App\Http\Controllers\Apis\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\GenreRequest;
use App\Http\Requests\QueryRequest;
use App\Services\GenreService;
use App\Traits\PaginationTrait;
use App\Traits\ResponseHelper;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class GenreController extends Controller
{
    use ResponseHelper, PaginationTrait;

    public function __construct(
        private GenreService $genreTypeService
    ) {
    }

    #[OA\Get(
        path: "/api/v1/genres",
        summary: "Get list of genres",
        tags: ["Genre"],
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
                schema: new OA\Schema(type: "string", example: "Tình cảm gia đình")
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
                                    new OA\Property(property: "id", type: "string", format: "uuid", example: "019d1b0b-9e90-72ed-8527-f1f966e2a279"),
                                    new OA\Property(property: "name", type: "string", example: "Tình cảm gia đình"),
                                    new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-03-23T14:13:59.000000Z"),
                                    new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2026-03-23T14:13:59.000000Z"),
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
                                new OA\Property(property: "current_page_url", type: "string", format: "uri", example: "http://localhost/api/v1/genres?page=1"),
                                new OA\Property(property: "first_page_url", type: "string", format: "uri", example: "http://localhost/api/v1/genres?page=1"),
                                new OA\Property(property: "last_page_url", type: "string", format: "uri", example: "http://localhost/api/v1/genres?page=1"),
                                new OA\Property(property: "next_page_url", type: "string", format: "uri", nullable: true, example: null),
                                new OA\Property(property: "prev_page_url", type: "string", format: "uri", nullable: true, example: null),
                            ]
                        ),
                    ]
                )
            ),
        ]
    )]
    public function paginate(QueryRequest $request)
    {
        $query = $request->validated();
        $limit = $query['limit'] ?? 15;
        $q = $query['q'] ?? null;

        $genres = $this->genreTypeService->paginate($limit, $q);

        return $this->successList($genres->items(), $this->paginationMeta($genres));
    }

    #[OA\Get(
        path: "/api/v1/public/genres",
        summary: "Get public genre list",
        tags: ["Public"],
        parameters: [
            new OA\Parameter(name: "limit", in: "query", required: false, schema: new OA\Schema(type: "integer", example: 100)),
            new OA\Parameter(name: "page", in: "query", required: false, schema: new OA\Schema(type: "integer", example: 1)),
            new OA\Parameter(name: "q", in: "query", required: false, description: "Search by genre name", schema: new OA\Schema(type: "string", example: "Hành động")),
        ],
        responses: [
            new OA\Response(response: 200, description: "Public genre list retrieved successfully"),
            new OA\Response(response: 422, description: "Validation error"),
        ]
    )]
    public function getAll(QueryRequest $request)
    {
        $query = $request->validated();
        $limit = $query['limit'] ?? 100;
        $q = $query['q'] ?? null;

        $genres = $this->genreTypeService->paginate($limit, $q);

        return $this->successList($genres->items(), $this->paginationMeta($genres));
    }

    #[OA\Post(
        path: "/api/v1/genres",
        summary: "Create a new genre",
        tags: ["Genre"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Tình cảm gia đình"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Genre created successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Tạo thể loại phim thành công!"),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(property: "id", type: "string", format: "uuid", example: "019d1b0b-9e90-72ed-8527-f1f966e2a279"),
                                new OA\Property(property: "name", type: "string", example: "Tình cảm gia đình"),
                                new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-03-23T14:13:59.000000Z"),
                                new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2026-03-23T14:13:59.000000Z"),
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
                            ]
                        ),
                    ]
                )
            ),
        ]
    )]
    public function create(GenreRequest $request)
    {
        $data = $request->validated();

        $genre = $this->genreTypeService->create($data);

        return $this->success($genre, 'Tạo thể loại phim thành công!', 201);
    }

    #[OA\Get(
        path: "/api/v1/genres/{id}",
        summary: "Get genre by ID",
        tags: ["Genre"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "Genre ID",
                schema: new OA\Schema(type: "string", format: "uuid", example: "019d1b0b-9e90-72ed-8527-f1f966e2a279")
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Genre retrieved successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Success"),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(property: "id", type: "string", format: "uuid", example: "019d1b0b-9e90-72ed-8527-f1f966e2a279"),
                                new OA\Property(property: "name", type: "string", example: "Tình cảm gia đình"),
                                new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-03-23T14:13:59.000000Z"),
                                new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2026-03-23T14:13:59.000000Z"),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Genre not found",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Không tìm thấy thể loại phim!"),
                    ]
                )
            ),
        ]
    )]
    public function show($id)
    {
        $genre = $this->genreTypeService->find($id);

        return $this->success($genre);
    }

    #[OA\Put(
        path: "/api/v1/genres/{id}",
        summary: "Update genre by ID",
        tags: ["Genre"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "Genre ID",
                schema: new OA\Schema(type: "string", format: "uuid", example: "019d1b0b-9e90-72ed-8527-f1f966e2a279")
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Tình cảm gia đình"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Genre updated successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Cập nhật thể loại phim thành công!"),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(property: "id", type: "string", format: "uuid", example: "019d1b0b-9e90-72ed-8527-f1f966e2a279"),
                                new OA\Property(property: "name", type: "string", example: "Tình cảm gia đình"),
                                new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-03-23T14:13:59.000000Z"),
                                new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2026-03-23T14:13:59.000000Z"),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Genre not found",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Không tìm thấy thể loại phim!"),
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
                            ]
                        ),
                    ]
                )
            ),
        ]
    )]
    public function update(GenreRequest $request, $id)
    {
        $data = $request->validated();

        $genre = $this->genreTypeService->update($id, $data);

        return $this->success($genre, 'Cập nhật thể loại phim thành công!', 200);
    }

    #[OA\Delete(
        path: "/api/v1/genres/{id}",
        summary: "Delete genre by ID",
        tags: ["Genre"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "Genre ID",
                schema: new OA\Schema(type: "string", format: "uuid", example: "019d1b0b-9e90-72ed-8527-f1f966e2a279")
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Genre deleted successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Xóa thể loại phim thành công!"),
                        new OA\Property(property: "data", type: "null", example: null),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Genre not found",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Không tìm thấy thể loại phim!"),
                    ]
                )
            ),
        ]
    )]
    public function delete($id)
    {
        $this->genreTypeService->delete($id);

        return $this->success(null, 'Xóa thể loại phim thành công!');
    }
}
