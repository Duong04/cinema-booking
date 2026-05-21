<?php

namespace App\Http\Controllers\Apis\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\MovieRequest;
use App\Http\Requests\QueryRequest;
use App\Services\MovieService;
use App\Traits\PaginationTrait;
use App\Traits\ResponseHelper;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class MovieController extends Controller
{
    use ResponseHelper, PaginationTrait;

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
                schema: new OA\Schema(type: "string", enum: ["coming_soon", "now_showing", "ended", "cancelled"], example: "coming_soon")
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
                                    new OA\Property(property: "banner_url", type: "string", format: "uri", nullable: true, example: "https://image.tmdb.org/t/p/w1280/xOMo8BRK7PfcJv9JCnx7s5hj0PX.jpg"),
                                    new OA\Property(property: "trailer_url", type: "string", format: "uri", example: "https://i.pinimg.com/736x/38/ac/35/38ac350c758e5b2d950d37f503aa33ce.jpg"),
                                    new OA\Property(property: "description", type: "string", example: "Mô tả"),
                                    new OA\Property(property: "content", type: "string", example: "Nội dung"),
                                    new OA\Property(property: "release_date", type: "string", format: "date", nullable: true, example: null),
                                    new OA\Property(property: "rating", type: "string", nullable: true, example: "T13"),
                                    new OA\Property(property: "rating_score", type: "number", format: "float", nullable: true, example: 8.6),
                                    new OA\Property(property: "rating_count", type: "integer", example: 128),
                                    new OA\Property(property: "language", type: "string", example: "Tiếng việt"),
                                    new OA\Property(property: "status", type: "string", enum: ["coming_soon", "now_showing", "ended", "cancelled"], example: "coming_soon"),
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
    public function paginate(QueryRequest $request)
    {
        $query = $request->validated();
        $limit = $query['limit'] ?? 15;
        $q = $query['q'] ?? null;
        $status = $query['status'] ?? null;

        $movies = $this->movieService->paginate($limit, $q, $status);

        return $this->successList($movies->items(), $this->paginationMeta($movies));
    }

    #[OA\Get(
        path: "/api/v1/public/movies",
        summary: "Get public movies",
        description: "Returns public movies only. Public movies are limited to now_showing and coming_soon. Use sort=top_rated to order by rating_score, release_date_desc for newest movies, duration_desc for longest movies. Use sort=best_selling with limit=5 for homepage best-selling movies. Best-selling is calculated by counting booking_items from confirmed bookings. When sort=best_selling, period defaults to 30d.",
        tags: ["Public"],
        parameters: [
            new OA\Parameter(
                name: "limit",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "integer", minimum: 1, maximum: 100, example: 5)
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
                description: "Filter by public movie status. cancelled and ended are never returned from this endpoint.",
                required: false,
                schema: new OA\Schema(type: "string", enum: ["now_showing", "coming_soon"], example: "now_showing")
            ),
            new OA\Parameter(
                name: "genre_id",
                in: "query",
                description: "Filter by genre ID",
                required: false,
                schema: new OA\Schema(type: "string", format: "uuid", example: "019d1b0b-9e90-72ed-8527-f1f966e2a279")
            ),
            new OA\Parameter(
                name: "sort",
                in: "query",
                description: "Use top_rated, release_date_desc, duration_desc, or best_selling.",
                required: false,
                schema: new OA\Schema(type: "string", enum: ["created_at_desc", "best_selling", "top_rated", "release_date_desc", "duration_desc"], example: "top_rated")
            ),
            new OA\Parameter(
                name: "period",
                in: "query",
                description: "Sales period used only with sort=best_selling. Defaults to 30d.",
                required: false,
                schema: new OA\Schema(type: "string", enum: ["7d", "30d", "all"], example: "30d")
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Public movie list retrieved successfully",
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
                                    new OA\Property(property: "banner_url", type: "string", format: "uri", nullable: true, example: "https://image.tmdb.org/t/p/w1280/xOMo8BRK7PfcJv9JCnx7s5hj0PX.jpg"),
                                    new OA\Property(property: "trailer_url", type: "string", format: "uri", example: "https://i.pinimg.com/736x/38/ac/35/38ac350c758e5b2d950d37f503aa33ce.jpg"),
                                    new OA\Property(property: "description", type: "string", example: "Mô tả"),
                                    new OA\Property(property: "content", type: "string", example: "Nội dung"),
                                    new OA\Property(property: "release_date", type: "string", format: "date", nullable: true, example: "2026-08-08"),
                                    new OA\Property(property: "rating", type: "string", nullable: true, example: "T13"),
                                    new OA\Property(property: "rating_score", type: "number", format: "float", nullable: true, example: 8.6),
                                    new OA\Property(property: "rating_count", type: "integer", example: 128),
                                    new OA\Property(property: "language", type: "string", example: "Tiếng việt"),
                                    new OA\Property(property: "status", type: "string", enum: ["coming_soon", "now_showing"], example: "now_showing"),
                                    new OA\Property(property: "sold_tickets_count", type: "integer", nullable: true, example: 128),
                                    new OA\Property(
                                        property: "genres",
                                        type: "array",
                                        items: new OA\Items(
                                            properties: [
                                                new OA\Property(property: "id", type: "string", format: "uuid", example: "019d1b0b-9e90-72ed-8527-f1f966e2a279"),
                                                new OA\Property(property: "name", type: "string", example: "Tình cảm gia đình"),
                                            ]
                                        )
                                    ),
                                ]
                            )
                        ),
                        new OA\Property(property: "meta", type: "object"),
                    ]
                )
            ),
            new OA\Response(response: 422, description: "Validation error"),
        ]
    )]
    public function getAll(QueryRequest $request)
    {
        $query = $request->validated();
        $limit = $query['limit'] ?? 15;
        $q = $query['q'] ?? null;
        $status = $query['status'] ?? null;
        $sort = $query['sort'] ?? null;
        $period = $query['period'] ?? null;
        $genreId = $query['genre_id'] ?? null;

        $movies = $this->movieService->getPublicMovies($limit, $q, $status, $sort, $period, $genreId);

        return $this->successList($movies->items(), $this->paginationMeta($movies));
    }

    #[OA\Get(
        path: "/api/v1/public/movies/{slug}",
        summary: "Get public movie detail by slug",
        description: "Returns only public movies with now_showing or coming_soon status.",
        tags: ["Public"],
        parameters: [
            new OA\Parameter(
                name: "slug",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "string", example: "bau-vat-troi-cho")
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Public movie retrieved successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Success"),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(property: "id", type: "string", format: "uuid"),
                                new OA\Property(property: "title", type: "string", example: "Báu vật trời cho"),
                                new OA\Property(property: "slug", type: "string", example: "bau-vat-troi-cho"),
                                new OA\Property(property: "duration_minutes", type: "integer", example: 198),
                                new OA\Property(property: "poster_url", type: "string", format: "uri"),
                                new OA\Property(property: "banner_url", type: "string", format: "uri", nullable: true),
                                new OA\Property(property: "trailer_url", type: "string", format: "uri"),
                                new OA\Property(property: "description", type: "string"),
                                new OA\Property(property: "content", type: "string"),
                                new OA\Property(property: "release_date", type: "string", format: "date", nullable: true),
                                new OA\Property(property: "rating", type: "string", nullable: true, example: "T13"),
                                new OA\Property(property: "rating_score", type: "number", format: "float", nullable: true, example: 8.6),
                                new OA\Property(property: "rating_count", type: "integer", example: 128),
                                new OA\Property(property: "language", type: "string", example: "Tiếng việt"),
                                new OA\Property(property: "status", type: "string", enum: ["coming_soon", "now_showing"]),
                                new OA\Property(property: "genres", type: "array", items: new OA\Items(type: "object")),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(response: 404, description: "Movie not found"),
        ]
    )]
    public function showPublic($slug)
    {
        $movie = $this->movieService->findPublicBySlug($slug);

        return $this->success($movie);
    }

    #[OA\Post(
        path: "/api/v1/movies",
        summary: "Create a new movie",
        tags: ["Movie"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["title", "duration_minutes", "status", "genres"],
                properties: [
                    new OA\Property(property: "title", type: "string", example: "Báu vật trời cho"),
                    new OA\Property(property: "duration_minutes", type: "integer", example: 198),
                    new OA\Property(property: "poster_url", type: "string", format: "uri", example: "https://i.pinimg.com/1200x/b1/a4/b7/b1a4b797511f432e508bd0377f316c57.jpg"),
                    new OA\Property(property: "banner_url", type: "string", format: "uri", nullable: true, example: "https://image.tmdb.org/t/p/w1280/xOMo8BRK7PfcJv9JCnx7s5hj0PX.jpg"),
                    new OA\Property(property: "trailer_url", type: "string", format: "uri", example: "https://i.pinimg.com/736x/38/ac/35/38ac350c758e5b2d950d37f503aa33ce.jpg"),
                    new OA\Property(property: "description", type: "string", example: "Mô tả"),
                    new OA\Property(property: "content", type: "string", example: "Nội dung"),
                    new OA\Property(property: "release_date", type: "string", format: "date", nullable: true, example: "2026-08-08"),
                    new OA\Property(property: "rating", type: "string", nullable: true, example: "T13"),
                    new OA\Property(property: "language", type: "string", example: "Tiếng việt"),
                    new OA\Property(property: "status", type: "string", enum: ["coming_soon", "now_showing", "ended", "cancelled"], example: "coming_soon"),
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
                                new OA\Property(property: "banner_url", type: "string", format: "uri", nullable: true, example: "https://image.tmdb.org/t/p/w1280/xOMo8BRK7PfcJv9JCnx7s5hj0PX.jpg"),
                                new OA\Property(property: "trailer_url", type: "string", format: "uri", example: "https://i.pinimg.com/736x/38/ac/35/38ac350c758e5b2d950d37f503aa33ce.jpg"),
                                new OA\Property(property: "description", type: "string", example: "Mô tả"),
                                new OA\Property(property: "content", type: "string", example: "Nội dung"),
                                new OA\Property(property: "release_date", type: "string", format: "date", nullable: true, example: "2026-08-08"),
                                new OA\Property(property: "rating", type: "string", nullable: true, example: "T13"),
                                new OA\Property(property: "rating_score", type: "number", format: "float", nullable: true, example: 8.6),
                                new OA\Property(property: "rating_count", type: "integer", example: 128),
                                new OA\Property(property: "language", type: "string", example: "Tiếng việt"),
                                new OA\Property(property: "status", type: "string", enum: ["coming_soon", "now_showing", "ended", "cancelled"], example: "coming_soon"),
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
                                new OA\Property(property: "banner_url", type: "array", items: new OA\Items(type: "string", example: "Banner không hợp lệ.")),
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
                                new OA\Property(property: "banner_url", type: "string", format: "uri", nullable: true, example: "https://image.tmdb.org/t/p/w1280/xOMo8BRK7PfcJv9JCnx7s5hj0PX.jpg"),
                                new OA\Property(property: "trailer_url", type: "string", format: "uri", example: "https://i.pinimg.com/736x/38/ac/35/38ac350c758e5b2d950d37f503aa33ce.jpg"),
                                new OA\Property(property: "description", type: "string", example: "Mô tả"),
                                new OA\Property(property: "content", type: "string", example: "Nội dung"),
                                new OA\Property(property: "release_date", type: "string", format: "date", nullable: true, example: null),
                                new OA\Property(property: "rating", type: "string", nullable: true, example: "T13"),
                                new OA\Property(property: "rating_score", type: "number", format: "float", nullable: true, example: 8.6),
                                new OA\Property(property: "rating_count", type: "integer", example: 128),
                                new OA\Property(property: "language", type: "string", example: "Tiếng việt"),
                                new OA\Property(property: "status", type: "string", enum: ["coming_soon", "now_showing", "ended", "cancelled"], example: "coming_soon"),
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
                required: ["title", "duration_minutes", "status", "genres"],
                properties: [
                    new OA\Property(property: "title", type: "string", example: "Báu vật trời cho"),
                    new OA\Property(property: "duration_minutes", type: "integer", example: 198),
                    new OA\Property(property: "poster_url", type: "string", format: "uri", example: "https://i.pinimg.com/1200x/b1/a4/b7/b1a4b797511f432e508bd0377f316c57.jpg"),
                    new OA\Property(property: "banner_url", type: "string", format: "uri", nullable: true, example: "https://image.tmdb.org/t/p/w1280/xOMo8BRK7PfcJv9JCnx7s5hj0PX.jpg"),
                    new OA\Property(property: "trailer_url", type: "string", format: "uri", example: "https://i.pinimg.com/736x/38/ac/35/38ac350c758e5b2d950d37f503aa33ce.jpg"),
                    new OA\Property(property: "description", type: "string", example: "Mô tả"),
                    new OA\Property(property: "content", type: "string", example: "Nội dung"),
                    new OA\Property(property: "release_date", type: "string", format: "date", nullable: true, example: "2026-08-08"),
                    new OA\Property(property: "rating", type: "string", nullable: true, example: "T13"),
                    new OA\Property(property: "language", type: "string", example: "Tiếng việt"),
                    new OA\Property(property: "status", type: "string", enum: ["coming_soon", "now_showing", "ended", "cancelled"], example: "coming_soon"),
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
                                new OA\Property(property: "banner_url", type: "string", format: "uri", nullable: true, example: "https://image.tmdb.org/t/p/w1280/xOMo8BRK7PfcJv9JCnx7s5hj0PX.jpg"),
                                new OA\Property(property: "trailer_url", type: "string", format: "uri", example: "https://i.pinimg.com/736x/38/ac/35/38ac350c758e5b2d950d37f503aa33ce.jpg"),
                                new OA\Property(property: "description", type: "string", example: "Mô tả"),
                                new OA\Property(property: "content", type: "string", example: "Nội dung"),
                                new OA\Property(property: "release_date", type: "string", format: "date", nullable: true, example: "2026-08-08"),
                                new OA\Property(property: "rating", type: "string", nullable: true, example: "T13"),
                                new OA\Property(property: "rating_score", type: "number", format: "float", nullable: true, example: 8.6),
                                new OA\Property(property: "rating_count", type: "integer", example: 128),
                                new OA\Property(property: "language", type: "string", example: "Tiếng việt"),
                                new OA\Property(property: "status", type: "string", enum: ["coming_soon", "now_showing", "ended", "cancelled"], example: "coming_soon"),
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
                                new OA\Property(property: "banner_url", type: "array", items: new OA\Items(type: "string", example: "Banner không hợp lệ.")),
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
