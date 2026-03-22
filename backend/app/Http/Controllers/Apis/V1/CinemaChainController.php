<?php

namespace App\Http\Controllers\Apis\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\QueryRequest;
use App\Http\Requests\CinemaChainRequest;
use Illuminate\Http\Request;
use App\Services\CinemaChainService;
use App\Traits\ResponseHelper;
use OpenApi\Attributes as OA;

class CinemaChainController extends Controller
{
    use ResponseHelper;

    private $cinemaChainService;

    public function __construct(CinemaChainService $cinemaChainService)
    {
        $this->cinemaChainService = $cinemaChainService;
    }

    #[OA\Get(
        path: "/api/v1/cinema-chains",
        summary: "Get list of cinema chains",
        tags: ["Cinema Chain"],
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
                                    new OA\Property(property: "id", type: "string", format: "uuid", example: "019d14e8-de20-7178-acf2-5850afc288f1"),
                                    new OA\Property(property: "name", type: "string", example: "CGV"),
                                    new OA\Property(property: "logo", type: "string", format: "uri", example: "https://jdhwjndjw.jpg"),
                                    new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-03-22T09:38:18.000000Z"),
                                    new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2026-03-22T09:38:18.000000Z"),
                                    new OA\Property(
                                        property: "cinemas",
                                        type: "array",
                                        items: new OA\Items(
                                            properties: [
                                                new OA\Property(property: "id", type: "string", format: "uuid", example: "019d1501-2b7e-7386-8e76-79a7470c3a2c"),
                                                new OA\Property(property: "name", type: "string", example: "CGV Đà Nẵng"),
                                                new OA\Property(property: "address", type: "string", nullable: true, example: "Liên Chiểu"),
                                                new OA\Property(property: "cinema_chain_id", type: "string", format: "uuid", example: "019d14e8-de20-7178-acf2-5850afc288f1"),
                                                new OA\Property(property: "city_id", type: "string", format: "uuid", example: "019d13d6-f3d4-7128-9bb4-5ab5546d20c0"),
                                                new OA\Property(
                                                    property: "city",
                                                    type: "object",
                                                    properties: [
                                                        new OA\Property(property: "id", type: "string", format: "uuid", example: "019d13d6-f3d4-7128-9bb4-5ab5546d20c0"),
                                                        new OA\Property(property: "name", type: "string", example: "Đà Nẵng"),
                                                    ]
                                                ),
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
                                new OA\Property(property: "total", type: "integer", example: 1),
                                new OA\Property(property: "per_page", type: "integer", example: 15),
                                new OA\Property(property: "current_page", type: "integer", example: 1),
                                new OA\Property(property: "last_page", type: "integer", example: 1),
                                new OA\Property(property: "current_page_url", type: "string", format: "uri", example: "http://localhost/api/v1/cinema-chains?page=1"),
                                new OA\Property(property: "first_page_url", type: "string", format: "uri", example: "http://localhost/api/v1/cinema-chains?page=1"),
                                new OA\Property(property: "last_page_url", type: "string", format: "uri", example: "http://localhost/api/v1/cinema-chains?page=1"),
                                new OA\Property(property: "next_page_url", type: "string", format: "uri", nullable: true, example: null),
                                new OA\Property(property: "prev_page_url", type: "string", format: "uri", nullable: true, example: null),
                            ]
                        ),
                    ]
                )
            ),
        ]
    )]
    public function paginate(QueryRequest $requets)
    {
        $query = $requets->validated();
        $limit = $query['limit'] ?? 15;
        $q = $query['q'] ?? null;

        $cinemaChains = $this->cinemaChainService->paginate($limit, $q);

        return $this->successList($cinemaChains->items(), [
            'total' => $cinemaChains->total(),
            'per_page' => $cinemaChains->perPage(),
            'current_page' => $cinemaChains->currentPage(),
            'last_page' => $cinemaChains->lastPage(),
            'current_page_url' => $cinemaChains->url($cinemaChains->currentPage()),
            'first_page_url' => $cinemaChains->url(1),
            'last_page_url' => $cinemaChains->url($cinemaChains->lastPage()),
            'next_page_url' => $cinemaChains->nextPageUrl(),
            'prev_page_url' => $cinemaChains->previousPageUrl(),
        ]);
    }

    #[OA\Post(
        path: "/api/v1/cinema-chains",
        summary: "Create a new cinema chain",
        tags: ["Cinema Chain"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "logo"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "CGV"),
                    new OA\Property(property: "logo", type: "string", format: "uri", example: "https://jdhwjndjw.jpg"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Cinema chain created successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Tạo chuỗi rạp phim thành công!"),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(property: "id", type: "string", format: "uuid", example: "019d14e8-de20-7178-acf2-5850afc288f1"),
                                new OA\Property(property: "name", type: "string", example: "CGV"),
                                new OA\Property(property: "logo", type: "string", format: "uri", example: "https://jdhwjndjw.jpg"),
                                new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-03-22T09:38:18.000000Z"),
                                new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2026-03-22T09:38:18.000000Z"),
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
                                new OA\Property(property: "logo", type: "array", items: new OA\Items(type: "string", example: "The logo field is required.")),
                            ]
                        ),
                    ]
                )
            ),
        ]
    )]
    public function create(CinemaChainRequest $request)
    {
        $data = $request->validated();

        $cinemaChain = $this->cinemaChainService->create($data);

        return $this->success($cinemaChain, 'Tạo chuỗi rạp phim thành công!', 201);
    }

    #[OA\Get(
        path: "/api/v1/cinema-chains/{id}",
        summary: "Get cinema chain by ID",
        tags: ["Cinema Chain"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "Cinema Chain ID",
                schema: new OA\Schema(type: "string", format: "uuid", example: "019d14e6-03df-7245-bdc0-ec3f9bccd362")
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Cinema chain retrieved successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Success"),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(property: "id", type: "string", format: "uuid", example: "019d14e6-03df-7245-bdc0-ec3f9bccd362"),
                                new OA\Property(property: "name", type: "string", example: "CGV"),
                                new OA\Property(property: "logo", type: "string", format: "uri", example: "https://jdhwjndjw.jpg"),
                                new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-03-22T09:35:11.000000Z"),
                                new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2026-03-22T09:35:11.000000Z"),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Cinema chain not found",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Không tìm thấy chuỗi rạp phim!"),
                    ]
                )
            ),
        ]
    )]
    public function show($id)
    {
        $cinemaChain = $this->cinemaChainService->find($id);

        return $this->success($cinemaChain);
    }

    #[OA\Put(
        path: "/api/v1/cinema-chains/{id}",
        summary: "Update cinema chain by ID",
        tags: ["Cinema Chain"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "Cinema Chain ID",
                schema: new OA\Schema(type: "string", format: "uuid", example: "019d14e6-03df-7245-bdc0-ec3f9bccd362")
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "logo"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "CGV"),
                    new OA\Property(property: "logo", type: "string", format: "uri", example: "https://jdhwjndjw.jpg"),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Cinema chain updated successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Cập nhật chuỗi rạp phim thành công!"),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(property: "id", type: "string", format: "uuid", example: "019d14e6-03df-7245-bdc0-ec3f9bccd362"),
                                new OA\Property(property: "name", type: "string", example: "CGV"),
                                new OA\Property(property: "logo", type: "string", format: "uri", example: "https://jdhwjndjw.jpg"),
                                new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-03-22T09:35:11.000000Z"),
                                new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2026-03-22T09:35:11.000000Z"),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Cinema chain not found",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Không tìm thấy chuỗi rạp phim!"),
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
                                new OA\Property(property: "logo", type: "array", items: new OA\Items(type: "string", example: "The logo field is required.")),
                            ]
                        ),
                    ]
                )
            ),
        ]
    )]
    public function update(CinemaChainRequest $request, $id)
    {
        $data = $request->validated();

        $cinemaChain = $this->cinemaChainService->update($id, $data);

        return $this->success($cinemaChain, 'Cập nhật chuỗi rạp phim thành công!', 200);
    }

    #[OA\Delete(
        path: "/api/v1/cinema-chains/{id}",
        summary: "Delete cinema chain by ID",
        tags: ["Cinema Chain"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "Cinema Chain ID",
                schema: new OA\Schema(type: "string", format: "uuid", example: "019d14e6-03df-7245-bdc0-ec3f9bccd362")
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Cinema chain deleted successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Xóa chuỗi rạp phim thành công!"),
                        new OA\Property(property: "data", type: "null", example: null),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Cinema chain not found",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Không tìm thấy chuỗi rạp phim!"),
                    ]
                )
            ),
        ]
    )]
    public function delete($id)
    {
        $this->cinemaChainService->delete($id);

        return $this->success(null, 'Xóa chuỗi rạp phim thành công!');
    }
}
