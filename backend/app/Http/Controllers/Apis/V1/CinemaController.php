<?php

namespace App\Http\Controllers\Apis\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\QueryRequest;
use App\Http\Requests\CinemaRequest;
use App\Traits\PaginationTrait;
use Illuminate\Http\Request;
use App\Services\CinemaService;
use App\Traits\ResponseHelper;
use OpenApi\Attributes as OA;

class CinemaController extends Controller
{
    use ResponseHelper, PaginationTrait;

    private $cinemaService;

    public function __construct(CinemaService $cinemaService)
    {
        $this->cinemaService = $cinemaService;
    }

    #[OA\Get(
        path: "/api/v1/cinemas",
        summary: "Get list of cinemas",
        tags: ["Cinema"],
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
                schema: new OA\Schema(type: "string", example: "CGV")
            ),
            new OA\Parameter(
                name: "city_id",
                in: "query",
                description: "Search by city",
                required: false,
                schema: new OA\Schema(type: "string", example: "019d14e8-de20-7178-acf2-5850afc288f1")
            ),
            new OA\Parameter(
                name: "cinema_chain_id",
                in: "query",
                description: "Search by cinema chain",
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
                                    new OA\Property(property: "id", type: "string", format: "uuid", example: "019d1501-2b7e-7386-8e76-79a7470c3a2c"),
                                    new OA\Property(property: "city_id", type: "string", format: "uuid", example: "019d13d6-f3d4-7128-9bb4-5ab5546d20c0"),
                                    new OA\Property(property: "cinema_chain_id", type: "string", format: "uuid", example: "019d14e8-de20-7178-acf2-5850afc288f1"),
                                    new OA\Property(property: "name", type: "string", example: "CGV Đà Nẵng"),
                                    new OA\Property(property: "address", type: "string", nullable: true, example: "Liên Chiểu"),
                                    new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-03-22T10:04:51.000000Z"),
                                    new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2026-03-22T10:04:51.000000Z"),
                                    new OA\Property(property: "deleted_at", type: "string", format: "date-time", nullable: true, example: null),
                                    new OA\Property(
                                        property: "city",
                                        type: "object",
                                        properties: [
                                            new OA\Property(property: "id", type: "string", format: "uuid", example: "019d13d6-f3d4-7128-9bb4-5ab5546d20c0"),
                                            new OA\Property(property: "name", type: "string", example: "Đà Nẵng"),
                                        ]
                                    ),
                                    new OA\Property(
                                        property: "cinema_chain",
                                        type: "object",
                                        properties: [
                                            new OA\Property(property: "id", type: "string", format: "uuid", example: "019d14e8-de20-7178-acf2-5850afc288f1"),
                                            new OA\Property(property: "name", type: "string", example: "CGV"),
                                            new OA\Property(property: "logo", type: "string", format: "uri", example: "https://jdhwjndjw.jpg"),
                                        ]
                                    ),
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
                                new OA\Property(property: "current_page_url", type: "string", format: "uri", example: "http://localhost/api/v1/cinemas?page=1"),
                                new OA\Property(property: "first_page_url", type: "string", format: "uri", example: "http://localhost/api/v1/cinemas?page=1"),
                                new OA\Property(property: "last_page_url", type: "string", format: "uri", example: "http://localhost/api/v1/cinemas?page=1"),
                                new OA\Property(property: "next_page_url", type: "string", format: "uri", nullable: true, example: null),
                                new OA\Property(property: "prev_page_url", type: "string", format: "uri", nullable: true, example: null),
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
        $cityId = $query['city_id'] ?? null;
        $cinemaChainId = $query['cinema_chain_id'] ?? null;

        $cinemas = $this->cinemaService->paginate($limit, $q, $cityId, $cinemaChainId);

        return $this->successList($cinemas->items(), $this->paginationMeta($cinemas));
    }

    #[OA\Post(
        path: "/api/v1/cinemas",
        summary: "Create a new cinema",
        tags: ["Cinema"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "city_id", "cinema_chain_id"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "CGV Đà Nẵng"),
                    new OA\Property(property: "address", type: "string", nullable: true, example: "Liên Chiểu"),
                    new OA\Property(property: "city_id", type: "string", format: "uuid", example: "019d13d6-f3d4-7128-9bb4-5ab5546d20c0"),
                    new OA\Property(property: "cinema_chain_id", type: "string", format: "uuid", example: "019d14e8-de20-7178-acf2-5850afc288f1"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Cinema created successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Tạo rạp phim thành công!"),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(property: "id", type: "string", format: "uuid", example: "019d1501-2b7e-7386-8e76-79a7470c3a2c"),
                                new OA\Property(property: "city_id", type: "string", format: "uuid", example: "019d13d6-f3d4-7128-9bb4-5ab5546d20c0"),
                                new OA\Property(property: "cinema_chain_id", type: "string", format: "uuid", example: "019d14e8-de20-7178-acf2-5850afc288f1"),
                                new OA\Property(property: "name", type: "string", example: "CGV Đà Nẵng"),
                                new OA\Property(property: "address", type: "string", nullable: true, example: "Liên Chiểu"),
                                new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-03-22T10:04:51.000000Z"),
                                new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2026-03-22T10:04:51.000000Z"),
                                new OA\Property(property: "deleted_at", type: "string", format: "date-time", nullable: true, example: null),
                                new OA\Property(
                                    property: "city",
                                    type: "object",
                                    properties: [
                                        new OA\Property(property: "id", type: "string", format: "uuid", example: "019d13d6-f3d4-7128-9bb4-5ab5546d20c0"),
                                        new OA\Property(property: "name", type: "string", example: "Đà Nẵng"),
                                    ]
                                ),
                                new OA\Property(
                                    property: "cinema_chain",
                                    type: "object",
                                    properties: [
                                        new OA\Property(property: "id", type: "string", format: "uuid", example: "019d14e8-de20-7178-acf2-5850afc288f1"),
                                        new OA\Property(property: "name", type: "string", example: "CGV"),
                                        new OA\Property(property: "logo", type: "string", format: "uri", example: "https://jdhwjndjw.jpg"),
                                    ]
                                ),
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
                                new OA\Property(property: "city_id", type: "array", items: new OA\Items(type: "string", example: "The city id field is required.")),
                                new OA\Property(property: "cinema_chain_id", type: "array", items: new OA\Items(type: "string", example: "The cinema chain id field is required.")),
                            ]
                        ),
                    ]
                )
            ),
        ]
    )]
    public function create(CinemaRequest $request)
    {
        $data = $request->validated();

        $cinema = $this->cinemaService->create($data);

        return $this->success($cinema, 'Tạo rạp phim thành công!', 201);
    }

    #[OA\Get(
        path: "/api/v1/cinemas/{id}",
        summary: "Get cinema by ID",
        tags: ["Cinema"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "Cinema ID",
                schema: new OA\Schema(type: "string", format: "uuid", example: "019d1501-2b7e-7386-8e76-79a7470c3a2c")
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Cinema retrieved successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Success"),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(property: "id", type: "string", format: "uuid", example: "019d1501-2b7e-7386-8e76-79a7470c3a2c"),
                                new OA\Property(property: "city_id", type: "string", format: "uuid", example: "019d13d6-f3d4-7128-9bb4-5ab5546d20c0"),
                                new OA\Property(property: "cinema_chain_id", type: "string", format: "uuid", example: "019d14e8-de20-7178-acf2-5850afc288f1"),
                                new OA\Property(property: "name", type: "string", example: "CGV Đà Nẵng"),
                                new OA\Property(property: "address", type: "string", nullable: true, example: "Liên Chiểu"),
                                new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-03-22T10:04:51.000000Z"),
                                new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2026-03-22T10:04:51.000000Z"),
                                new OA\Property(property: "deleted_at", type: "string", format: "date-time", nullable: true, example: null),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Cinema not found",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Không tìm thấy rạp phim!"),
                    ]
                )
            ),
        ]
    )]
    public function show($id)
    {
        $cinema = $this->cinemaService->find($id);

        return $this->success($cinema);
    }

    #[OA\Put(
        path: "/api/v1/cinemas/{id}",
        summary: "Update cinema by ID",
        tags: ["Cinema"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "Cinema ID",
                schema: new OA\Schema(type: "string", format: "uuid", example: "019d1501-2b7e-7386-8e76-79a7470c3a2c")
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "city_id", "cinema_chain_id"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "CGV Đà Nẵng"),
                    new OA\Property(property: "address", type: "string", nullable: true, example: "Liên Chiểu"),
                    new OA\Property(property: "city_id", type: "string", format: "uuid", example: "019d13d6-f3d4-7128-9bb4-5ab5546d20c0"),
                    new OA\Property(property: "cinema_chain_id", type: "string", format: "uuid", example: "019d14e8-de20-7178-acf2-5850afc288f1"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Cinema updated successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Cập nhật rạp phim thành công!"),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(property: "id", type: "string", format: "uuid", example: "019d1501-2b7e-7386-8e76-79a7470c3a2c"),
                                new OA\Property(property: "city_id", type: "string", format: "uuid", example: "019d13d6-f3d4-7128-9bb4-5ab5546d20c0"),
                                new OA\Property(property: "cinema_chain_id", type: "string", format: "uuid", example: "019d14e8-de20-7178-acf2-5850afc288f1"),
                                new OA\Property(property: "name", type: "string", example: "CGV Đà Nẵng"),
                                new OA\Property(property: "address", type: "string", nullable: true, example: "Liên Chiểu"),
                                new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-03-22T10:04:51.000000Z"),
                                new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2026-03-22T10:04:51.000000Z"),
                                new OA\Property(property: "deleted_at", type: "string", format: "date-time", nullable: true, example: null),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Cinema not found",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Không tìm thấy rạp phim!"),
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
                                new OA\Property(property: "city_id", type: "array", items: new OA\Items(type: "string", example: "The city id field is required.")),
                                new OA\Property(property: "cinema_chain_id", type: "array", items: new OA\Items(type: "string", example: "The cinema chain id field is required.")),
                            ]
                        ),
                    ]
                )
            ),
        ]
    )]
    public function update(CinemaRequest $request, $id)
    {
        $data = $request->validated();

        $cinema = $this->cinemaService->update($id, $data);

        return $this->success($cinema, 'Cập nhật rạp phim thành công!', 200);
    }

    #[OA\Delete(
        path: "/api/v1/cinemas/{id}",
        summary: "Delete cinema by ID",
        tags: ["Cinema"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "Cinema ID",
                schema: new OA\Schema(type: "string", format: "uuid", example: "019d1501-2b7e-7386-8e76-79a7470c3a2c")
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Cinema deleted successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Xóa rạp phim thành công!"),
                        new OA\Property(property: "data", type: "null", example: null),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Cinema not found",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Không tìm thấy rạp phim!"),
                    ]
                )
            ),
        ]
    )]
    public function delete($id)
    {
        $this->cinemaService->delete($id);

        return $this->success(null, 'Xóa rạp phim thành công!');
    }
}
