<?php

namespace App\Http\Controllers\Apis\V1;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use App\Http\Requests\QueryRequest;
use App\Http\Requests\UserRequest;
use App\Traits\ResponseHelper;
use App\Traits\PaginationTrait;
use OpenApi\Attributes as OA;

class UserController extends Controller
{
    use ResponseHelper, PaginationTrait;

    public function __construct(
        private UserService $userService
    ) {
    }

    #[OA\Get(
        path: "/api/v1/users",
        summary: "Get list of users",
        tags: ["User"],
        security: [["sanctum" => []]],
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
                description: "Search by user name",
                required: false,
                schema: new OA\Schema(type: "string", example: "Nguyen")
            ),
            new OA\Parameter(
                name: "role_id",
                in: "query",
                description: "Filter users by role id. Use this to list customers only.",
                required: false,
                schema: new OA\Schema(type: "string", format: "uuid")
            ),
            new OA\Parameter(
                name: "ignore_role_id",
                in: "query",
                description: "Exclude users with this role id. Use this to list staff by excluding customer role.",
                required: false,
                schema: new OA\Schema(type: "string", format: "uuid")
            ),
            new OA\Parameter(
                name: "is_active",
                in: "query",
                description: "Filter by active status. Accepts 1 or 0.",
                required: false,
                schema: new OA\Schema(type: "boolean", example: true)
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
                                    new OA\Property(property: "id", type: "string", format: "uuid"),
                                    new OA\Property(property: "name", type: "string", example: "Nguyen Van A"),
                                    new OA\Property(property: "email", type: "string", format: "email", example: "user@example.com"),
                                    new OA\Property(property: "phone", type: "string", nullable: true, example: "0901234567"),
                                    new OA\Property(property: "avatar", type: "string", nullable: true, example: "https://example.com/avatar.jpg"),
                                    new OA\Property(property: "date_of_birth", type: "string", format: "date", nullable: true, example: "1998-06-18"),
                                    new OA\Property(property: "gender", type: "string", nullable: true, enum: ["male", "female", "other"], example: "male"),
                                    new OA\Property(property: "is_active", type: "boolean", example: true),
                                    new OA\Property(
                                        property: "role",
                                        type: "object",
                                        nullable: true,
                                        properties: [
                                            new OA\Property(property: "id", type: "string", format: "uuid"),
                                            new OA\Property(property: "name", type: "string", example: "staff"),
                                            new OA\Property(property: "description", type: "string", nullable: true),
                                        ]
                                    ),
                                ]
                            )
                        ),
                        new OA\Property(
                            property: "meta",
                            type: "object",
                            properties: [
                                new OA\Property(property: "total", type: "integer", example: 42),
                                new OA\Property(property: "per_page", type: "integer", example: 15),
                                new OA\Property(property: "current_page", type: "integer", example: 1),
                                new OA\Property(property: "last_page", type: "integer", example: 3),
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
            new OA\Response(response: 401, description: "Unauthenticated"),
            new OA\Response(response: 422, description: "Validation error"),
        ]
    )]
    public function paginate(QueryRequest $request)
    {
        $query = $request->validated();
        $limit = $query['limit'] ?? 15;
        $q = $query['q'] ?? null;
        $roleId = $query['role_id'] ?? null;
        $ignoreRoleId = $query['ignore_role_id'] ?? null;
        $isActive = $query['is_active'] ?? null;

        $users = $this->userService->paginate($limit, $q, $roleId, $ignoreRoleId, $isActive);

        return $this->successList($users->items(), $this->paginationMeta($users));
    }

    #[OA\Post(
        path: "/api/v1/users",
        summary: "Create new user",
        tags: ["User"],
        security: [["sanctum" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "email", "password", "role_id", "is_active"],
                properties: [
                    new OA\Property(property: "name", type: "string", maxLength: 255, example: "Nguyen Van A"),
                    new OA\Property(property: "email", type: "string", format: "email", example: "user@example.com"),
                    new OA\Property(property: "password", type: "string", minLength: 8, example: "password123"),
                    new OA\Property(property: "role_id", type: "string", format: "uuid"),
                    new OA\Property(property: "is_active", type: "boolean", example: true),
                    new OA\Property(property: "phone", type: "string", nullable: true, maxLength: 20, example: "0901234567"),
                    new OA\Property(property: "gender", type: "string", nullable: true, enum: ["male", "female", "other"], example: "male"),
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
                        new OA\Property(property: "message", type: "string", example: "Tạo người dùng thành công"),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(property: "id", type: "string", format: "uuid"),
                                new OA\Property(property: "name", type: "string", example: "Nguyen Van A"),
                                new OA\Property(property: "email", type: "string", format: "email", example: "user@example.com"),
                                new OA\Property(property: "phone", type: "string", nullable: true, example: "0901234567"),
                                new OA\Property(property: "gender", type: "string", nullable: true, enum: ["male", "female", "other"], example: "male"),
                                new OA\Property(property: "is_active", type: "boolean", example: true),
                                new OA\Property(property: "role_id", type: "string", format: "uuid"),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Unauthenticated"),
            new OA\Response(response: 422, description: "Validation error"),
        ]
    )]
    public function create(UserRequest $request)
    {
        $data = $request->validated();

        $user = $this->userService->create($data);

        return $this->success($user, 'Tạo người dùng thành công', 201);
    }

    #[OA\Get(
        path: "/api/v1/users/{id}",
        summary: "Get user detail",
        tags: ["User"],
        security: [["sanctum" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "string", format: "uuid")
            ),
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
                            type: "object",
                            properties: [
                                new OA\Property(property: "id", type: "string", format: "uuid"),
                                new OA\Property(property: "name", type: "string", example: "Nguyen Van A"),
                                new OA\Property(property: "email", type: "string", format: "email", example: "user@example.com"),
                                new OA\Property(property: "phone", type: "string", nullable: true, example: "0901234567"),
                                new OA\Property(property: "avatar", type: "string", nullable: true),
                                new OA\Property(property: "date_of_birth", type: "string", format: "date", nullable: true),
                                new OA\Property(property: "gender", type: "string", nullable: true, enum: ["male", "female", "other"], example: "male"),
                                new OA\Property(property: "is_active", type: "boolean", example: true),
                                new OA\Property(
                                    property: "role",
                                    type: "object",
                                    nullable: true,
                                    properties: [
                                        new OA\Property(property: "id", type: "string", format: "uuid"),
                                        new OA\Property(property: "name", type: "string", example: "staff"),
                                        new OA\Property(property: "description", type: "string", nullable: true),
                                    ]
                                ),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Unauthenticated"),
            new OA\Response(response: 404, description: "Resource not found"),
        ]
    )]
    public function show($id)
    {
        $user = $this->userService->find($id);

        return $this->success($user);
    }

    #[OA\Put(
        path: "/api/v1/users/{id}",
        summary: "Update user",
        description: "All fields are optional on update. Send only is_active to lock or unlock a user.",
        tags: ["User"],
        security: [["sanctum" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "string", format: "uuid")
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "name", type: "string", maxLength: 255, example: "Nguyen Van A"),
                    new OA\Property(property: "email", type: "string", format: "email", example: "user@example.com"),
                    new OA\Property(property: "password", type: "string", nullable: true, minLength: 8, example: "newpassword123"),
                    new OA\Property(property: "role_id", type: "string", format: "uuid"),
                    new OA\Property(property: "is_active", type: "boolean", example: false),
                    new OA\Property(property: "phone", type: "string", nullable: true, maxLength: 20, example: "0901234567"),
                    new OA\Property(property: "gender", type: "string", nullable: true, enum: ["male", "female", "other"], example: "female"),
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
                        new OA\Property(property: "message", type: "string", example: "Cập nhật người dùng thành công"),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(property: "id", type: "string", format: "uuid"),
                                new OA\Property(property: "name", type: "string", example: "Nguyen Van A"),
                                new OA\Property(property: "email", type: "string", format: "email", example: "user@example.com"),
                                new OA\Property(property: "phone", type: "string", nullable: true, example: "0901234567"),
                                new OA\Property(property: "gender", type: "string", nullable: true, enum: ["male", "female", "other"], example: "female"),
                                new OA\Property(property: "is_active", type: "boolean", example: false),
                                new OA\Property(property: "role_id", type: "string", format: "uuid"),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Unauthenticated"),
            new OA\Response(response: 404, description: "Resource not found"),
            new OA\Response(response: 422, description: "Validation error"),
        ]
    )]
    public function update(UserRequest $request, $id)
    {
        $data = $request->validated();

        $user = $this->userService->update($id, $data);

        return $this->success($user, 'Cập nhật người dùng thành công');
    }
}
