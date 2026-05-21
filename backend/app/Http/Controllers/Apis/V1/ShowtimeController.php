<?php

namespace App\Http\Controllers\Apis\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\QueryRequest;
use App\Http\Requests\ShowtimeRequest;
use App\Services\ShowtimeService;
use App\Traits\PaginationTrait;
use App\Traits\ResponseHelper;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class ShowtimeController extends Controller
{
    use ResponseHelper, PaginationTrait;

    private $showtimeService;

    public function __construct(ShowtimeService $showtimeService)
    {
        $this->showtimeService = $showtimeService;
    }
    #[OA\Get(
        path: "/api/v1/showtimes",
        summary: "Get list of showtimes",
        tags: ["Showtime"],
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
                name: "movie_id",
                in: "query",
                description: "Filter by movie",
                required: false,
                schema: new OA\Schema(type: "string", format: "uuid", example: "019d1b15-14c1-7195-abf2-33f252dc10b6")
            ),
            new OA\Parameter(
                name: "room_id",
                in: "query",
                description: "Filter by room",
                required: false,
                schema: new OA\Schema(type: "string", format: "uuid", example: "019d1506-609d-70cf-8bbe-3d3d21f36a66")
            ),
            new OA\Parameter(
                name: "show_date",
                in: "query",
                description: "Filter by show date (Y-m-d)",
                required: false,
                schema: new OA\Schema(type: "string", format: "date", example: "2026-04-04")
            ),
            new OA\Parameter(
                name: "status",
                in: "query",
                description: "Filter by status",
                required: false,
                schema: new OA\Schema(type: "string", enum: ["scheduled", "ongoing", "completed", "cancelled"], example: "scheduled")
            )
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
                                    new OA\Property(property: "id", type: "string", format: "uuid", example: "019d20c8-c81a-70d8-bc76-ef970ddb4a31"),
                                    new OA\Property(property: "movie_id", type: "string", format: "uuid", example: "019d1b15-14c1-7195-abf2-33f252dc10b6"),
                                    new OA\Property(property: "room_id", type: "string", format: "uuid", example: "019d1506-609d-70cf-8bbe-3d3d21f36a66"),
                                    new OA\Property(property: "show_date", type: "string", format: "date", example: "2026-04-04"),
                                    new OA\Property(property: "start_time", type: "string", example: "2026-04-04 10:00:00"),
                                    new OA\Property(property: "end_time", type: "string", example: "2026-04-04 13:18:00"),
                                    new OA\Property(property: "base_price", type: "string", example: "100000.00"),
                                    new OA\Property(property: "status", type: "string", enum: ["scheduled", "ongoing", "completed", "cancelled"], example: "scheduled"),
                                    new OA\Property(property: "cancelled_reason", type: "string", nullable: true, example: null),
                                    new OA\Property(property: "cancelled_by", type: "string", format: "uuid", nullable: true, example: null),
                                    new OA\Property(property: "cancelled_at", type: "string", format: "date-time", nullable: true, example: null),
                                    new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-03-24T16:58:42.000000Z"),
                                    new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2026-03-24T16:58:42.000000Z"),
                                    new OA\Property(property: "deleted_at", type: "string", format: "date-time", nullable: true, example: null),
                                    new OA\Property(
                                        property: "movie",
                                        type: "object",
                                        properties: [
                                            new OA\Property(property: "id", type: "string", format: "uuid", example: "019d1b15-14c1-7195-abf2-33f252dc10b6"),
                                            new OA\Property(property: "title", type: "string", example: "Báu vật trời cho 2"),
                                            new OA\Property(property: "slug", type: "string", example: "bau-vat-troi-cho"),
                                            new OA\Property(property: "duration_minutes", type: "integer", example: 198),
                                            new OA\Property(property: "poster_url", type: "string", format: "uri", example: "https://i.pinimg.com/1200x/b1/a4/b7/b1a4b797511f432e508bd0377f316c57.jpg"),
                                            new OA\Property(property: "trailer_url", type: "string", format: "uri", example: "https://i.pinimg.com/736x/38/ac/35/38ac350c758e5b2d950d37f503aa33ce.jpg"),
                                            new OA\Property(property: "description", type: "string", example: "Mô tả"),
                                            new OA\Property(property: "content", type: "string", example: "Nội dung"),
                                            new OA\Property(property: "release_date", type: "string", format: "date", nullable: true, example: null),
                                            new OA\Property(property: "rating", type: "number", format: "float", nullable: true, example: null),
                                            new OA\Property(property: "language", type: "string", example: "Tiếng việt"),
                                            new OA\Property(property: "status", type: "string", example: "coming_soon"),
                                            new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-03-23T14:24:19.000000Z"),
                                            new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2026-03-23T14:47:48.000000Z"),
                                            new OA\Property(property: "deleted_at", type: "string", format: "date-time", nullable: true, example: null),
                                        ]
                                    ),
                                    new OA\Property(
                                        property: "room",
                                        type: "object",
                                        properties: [
                                            new OA\Property(property: "id", type: "string", format: "uuid", example: "019d1506-609d-70cf-8bbe-3d3d21f36a66"),
                                            new OA\Property(property: "cinema_id", type: "string", format: "uuid", example: "019d1501-2b7e-7386-8e76-79a7470c3a2c"),
                                            new OA\Property(property: "name", type: "string", example: "ROOM 1"),
                                            new OA\Property(property: "type", type: "string", example: "2D"),
                                            new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-03-22T10:10:32.000000Z"),
                                            new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2026-03-22T10:14:08.000000Z"),
                                            new OA\Property(property: "deleted_at", type: "string", format: "date-time", nullable: true, example: null),
                                        ]
                                    ),
                                    new OA\Property(
                                        property: "prices",
                                        type: "array",
                                        items: new OA\Items(
                                            properties: [
                                                new OA\Property(property: "id", type: "string", format: "uuid", example: "019d20c8-c8b5-73e7-a71b-830902dcb82f"),
                                                new OA\Property(property: "showtime_id", type: "string", format: "uuid", example: "019d20c8-c81a-70d8-bc76-ef970ddb4a31"),
                                                new OA\Property(property: "seat_type_id", type: "string", format: "uuid", example: "019d1666-27cd-7089-8240-8d92a425dfd1"),
                                                new OA\Property(property: "price", type: "string", example: "100000.00"),
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
                                new OA\Property(property: "current_page_url", type: "string", format: "uri", example: "http://localhost/api/v1/showtimes?page=1"),
                                new OA\Property(property: "first_page_url", type: "string", format: "uri", example: "http://localhost/api/v1/showtimes?page=1"),
                                new OA\Property(property: "last_page_url", type: "string", format: "uri", example: "http://localhost/api/v1/showtimes?page=1"),
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
        $movieId = $query['movie_id'] ?? null;
        $roomId = $query['room_id'] ?? null;
        $showDate = $query['show_date'] ?? null;
        $status = $query['status'] ?? null;

        $showtimes = $this->showtimeService->paginate($limit, $movieId, $roomId, $showDate, $status);

        return $this->successList($showtimes->items(), $this->paginationMeta($showtimes));
    }

    #[OA\Get(
        path: "/api/v1/public/showtimes",
        summary: "Get public showtime list",
        description: "Returns only showtimes for public movies and public showtime statuses. Cancelled and completed showtimes are never returned.",
        tags: ["Public"],
        parameters: [
            new OA\Parameter(name: "limit", in: "query", required: false, schema: new OA\Schema(type: "integer", example: 15)),
            new OA\Parameter(name: "page", in: "query", required: false, schema: new OA\Schema(type: "integer", example: 1)),
            new OA\Parameter(name: "movie_id", in: "query", required: false, description: "Filter by movie", schema: new OA\Schema(type: "string", format: "uuid")),
            new OA\Parameter(name: "cinema_id", in: "query", required: false, description: "Filter by cinema", schema: new OA\Schema(type: "string", format: "uuid")),
            new OA\Parameter(name: "city_id", in: "query", required: false, description: "Filter by city", schema: new OA\Schema(type: "string", format: "uuid")),
            new OA\Parameter(name: "cinema_chain_id", in: "query", required: false, description: "Filter by cinema chain", schema: new OA\Schema(type: "string", format: "uuid")),
            new OA\Parameter(name: "show_date", in: "query", required: false, description: "Exact show date", schema: new OA\Schema(type: "string", format: "date", example: "2026-05-18")),
            new OA\Parameter(name: "from_date", in: "query", required: false, description: "Start show date", schema: new OA\Schema(type: "string", format: "date", example: "2026-05-18")),
            new OA\Parameter(name: "to_date", in: "query", required: false, description: "End show date", schema: new OA\Schema(type: "string", format: "date", example: "2026-05-25")),
            new OA\Parameter(name: "status", in: "query", required: false, description: "Filter by public showtime status", schema: new OA\Schema(type: "string", enum: ["scheduled", "ongoing"], example: "scheduled")),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Public showtime list retrieved successfully",
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
                                    new OA\Property(property: "movie_id", type: "string", format: "uuid"),
                                    new OA\Property(property: "room_id", type: "string", format: "uuid"),
                                    new OA\Property(property: "show_date", type: "string", format: "date"),
                                    new OA\Property(property: "start_time", type: "string", example: "2026-05-18 10:00:00"),
                                    new OA\Property(property: "end_time", type: "string", example: "2026-05-18 12:00:00"),
                                    new OA\Property(property: "base_price", type: "string", example: "100000.00"),
                                    new OA\Property(property: "status", type: "string", enum: ["scheduled", "ongoing"]),
                                    new OA\Property(property: "movie", type: "object"),
                                    new OA\Property(property: "room", type: "object"),
                                    new OA\Property(property: "prices", type: "array", items: new OA\Items(type: "object")),
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
        $movieId = $query['movie_id'] ?? null;
        $cinemaId = $query['cinema_id'] ?? null;
        $cityId = $query['city_id'] ?? null;
        $cinemaChainId = $query['cinema_chain_id'] ?? null;
        $showDate = $query['show_date'] ?? null;
        $fromDate = $query['from_date'] ?? null;
        $toDate = $query['to_date'] ?? null;
        $status = $query['status'] ?? null;

        $showtimes = $this->showtimeService->getPublicShowtimes($limit, $movieId, $cinemaId, $cityId, $cinemaChainId, $showDate, $fromDate, $toDate, $status);

        return $this->successList($showtimes->items(), $this->paginationMeta($showtimes));
    }

    #[OA\Get(
        path: "/api/v1/public/showtimes/{id}",
        summary: "Get public showtime detail",
        description: "Returns one public showtime with movie, room, cinema, city, cinema chain, and prices.",
        tags: ["Public"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "string", format: "uuid")),
        ],
        responses: [
            new OA\Response(response: 200, description: "Public showtime retrieved successfully"),
            new OA\Response(response: 404, description: "Showtime not found"),
        ]
    )]
    public function showPublic($id)
    {
        $showtime = $this->showtimeService->findPublic($id);

        return $this->success($showtime);
    }

    #[OA\Post(
        path: "/api/v1/showtimes",
        summary: "Create a new showtime",
        tags: ["Showtime"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["movie_id", "room_id", "show_date", "start_time", "base_price", "prices"],
                properties: [
                    new OA\Property(property: "movie_id", type: "string", format: "uuid", example: "019d1b15-14c1-7195-abf2-33f252dc10b6"),
                    new OA\Property(property: "room_id", type: "string", format: "uuid", example: "019d1506-609d-70cf-8bbe-3d3d21f36a66"),
                    new OA\Property(property: "show_date", type: "string", format: "date", example: "2026-04-04"),
                    new OA\Property(property: "start_time", type: "string", example: "2026-04-04 10:00:00", description: "Format: Y-m-d H:i:s"),
                    new OA\Property(property: "base_price", type: "number", format: "float", example: 100000),
                    new OA\Property(
                        property: "prices",
                        type: "array",
                        items: new OA\Items(
                            required: ["seat_type_id", "price"],
                            properties: [
                                new OA\Property(property: "seat_type_id", type: "string", format: "uuid", example: "019d1666-27cd-7089-8240-8d92a425dfd1"),
                                new OA\Property(property: "price", type: "number", format: "float", example: 100000),
                            ]
                        )
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Showtime created successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Tạo suất chiếu thành công!"),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(property: "id", type: "string", format: "uuid", example: "019d20c8-c81a-70d8-bc76-ef970ddb4a31"),
                                new OA\Property(property: "movie_id", type: "string", format: "uuid", example: "019d1b15-14c1-7195-abf2-33f252dc10b6"),
                                new OA\Property(property: "room_id", type: "string", format: "uuid", example: "019d1506-609d-70cf-8bbe-3d3d21f36a66"),
                                new OA\Property(property: "show_date", type: "string", format: "date", example: "2026-04-04"),
                                new OA\Property(property: "start_time", type: "string", example: "2026-04-04 10:00:00"),
                                new OA\Property(property: "end_time", type: "string", example: "2026-04-04 13:18:00"),
                                new OA\Property(property: "base_price", type: "string", example: "100000.00"),
                                new OA\Property(property: "status", type: "string", example: "scheduled"),
                                new OA\Property(property: "cancelled_reason", type: "string", nullable: true, example: null),
                                new OA\Property(property: "cancelled_by", type: "string", format: "uuid", nullable: true, example: null),
                                new OA\Property(property: "cancelled_at", type: "string", format: "date-time", nullable: true, example: null),
                                new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-03-24T16:58:42.000000Z"),
                                new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2026-03-24T16:58:42.000000Z"),
                                new OA\Property(property: "deleted_at", type: "string", format: "date-time", nullable: true, example: null),
                                new OA\Property(
                                    property: "prices",
                                    type: "array",
                                    items: new OA\Items(
                                        properties: [
                                            new OA\Property(property: "id", type: "string", format: "uuid", example: "019d20c8-c8b5-73e7-a71b-830902dcb82f"),
                                            new OA\Property(property: "showtime_id", type: "string", format: "uuid", example: "019d20c8-c81a-70d8-bc76-ef970ddb4a31"),
                                            new OA\Property(property: "seat_type_id", type: "string", format: "uuid", example: "019d1666-27cd-7089-8240-8d92a425dfd1"),
                                            new OA\Property(property: "price", type: "string", example: "100000.00"),
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
                                new OA\Property(property: "movie_id", type: "array", items: new OA\Items(type: "string", example: "The movie id field is required.")),
                                new OA\Property(property: "room_id", type: "array", items: new OA\Items(type: "string", example: "The room id field is required.")),
                                new OA\Property(property: "show_date", type: "array", items: new OA\Items(type: "string", example: "The show date must be a date after or equal to today.")),
                                new OA\Property(property: "start_time", type: "array", items: new OA\Items(type: "string", example: "The start time does not match the format Y-m-d H:i:s.")),
                                new OA\Property(property: "base_price", type: "array", items: new OA\Items(type: "string", example: "The base price field is required.")),
                                new OA\Property(property: "prices", type: "array", items: new OA\Items(type: "string", example: "The prices field is required.")),
                                new OA\Property(property: "prices.0.seat_type_id", type: "array", items: new OA\Items(type: "string", example: "The prices.0.seat_type_id field is required.")),
                                new OA\Property(property: "prices.0.price", type: "array", items: new OA\Items(type: "string", example: "The prices.0.price field is required.")),
                            ]
                        ),
                    ]
                )
            ),
        ]
    )]
    public function create(ShowtimeRequest $request)
    {
        $data = $request->validated();

        $showtime = $this->showtimeService->create($data);

        return $this->success($showtime, 'Tạo thời gian chiếu thành công!', 201);
    }
    #[OA\Get(
        path: "/api/v1/showtimes/{id}",
        summary: "Get showtime by ID",
        tags: ["Showtime"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "Showtime ID",
                schema: new OA\Schema(type: "string", format: "uuid", example: "019d20c8-c81a-70d8-bc76-ef970ddb4a31")
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Showtime retrieved successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Success"),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(property: "id", type: "string", format: "uuid", example: "019d20c8-c81a-70d8-bc76-ef970ddb4a31"),
                                new OA\Property(property: "movie_id", type: "string", format: "uuid", example: "019d1b15-14c1-7195-abf2-33f252dc10b6"),
                                new OA\Property(property: "room_id", type: "string", format: "uuid", example: "019d1506-609d-70cf-8bbe-3d3d21f36a66"),
                                new OA\Property(property: "show_date", type: "string", format: "date", example: "2026-04-04"),
                                new OA\Property(property: "start_time", type: "string", example: "2026-04-04 10:00:00"),
                                new OA\Property(property: "end_time", type: "string", example: "2026-04-04 13:18:00"),
                                new OA\Property(property: "base_price", type: "string", example: "100000.00"),
                                new OA\Property(property: "status", type: "string", enum: ["scheduled", "ongoing", "completed", "cancelled"], example: "scheduled"),
                                new OA\Property(property: "cancelled_reason", type: "string", nullable: true, example: null),
                                new OA\Property(property: "cancelled_by", type: "string", format: "uuid", nullable: true, example: null),
                                new OA\Property(property: "cancelled_at", type: "string", format: "date-time", nullable: true, example: null),
                                new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-03-24T16:58:42.000000Z"),
                                new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2026-03-24T16:58:42.000000Z"),
                                new OA\Property(property: "deleted_at", type: "string", format: "date-time", nullable: true, example: null),
                                new OA\Property(
                                    property: "movie",
                                    type: "object",
                                    properties: [
                                        new OA\Property(property: "id", type: "string", format: "uuid", example: "019d1b15-14c1-7195-abf2-33f252dc10b6"),
                                        new OA\Property(property: "title", type: "string", example: "Báu vật trời cho 2"),
                                        new OA\Property(property: "slug", type: "string", example: "bau-vat-troi-cho"),
                                        new OA\Property(property: "duration_minutes", type: "integer", example: 198),
                                        new OA\Property(property: "poster_url", type: "string", format: "uri", example: "https://i.pinimg.com/1200x/b1/a4/b7/b1a4b797511f432e508bd0377f316c57.jpg"),
                                        new OA\Property(property: "trailer_url", type: "string", format: "uri", example: "https://i.pinimg.com/736x/38/ac/35/38ac350c758e5b2d950d37f503aa33ce.jpg"),
                                        new OA\Property(property: "description", type: "string", example: "Mô tả"),
                                        new OA\Property(property: "content", type: "string", example: "Nội dung"),
                                        new OA\Property(property: "release_date", type: "string", format: "date", nullable: true, example: null),
                                        new OA\Property(property: "rating", type: "number", format: "float", nullable: true, example: null),
                                        new OA\Property(property: "language", type: "string", example: "Tiếng việt"),
                                        new OA\Property(property: "status", type: "string", example: "coming_soon"),
                                        new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-03-23T14:24:19.000000Z"),
                                        new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2026-03-23T14:47:48.000000Z"),
                                        new OA\Property(property: "deleted_at", type: "string", format: "date-time", nullable: true, example: null),
                                    ]
                                ),
                                new OA\Property(
                                    property: "room",
                                    type: "object",
                                    properties: [
                                        new OA\Property(property: "id", type: "string", format: "uuid", example: "019d1506-609d-70cf-8bbe-3d3d21f36a66"),
                                        new OA\Property(property: "cinema_id", type: "string", format: "uuid", example: "019d1501-2b7e-7386-8e76-79a7470c3a2c"),
                                        new OA\Property(property: "name", type: "string", example: "ROOM 1"),
                                        new OA\Property(property: "type", type: "string", example: "2D"),
                                        new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-03-22T10:10:32.000000Z"),
                                        new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2026-03-22T10:14:08.000000Z"),
                                        new OA\Property(property: "deleted_at", type: "string", format: "date-time", nullable: true, example: null),
                                    ]
                                ),
                                new OA\Property(
                                    property: "prices",
                                    type: "array",
                                    items: new OA\Items(
                                        properties: [
                                            new OA\Property(property: "id", type: "string", format: "uuid", example: "019d20c8-c8b5-73e7-a71b-830902dcb82f"),
                                            new OA\Property(property: "showtime_id", type: "string", format: "uuid", example: "019d20c8-c81a-70d8-bc76-ef970ddb4a31"),
                                            new OA\Property(property: "seat_type_id", type: "string", format: "uuid", example: "019d1666-27cd-7089-8240-8d92a425dfd1"),
                                            new OA\Property(property: "price", type: "string", example: "100000.00"),
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
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Showtime not found",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Không tìm thấy suất chiếu!"),
                    ]
                )
            ),
        ]
    )]
    public function show($id)
    {
        $showtime = $this->showtimeService->find($id);

        return $this->success($showtime);
    }

    #[OA\Get(
        path: "/api/v1/showtimes/{id}/seat-overview",
        summary: "Get seat overview by showtime",
        description: "Admin endpoint. Returns all seats in the showtime room with availability status and private booking or hold details when available.",
        tags: ["Showtime"],
        security: [["sanctum" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "Showtime ID",
                schema: new OA\Schema(type: "string", format: "uuid", example: "019d20c8-c81a-70d8-bc76-ef970ddb4a31")
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Seat overview retrieved successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Success"),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(property: "showtime", type: "object"),
                                new OA\Property(
                                    property: "summary",
                                    type: "object",
                                    properties: [
                                        new OA\Property(property: "total", type: "integer", example: 96),
                                        new OA\Property(property: "booked", type: "integer", example: 18),
                                        new OA\Property(property: "held", type: "integer", example: 4),
                                        new OA\Property(property: "available", type: "integer", example: 74),
                                    ]
                                ),
                                new OA\Property(
                                    property: "seats",
                                    type: "array",
                                    items: new OA\Items(
                                        properties: [
                                            new OA\Property(property: "id", type: "string", format: "uuid"),
                                            new OA\Property(property: "room_id", type: "string", format: "uuid"),
                                            new OA\Property(property: "seat_type_id", type: "string", format: "uuid"),
                                            new OA\Property(property: "row_label", type: "string", example: "A"),
                                            new OA\Property(property: "seat_number", type: "integer", example: 1),
                                            new OA\Property(property: "label", type: "string", example: "A1"),
                                            new OA\Property(property: "status", type: "string", enum: ["available", "held", "booked"], example: "available"),
                                            new OA\Property(property: "seat_type", type: "object", nullable: true),
                                            new OA\Property(
                                                property: "booking",
                                                type: "object",
                                                nullable: true,
                                                description: "Present only for booked seats.",
                                                properties: [
                                                    new OA\Property(property: "id", type: "string", format: "uuid"),
                                                    new OA\Property(property: "booking_code", type: "string", example: "BK-20260522-ABC123"),
                                                    new OA\Property(property: "status", type: "string", example: "confirmed"),
                                                    new OA\Property(property: "user", type: "object", nullable: true),
                                                ]
                                            ),
                                            new OA\Property(
                                                property: "hold",
                                                type: "object",
                                                nullable: true,
                                                description: "Present only for held seats.",
                                                properties: [
                                                    new OA\Property(property: "id", type: "string", format: "uuid"),
                                                    new OA\Property(property: "expired_at", type: "string", format: "date-time"),
                                                    new OA\Property(property: "user", type: "object", nullable: true),
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
            new OA\Response(response: 401, description: "Unauthenticated"),
            new OA\Response(response: 404, description: "Showtime not found"),
        ]
    )]
    public function seatOverview($id)
    {
        $data = $this->showtimeService->seatOverview($id);

        return $this->success($data);
    }

    #[OA\Get(
        path: "/api/v1/public/showtimes/{id}/seat-overview",
        summary: "Get public seat overview by showtime",
        description: "Public endpoint. Returns all seats in the showtime room with availability status only. Private booking and hold details are always hidden.",
        tags: ["Public"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "Showtime ID",
                schema: new OA\Schema(type: "string", format: "uuid", example: "019d20c8-c81a-70d8-bc76-ef970ddb4a31")
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Public seat overview retrieved successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Success"),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(property: "showtime", type: "object"),
                                new OA\Property(
                                    property: "summary",
                                    type: "object",
                                    properties: [
                                        new OA\Property(property: "total", type: "integer", example: 96),
                                        new OA\Property(property: "booked", type: "integer", example: 18),
                                        new OA\Property(property: "held", type: "integer", example: 4),
                                        new OA\Property(property: "available", type: "integer", example: 74),
                                    ]
                                ),
                                new OA\Property(
                                    property: "seats",
                                    type: "array",
                                    items: new OA\Items(
                                        properties: [
                                            new OA\Property(property: "id", type: "string", format: "uuid"),
                                            new OA\Property(property: "room_id", type: "string", format: "uuid"),
                                            new OA\Property(property: "seat_type_id", type: "string", format: "uuid"),
                                            new OA\Property(property: "row_label", type: "string", example: "A"),
                                            new OA\Property(property: "seat_number", type: "integer", example: 1),
                                            new OA\Property(property: "label", type: "string", example: "A1"),
                                            new OA\Property(property: "status", type: "string", enum: ["available", "held", "booked"], example: "available"),
                                            new OA\Property(property: "seat_type", type: "object", nullable: true),
                                            new OA\Property(property: "booking", type: "object", nullable: true, example: null),
                                            new OA\Property(property: "hold", type: "object", nullable: true, example: null),
                                        ]
                                    )
                                ),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(response: 404, description: "Showtime not found"),
        ]
    )]
    public function publicSeatOverview($id)
    {
        $data = $this->showtimeService->seatOverview($id, false);

        return $this->success($data);
    }

    #[OA\Put(
        path: "/api/v1/showtimes/{id}",
        summary: "Update showtime by ID",
        description: "Chỉ cho phép cập nhật base_price và prices. Không thể chỉnh movie, room, show_date, start_time sau khi tạo.",
        tags: ["Showtime"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "Showtime ID",
                schema: new OA\Schema(type: "string", format: "uuid", example: "019d20c8-c81a-70d8-bc76-ef970ddb4a31")
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "base_price", type: "number", format: "float", example: 120000),
                    new OA\Property(
                        property: "prices",
                        type: "array",
                        items: new OA\Items(
                            required: ["seat_type_id", "price"],
                            properties: [
                                new OA\Property(property: "seat_type_id", type: "string", format: "uuid", example: "019d1666-27cd-7089-8240-8d92a425dfd1"),
                                new OA\Property(property: "price", type: "number", format: "float", example: 120000),
                            ]
                        )
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Showtime updated successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Cập nhật suất chiếu thành công!"),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(property: "id", type: "string", format: "uuid", example: "019d20c8-c81a-70d8-bc76-ef970ddb4a31"),
                                new OA\Property(property: "movie_id", type: "string", format: "uuid", example: "019d1b15-14c1-7195-abf2-33f252dc10b6"),
                                new OA\Property(property: "room_id", type: "string", format: "uuid", example: "019d1506-609d-70cf-8bbe-3d3d21f36a66"),
                                new OA\Property(property: "show_date", type: "string", format: "date", example: "2026-04-04"),
                                new OA\Property(property: "start_time", type: "string", example: "2026-04-04 10:00:00"),
                                new OA\Property(property: "end_time", type: "string", example: "2026-04-04 13:18:00"),
                                new OA\Property(property: "base_price", type: "string", example: "120000.00"),
                                new OA\Property(property: "status", type: "string", example: "scheduled"),
                                new OA\Property(property: "cancelled_reason", type: "string", nullable: true, example: null),
                                new OA\Property(property: "cancelled_by", type: "string", format: "uuid", nullable: true, example: null),
                                new OA\Property(property: "cancelled_at", type: "string", format: "date-time", nullable: true, example: null),
                                new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-03-24T16:58:42.000000Z"),
                                new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2026-03-24T16:58:42.000000Z"),
                                new OA\Property(property: "deleted_at", type: "string", format: "date-time", nullable: true, example: null),
                                new OA\Property(
                                    property: "prices",
                                    type: "array",
                                    items: new OA\Items(
                                        properties: [
                                            new OA\Property(property: "id", type: "string", format: "uuid", example: "019d20c8-c8b5-73e7-a71b-830902dcb82f"),
                                            new OA\Property(property: "showtime_id", type: "string", format: "uuid", example: "019d20c8-c81a-70d8-bc76-ef970ddb4a31"),
                                            new OA\Property(property: "seat_type_id", type: "string", format: "uuid", example: "019d1666-27cd-7089-8240-8d92a425dfd1"),
                                            new OA\Property(property: "price", type: "string", example: "120000.00"),
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
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Showtime not found",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Không tìm thấy suất chiếu!"),
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
                                new OA\Property(property: "base_price", type: "array", items: new OA\Items(type: "string", example: "The base price must be a number.")),
                                new OA\Property(property: "prices", type: "array", items: new OA\Items(type: "string", example: "The prices must be an array.")),
                                new OA\Property(property: "prices.0.seat_type_id", type: "array", items: new OA\Items(type: "string", example: "The prices.0.seat_type_id field is required.")),
                                new OA\Property(property: "prices.0.price", type: "array", items: new OA\Items(type: "string", example: "The prices.0.price field is required.")),
                            ]
                        ),
                    ]
                )
            ),
        ]
    )]
    public function update(ShowtimeRequest $request, $id)
    {
        $data = $request->validated();

        $showtime = $this->showtimeService->update($id, $data);

        return $this->success($showtime, 'Cập nhật thời gian chiếu thành công!', 200);
    }
    #[OA\Delete(
        path: "/api/v1/showtimes/{id}",
        summary: "Delete showtime by ID",
        tags: ["Showtime"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "Showtime ID",
                schema: new OA\Schema(type: "string", format: "uuid", example: "019d20c8-c81a-70d8-bc76-ef970ddb4a31")
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Showtime deleted successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Xóa suất chiếu thành công!"),
                        new OA\Property(property: "data", type: "null", example: null),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Showtime not found",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Không tìm thấy suất chiếu!"),
                    ]
                )
            ),
        ]
    )]
    public function delete($id)
    {
        $this->showtimeService->delete($id);

        return $this->success(null, 'Xóa thời gian chiếu thành công!');
    }
}
