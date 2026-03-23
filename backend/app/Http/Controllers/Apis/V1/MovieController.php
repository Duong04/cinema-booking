<?php

namespace App\Http\Controllers\Apis\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\MovieRequest;
use App\Http\Requests\QueryRequest;
use App\Services\MovieService;
use App\Traits\ResponseHelper;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class MovieController extends Controller
{
    use ResponseHelper;

    private $movieService;

    public function __construct(MovieService $movieService)
    {
        $this->movieService = $movieService;
    }
    #[OA\Get(
        path: "/api/v1/movies",
        summary: "Get list of movies",
        tags: ["Movie"],
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
                description: "Search by title",
                required: false,
                schema: new OA\Schema(type: "string", example: "Báu vật trời cho")
            ),
            new OA\Parameter(
                name: "status",
                in: "query",
                description: "Filter by status",
                required: false,
                schema: new OA\Schema(type: "string", enum: ["coming_soon", "now_showing", "ended"], example: "coming_soon")
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
                                    new OA\Property(property: "id", type: "string", format: "uuid", example: "019d1b15-14c1-7195-abf2-33f252dc10b6"),
                                    new OA\Property(property: "title", type: "string", example: "Báu vật trời cho"),
                                    new OA\Property(property: "slug", type: "string", example: "bau-vat-troi-cho"),
                                    new OA\Property(property: "duration_minutes", type: "integer", example: 198),
                                    new OA\Property(property: "poster_url", type: "string", format: "uri", example: "https://i.pinimg.com/1200x/b1/a4/b7/b1a4b797511f432e508bd0377f316c57.jpg"),
                                    new OA\Property(property: "trailer_url", type: "string", format: "uri", example: "https://i.pinimg.com/736x/38/ac/35/38ac350c758e5b2d950d37f503aa33ce.jpg"),
                                    new OA\Property(property: "description", type: "string", example: "Mô tả"),
                                    new OA\Property(property: "content", type: "string", example: "Nội dung"),
                                    new OA\Property(property: "release_date", type: "string", format: "date", nullable: true, example: null),
                                    new OA\Property(property: "rating", type: "number", format: "float", nullable: true, example: null),
                                    new OA\Property(property: "language", type: "string", example: "Tiếng việt"),
                                    new OA\Property(property: "status", type: "string", enum: ["coming_soon", "now_showing", "ended"], example: "coming_soon"),
                                    new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-03-23T14:24:19.000000Z"),
                                    new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2026-03-23T14:24:19.000000Z"),
                                    new OA\Property(property: "deleted_at", type: "string", format: "date-time", nullable: true, example: null),
                                    new OA\Property(
                                        property: "genres",
                                        type: "array",
                                        items: new OA\Items(
                                            properties: [
                                                new OA\Property(property: "id", type: "string", format: "uuid", example: "019d1b0b-9e90-72ed-8527-f1f966e2a279"),
                                                new OA\Property(property: "name", type: "string", example: "Tình cảm gia đình"),
                                                new OA\Property(
                                                    property: "pivot",
                                                    type: "object",
                                                    properties: [
                                                        new OA\Property(property: "movie_id", type: "string", format: "uuid", example: "019d1b15-14c1-7195-abf2-33f252dc10b6"),
                                                        new OA\Property(property: "genre_id", type: "string", format: "uuid", example: "019d1b0b-9e90-72ed-8527-f1f966e2a279"),
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
                                new OA\Property(property: "current_page_url", type: "string", format: "uri", example: "http://localhost/api/v1/movies?page=1"),
                                new OA\Property(property: "first_page_url", type: "string", format: "uri", example: "http://localhost/api/v1/movies?page=1"),
                                new OA\Property(property: "last_page_url", type: "string", format: "uri", example: "http://localhost/api/v1/movies?page=1"),
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

        $movies = $this->movieService->paginate($limit, $q);

        return $this->successList($movies->items(), [
            'total' => $movies->total(),
            'per_page' => $movies->perPage(),
            'current_page' => $movies->currentPage(),
            'last_page' => $movies->lastPage(),
            'current_page_url' => $movies->url($movies->currentPage()),
            'first_page_url' => $movies->url(1),
            'last_page_url' => $movies->url($movies->lastPage()),
            'next_page_url' => $movies->nextPageUrl(),
            'prev_page_url' => $movies->previousPageUrl(),
        ]);
    }
    #[OA\Post(
        path: "/api/v1/movies",
        summary: "Create a new movie",
        tags: ["Movie"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["title", "duration_minutes", "poster_url", "trailer_url", "description", "content", "language", "status", "genres"],
                properties: [
                    new OA\Property(property: "title", type: "string", example: "Báu vật trời cho"),
                    new OA\Property(property: "duration_minutes", type: "integer", example: 198),
                    new OA\Property(property: "poster_url", type: "string", format: "uri", example: "https://i.pinimg.com/1200x/b1/a4/b7/b1a4b797511f432e508bd0377f316c57.jpg"),
                    new OA\Property(property: "trailer_url", type: "string", format: "uri", example: "https://i.pinimg.com/736x/38/ac/35/38ac350c758e5b2d950d37f503aa33ce.jpg"),
                    new OA\Property(property: "description", type: "string", example: "Mô tả"),
                    new OA\Property(property: "content", type: "string", example: "Nội dung"),
                    new OA\Property(property: "release_date", type: "string", format: "date", nullable: true, example: "2026-08-08"),
                    new OA\Property(property: "language", type: "string", example: "Tiếng việt"),
                    new OA\Property(property: "status", type: "string", enum: ["coming_soon", "now_showing", "ended"], example: "coming_soon"),
                    new OA\Property(
                        property: "genres",
                        type: "array",
                        items: new OA\Items(type: "string", format: "uuid", example: "019d1b0b-9e90-72ed-8527-f1f966e2a279")
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Movie created successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Tạo phim thành công!"),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(property: "id", type: "string", format: "uuid", example: "019d1b15-14c1-7195-abf2-33f252dc10b6"),
                                new OA\Property(property: "title", type: "string", example: "Báu vật trời cho"),
                                new OA\Property(property: "slug", type: "string", example: "bau-vat-troi-cho"),
                                new OA\Property(property: "duration_minutes", type: "integer", example: 198),
                                new OA\Property(property: "poster_url", type: "string", format: "uri", example: "https://i.pinimg.com/1200x/b1/a4/b7/b1a4b797511f432e508bd0377f316c57.jpg"),
                                new OA\Property(property: "trailer_url", type: "string", format: "uri", example: "https://i.pinimg.com/736x/38/ac/35/38ac350c758e5b2d950d37f503aa33ce.jpg"),
                                new OA\Property(property: "description", type: "string", example: "Mô tả"),
                                new OA\Property(property: "content", type: "string", example: "Nội dung"),
                                new OA\Property(property: "release_date", type: "string", format: "date", nullable: true, example: "2026-08-08"),
                                new OA\Property(property: "rating", type: "number", format: "float", nullable: true, example: null),
                                new OA\Property(property: "language", type: "string", example: "Tiếng việt"),
                                new OA\Property(property: "status", type: "string", enum: ["coming_soon", "now_showing", "ended"], example: "coming_soon"),
                                new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-03-23T14:24:19.000000Z"),
                                new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2026-03-23T14:24:19.000000Z"),
                                new OA\Property(property: "deleted_at", type: "string", format: "date-time", nullable: true, example: null),
                                new OA\Property(
                                    property: "genres",
                                    type: "array",
                                    items: new OA\Items(
                                        properties: [
                                            new OA\Property(property: "id", type: "string", format: "uuid", example: "019d1b0b-9e90-72ed-8527-f1f966e2a279"),
                                            new OA\Property(property: "name", type: "string", example: "Tình cảm gia đình"),
                                            new OA\Property(
                                                property: "pivot",
                                                type: "object",
                                                properties: [
                                                    new OA\Property(property: "movie_id", type: "string", format: "uuid", example: "019d1b15-14c1-7195-abf2-33f252dc10b6"),
                                                    new OA\Property(property: "genre_id", type: "string", format: "uuid", example: "019d1b0b-9e90-72ed-8527-f1f966e2a279"),
                                                ]
                                            ),
                                        ]
                                    )
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
                                new OA\Property(property: "title", type: "array", items: new OA\Items(type: "string", example: "The title field is required.")),
                                new OA\Property(property: "duration_minutes", type: "array", items: new OA\Items(type: "string", example: "The duration minutes field is required.")),
                                new OA\Property(property: "poster_url", type: "array", items: new OA\Items(type: "string", example: "The poster url field is required.")),
                                new OA\Property(property: "status", type: "array", items: new OA\Items(type: "string", example: "The status field is required.")),
                                new OA\Property(property: "genres", type: "array", items: new OA\Items(type: "string", example: "The genres field is required.")),
                            ]
                        ),
                    ]
                )
            ),
        ]
    )]
    public function create(MovieRequest $request)
    {
        $data = $request->validated();

        $movie = $this->movieService->create($data);

        return $this->success($movie, 'Tạo phim thành công!', 201);
    }
    #[OA\Get(
        path: "/api/v1/movies/{id}",
        summary: "Get movie by ID",
        tags: ["Movie"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "Movie ID",
                schema: new OA\Schema(type: "string", format: "uuid", example: "019d1b15-14c1-7195-abf2-33f252dc10b6")
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Movie retrieved successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Success"),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(property: "id", type: "string", format: "uuid", example: "019d1b15-14c1-7195-abf2-33f252dc10b6"),
                                new OA\Property(property: "title", type: "string", example: "Báu vật trời cho"),
                                new OA\Property(property: "slug", type: "string", example: "bau-vat-troi-cho"),
                                new OA\Property(property: "duration_minutes", type: "integer", example: 198),
                                new OA\Property(property: "poster_url", type: "string", format: "uri", example: "https://i.pinimg.com/1200x/b1/a4/b7/b1a4b797511f432e508bd0377f316c57.jpg"),
                                new OA\Property(property: "trailer_url", type: "string", format: "uri", example: "https://i.pinimg.com/736x/38/ac/35/38ac350c758e5b2d950d37f503aa33ce.jpg"),
                                new OA\Property(property: "description", type: "string", example: "Mô tả"),
                                new OA\Property(property: "content", type: "string", example: "Nội dung"),
                                new OA\Property(property: "release_date", type: "string", format: "date", nullable: true, example: null),
                                new OA\Property(property: "rating", type: "number", format: "float", nullable: true, example: null),
                                new OA\Property(property: "language", type: "string", example: "Tiếng việt"),
                                new OA\Property(property: "status", type: "string", enum: ["coming_soon", "now_showing", "ended"], example: "coming_soon"),
                                new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-03-23T14:24:19.000000Z"),
                                new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2026-03-23T14:24:19.000000Z"),
                                new OA\Property(property: "deleted_at", type: "string", format: "date-time", nullable: true, example: null),
                                new OA\Property(
                                    property: "genres",
                                    type: "array",
                                    items: new OA\Items(
                                        properties: [
                                            new OA\Property(property: "id", type: "string", format: "uuid", example: "019d1b0b-9e90-72ed-8527-f1f966e2a279"),
                                            new OA\Property(property: "name", type: "string", example: "Tình cảm gia đình"),
                                            new OA\Property(
                                                property: "pivot",
                                                type: "object",
                                                properties: [
                                                    new OA\Property(property: "movie_id", type: "string", format: "uuid", example: "019d1b15-14c1-7195-abf2-33f252dc10b6"),
                                                    new OA\Property(property: "genre_id", type: "string", format: "uuid", example: "019d1b0b-9e90-72ed-8527-f1f966e2a279"),
                                                ]
                                            ),
                                        ]
                                    )
                                ),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Movie not found",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Không tìm thấy phim!"),
                    ]
                )
            ),
        ]
    )]
    public function show($id)
    {
        $movie = $this->movieService->find($id);

        return $this->success($movie);
    }
    #[OA\Put(
        path: "/api/v1/movies/{id}",
        summary: "Update movie by ID",
        tags: ["Movie"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "Movie ID",
                schema: new OA\Schema(type: "string", format: "uuid", example: "019d1b15-14c1-7195-abf2-33f252dc10b6")
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["title", "duration_minutes", "poster_url", "trailer_url", "description", "content", "language", "status", "genres"],
                properties: [
                    new OA\Property(property: "title", type: "string", example: "Báu vật trời cho"),
                    new OA\Property(property: "duration_minutes", type: "integer", example: 198),
                    new OA\Property(property: "poster_url", type: "string", format: "uri", example: "https://i.pinimg.com/1200x/b1/a4/b7/b1a4b797511f432e508bd0377f316c57.jpg"),
                    new OA\Property(property: "trailer_url", type: "string", format: "uri", example: "https://i.pinimg.com/736x/38/ac/35/38ac350c758e5b2d950d37f503aa33ce.jpg"),
                    new OA\Property(property: "description", type: "string", example: "Mô tả"),
                    new OA\Property(property: "content", type: "string", example: "Nội dung"),
                    new OA\Property(property: "release_date", type: "string", format: "date", nullable: true, example: "2026-08-08"),
                    new OA\Property(property: "language", type: "string", example: "Tiếng việt"),
                    new OA\Property(property: "status", type: "string", enum: ["coming_soon", "now_showing", "ended"], example: "coming_soon"),
                    new OA\Property(
                        property: "genres",
                        type: "array",
                        items: new OA\Items(type: "string", format: "uuid", example: "019d1b0b-9e90-72ed-8527-f1f966e2a279")
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Movie updated successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Cập nhật phim thành công!"),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(property: "id", type: "string", format: "uuid", example: "019d1b15-14c1-7195-abf2-33f252dc10b6"),
                                new OA\Property(property: "title", type: "string", example: "Báu vật trời cho"),
                                new OA\Property(property: "slug", type: "string", example: "bau-vat-troi-cho"),
                                new OA\Property(property: "duration_minutes", type: "integer", example: 198),
                                new OA\Property(property: "poster_url", type: "string", format: "uri", example: "https://i.pinimg.com/1200x/b1/a4/b7/b1a4b797511f432e508bd0377f316c57.jpg"),
                                new OA\Property(property: "trailer_url", type: "string", format: "uri", example: "https://i.pinimg.com/736x/38/ac/35/38ac350c758e5b2d950d37f503aa33ce.jpg"),
                                new OA\Property(property: "description", type: "string", example: "Mô tả"),
                                new OA\Property(property: "content", type: "string", example: "Nội dung"),
                                new OA\Property(property: "release_date", type: "string", format: "date", nullable: true, example: "2026-08-08"),
                                new OA\Property(property: "rating", type: "number", format: "float", nullable: true, example: null),
                                new OA\Property(property: "language", type: "string", example: "Tiếng việt"),
                                new OA\Property(property: "status", type: "string", enum: ["coming_soon", "now_showing", "ended"], example: "coming_soon"),
                                new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-03-23T14:24:19.000000Z"),
                                new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2026-03-23T14:24:19.000000Z"),
                                new OA\Property(property: "deleted_at", type: "string", format: "date-time", nullable: true, example: null),
                                new OA\Property(
                                    property: "genres",
                                    type: "array",
                                    items: new OA\Items(
                                        properties: [
                                            new OA\Property(property: "id", type: "string", format: "uuid", example: "019d1b0b-9e90-72ed-8527-f1f966e2a279"),
                                            new OA\Property(property: "name", type: "string", example: "Tình cảm gia đình"),
                                            new OA\Property(
                                                property: "pivot",
                                                type: "object",
                                                properties: [
                                                    new OA\Property(property: "movie_id", type: "string", format: "uuid", example: "019d1b15-14c1-7195-abf2-33f252dc10b6"),
                                                    new OA\Property(property: "genre_id", type: "string", format: "uuid", example: "019d1b0b-9e90-72ed-8527-f1f966e2a279"),
                                                ]
                                            ),
                                        ]
                                    )
                                ),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Movie not found",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Không tìm thấy phim!"),
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
                                new OA\Property(property: "title", type: "array", items: new OA\Items(type: "string", example: "The title field is required.")),
                                new OA\Property(property: "duration_minutes", type: "array", items: new OA\Items(type: "string", example: "The duration minutes field is required.")),
                                new OA\Property(property: "poster_url", type: "array", items: new OA\Items(type: "string", example: "The poster url field is required.")),
                                new OA\Property(property: "status", type: "array", items: new OA\Items(type: "string", example: "The status field is required.")),
                                new OA\Property(property: "genres", type: "array", items: new OA\Items(type: "string", example: "The genres field is required.")),
                            ]
                        ),
                    ]
                )
            ),
        ]
    )]
    public function update(MovieRequest $request, $id)
    {
        $data = $request->validated();

        $movie = $this->movieService->update($id, $data);

        return $this->success($movie, 'Cập nhật phim thành công!', 200);
    }
    #[OA\Delete(
        path: "/api/v1/movies/{id}",
        summary: "Delete movie by ID",
        tags: ["Movie"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "Movie ID",
                schema: new OA\Schema(type: "string", format: "uuid", example: "019d1b15-14c1-7195-abf2-33f252dc10b6")
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Movie deleted successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Xóa phim thành công!"),
                        new OA\Property(property: "data", type: "null", example: null),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Movie not found",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Không tìm thấy phim!"),
                    ]
                )
            ),
        ]
    )]
    public function delete($id)
    {
        $this->movieService->delete($id);

        return $this->success(null, 'Xóa phim thành công!');
    }
}
