<?php

namespace App\Http\Controllers\Apis\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\QueryRequest;
use App\Http\Requests\RoleRequest;
use App\Http\Resources\RoleResource;
use App\Traits\PaginationTrait;
use Illuminate\Http\Request;
use App\Services\RoleService;
use App\Traits\ResponseHelper;
use OpenApi\Attributes as OA;

class RoleController extends Controller
{
    use ResponseHelper, PaginationTrait;

    private $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    #[OA\Get(
        path: "/api/v1/roles",
        summary: "Get list of roles",
        tags: ["Role"],
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
                                    new OA\Property(property: "name", type: "string", example: "admin"),
                                    new OA\Property(property: "description", type: "string", nullable: true),
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
    public function paginate(QueryRequest $requet)
    {
        $query = $requet->validated();
        $limit = $query['limit'] ?? 15;
        $q = $query['q'] ?? null;

        $roles = $this->roleService->paginate($limit, $q);

        return $this->successList($roles->items(), $this->paginationMeta($roles));
    }

    #[OA\Post(
        path: "/api/v1/roles",
        summary: "Create new role",
        tags: ["Role"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "admin"),
                    new OA\Property(property: "description", type: "string", nullable: true),

                    new OA\Property(
                        property: "permissions",
                        type: "array",
                        items: new OA\Items(
                            properties: [
                                new OA\Property(property: "id", type: "string"),
                                new OA\Property(
                                    property: "actions",
                                    type: "array",
                                    items: new OA\Items(
                                        properties: [
                                            new OA\Property(property: "id", type: "string"),
                                        ]
                                    )
                                ),
                            ]
                        )
                    ),
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
                        new OA\Property(property: "message", type: "string", example: "Tạo vai trò thành công!"),
                        new OA\Property(property: "data", type: "object"),
                    ]
                )
            )
        ]
    )]
    public function create(RoleRequest $request)
    {
        $data = $request->validated();

        $role = $this->roleService->create($data);

        return $this->success($role, 'Tạo vai trò thành công!', 201);
    }

    #[OA\Get(
        path: "/api/v1/roles/{id}",
        summary: "Get role detail",
        tags: ["Role"],
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
                                new OA\Property(property: "description", type: "string", nullable: true),

                                new OA\Property(
                                    property: "permissions",
                                    type: "array",
                                    items: new OA\Items(
                                        properties: [
                                            new OA\Property(property: "id", type: "string"),
                                            new OA\Property(property: "name", type: "string"),
                                            new OA\Property(property: "key", type: "string"),

                                            new OA\Property(
                                                property: "actions",
                                                type: "array",
                                                items: new OA\Items(
                                                    properties: [
                                                        new OA\Property(property: "id", type: "string"),
                                                        new OA\Property(property: "name", type: "string"),
                                                        new OA\Property(property: "key", type: "string"),
                                                    ]
                                                )
                                            ),
                                        ]
                                    )
                                ),
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
        $role = $this->roleService->find($id);

        return $this->success(new RoleResource($role));
    }

    #[OA\Put(
        path: "/api/v1/roles/{id}",
        summary: "Update role",
        tags: ["Role"],
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
                    new OA\Property(property: "description", type: "string", nullable: true),

                    new OA\Property(
                        property: "permissions",
                        type: "array",
                        items: new OA\Items(
                            properties: [
                                new OA\Property(property: "id", type: "string"),
                                new OA\Property(
                                    property: "actions",
                                    type: "array",
                                    items: new OA\Items(
                                        properties: [
                                            new OA\Property(property: "id", type: "string"),
                                        ]
                                    )
                                ),
                            ]
                        )
                    ),
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
                        new OA\Property(property: "message", type: "string", example: "Cập nhật vai trò thành công!"),
                        new OA\Property(property: "data", type: "object"),
                    ]
                )
            )
        ]
    )]
    public function update(RoleRequest $request, $id)
    {
        $data = $request->validated();

        $role = $this->roleService->update($id, $data);

        return $this->success($role, 'Cập nhật vai trò thành công!', 200);
    }

    #[OA\Delete(
        path: "/api/v1/roles/{id}",
        summary: "Delete role",
        tags: ["Role"],
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
                        new OA\Property(property: "message", type: "string", example: "Xóa vai trò thành công!"),
                        new OA\Property(property: "data", type: "object", nullable: true, example: null),
                    ]
                )
            )
        ]
    )]
    public function delete($id)
    {
        $this->roleService->delete($id);

        return $this->success(null, 'Xóa vai trò thành công!');
    }
}
