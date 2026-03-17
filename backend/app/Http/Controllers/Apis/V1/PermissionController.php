<?php

namespace App\Http\Controllers\Apis\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\PermissionRequest;
use App\Http\Requests\QueryRequest;
use App\Services\PermissionService;
use App\Traits\ResponseHelper;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class PermissionController extends Controller
{
    use ResponseHelper;

    private $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    #[OA\Get(
        path: "/api/v1/permissions",
        summary: "Get list of permissions",
        tags: ["Permission"],
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
                                    new OA\Property(property: "name", type: "string", example: "Post Management"),
                                    new OA\Property(property: "key", type: "string", example: "post_management"),
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
                                    )
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
    public function paginate(QueryRequest $requets)
    {
        $query = $requets->validated();
        $limit = $query['limit'] ?? 15;

        $permissions = $this->permissionService->paginate($limit);

        return $this->successList($permissions->items(), [
            'total' => $permissions->total(),
            'per_page' => $permissions->perPage(),
            'current_page' => $permissions->currentPage(),
            'last_page' => $permissions->lastPage(),
            'current_page_url' => $permissions->url($permissions->currentPage()),
            'first_page_url' => $permissions->url(1),
            'last_page_url' => $permissions->url($permissions->lastPage()),
            'next_page_url' => $permissions->nextPageUrl(),
            'prev_page_url' => $permissions->previousPageUrl(),
        ]);
    }

    #[OA\Post(
        path: "/api/v1/permissions",
        summary: "Create new permission",
        tags: ["Permission"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "key"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Post Management"),
                    new OA\Property(property: "key", type: "string", example: "post_management"),

                    new OA\Property(
                        property: "actions",
                        type: "array",
                        description: "List of action IDs",
                        items: new OA\Items(
                            properties: [
                                new OA\Property(property: "action_id", type: "string", example: "019cfc04-566e-734e-91f6-8d2470bfba30"),
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
                        new OA\Property(property: "message", type: "string", example: "Tạo quyền thành công!"),
                        new OA\Property(property: "data", type: "object"),
                    ]
                )
            )
        ]
    )]
    public function create(PermissionRequest $request)
    {
        $data = $request->validated();

        $permission = $this->permissionService->create($data);

        return $this->success($permission, 'Tạo quyền thành công!', 201);
    }

    #[OA\Get(
        path: "/api/v1/permissions/{id}",
        summary: "Get permission detail",
        tags: ["Permission"],
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
                                new OA\Property(property: "key", type: "string"),
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
        $permission = $this->permissionService->find($id);

        return $this->success($permission);
    }

    #[OA\Put(
        path: "/api/v1/permissions/{id}",
        summary: "Update permission",
        tags: ["Permission"],
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
                    new OA\Property(property: "name", type: "string", example: "Post Management"),
                    new OA\Property(property: "key", type: "string", example: "post_management"),

                    new OA\Property(
                        property: "actions",
                        type: "array",
                        description: "List of action IDs",
                        items: new OA\Items(
                            properties: [
                                new OA\Property(property: "action_id", type: "string", example: "019cfc04-566e-734e-91f6-8d2470bfba30"),
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
                        new OA\Property(property: "message", type: "string", example: "Cập nhật quyền thành công!"),
                        new OA\Property(property: "data", type: "object"),
                    ]
                )
            )
        ]
    )]
    public function update(PermissionRequest $request, $id)
    {
        $data = $request->validated();

        $permission = $this->permissionService->update($id, $data);

        return $this->success($permission, 'Cập nhật quyền thành công!', 200);
    }

    #[OA\Delete(
        path: "/api/v1/permissions/{id}",
        summary: "Delete permission",
        tags: ["Permission"],
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
                        new OA\Property(property: "message", type: "string", example: "Xóa quyền thành công!"),
                        new OA\Property(property: "data", type: "object", nullable: true, example: null),
                    ]
                )
            )
        ]
    )]
    public function delete($id)
    {
        $this->permissionService->delete($id);

        return $this->success(null, 'Xóa quyền thành công!');
    }
}
