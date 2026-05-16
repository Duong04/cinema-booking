<?php

namespace App\Http\Controllers\Apis\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ActionRequest;
use App\Http\Requests\QueryRequest;
use App\Services\ActionService;
use App\Traits\PaginationTrait;
use App\Traits\ResponseHelper;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class ActionController extends Controller
{
    use ResponseHelper, PaginationTrait;

    private $actionService;

    public function __construct(ActionService $actionService)
    {
        $this->actionService = $actionService;
    }

    #[OA\Get(
        path: "/api/v1/actions",
        summary: "Get list of actions",
        tags: ["Action"],
        parameters: [
            new OA\Parameter(
                name: "limit",
                in: "query",
                description: "Number of items per page",
                required: false,
                schema: new OA\Schema(type: "integer", example: 15)
            ),
            new OA\Parameter(
                name: "page",
                in: "query",
                description: "Page number",
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
                                    new OA\Property(property: "id", type: "string", example: "019cfc04-566e-734e-91f6-8d2470bfba30"),
                                    new OA\Property(property: "name", type: "string", example: "Update"),
                                    new OA\Property(property: "key", type: "string", example: "update"),
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
                                new OA\Property(property: "total", type: "integer", example: 100),
                                new OA\Property(property: "per_page", type: "integer", example: 15),
                                new OA\Property(property: "current_page", type: "integer", example: 1),
                                new OA\Property(property: "last_page", type: "integer", example: 7),
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

        $actions = $this->actionService->paginate($limit);

        return $this->successList($actions->items(), $this->paginationMeta($actions));
    }

    #[OA\Post(
        path: "/api/v1/actions",
        summary: "Create new action",
        tags: ["Action"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "key"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "Update"),
                    new OA\Property(property: "key", type: "string", example: "update"),
                    new OA\Property(
                        property: "permissions",
                        type: "array",
                        description: "List of permission IDs",
                        items: new OA\Items(
                            properties: [
                                new OA\Property(property: "permission_id", type: "string", example: "019cfc04-566e-734e-91f6-8d2470bfba30"),
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
                        new OA\Property(property: "message", type: "string", example: "Tạo hành động thành công!"),
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
            )
        ]
    )]
    public function create(ActionRequest $request)
    {
        $data = $request->validated();

        $action = $this->actionService->create($data);

        return $this->success($action, 'Tạo hành động thành công!', 201);
    }

    #[OA\Get(
        path: "/api/v1/actions/{id}",
        summary: "Get action detail",
        tags: ["Action"],
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
        $action = $this->actionService->find($id);

        return $this->success($action);
    }

    #[OA\Put(
        path: "/api/v1/actions/{id}",
        summary: "Update action",
        tags: ["Action"],
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
                    new OA\Property(property: "name", type: "string", example: "Update"),
                    new OA\Property(property: "key", type: "string", example: "update"),
                    new OA\Property(
                        property: "permissions",
                        type: "array",
                        description: "List of permission IDs",
                        items: new OA\Items(
                            properties: [
                                new OA\Property(property: "permission_id", type: "string", example: "019cfc04-566e-734e-91f6-8d2470bfba30"),
                            ]
                        )
                    )
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
                        new OA\Property(property: "message", type: "string", example: "Cập nhật hành động thành công!"),
                        new OA\Property(property: "data", type: "object")
                    ]
                )
            )
        ]
    )]
    public function update(ActionRequest $request, $id)
    {
        $data = $request->validated();

        $action = $this->actionService->update($id, $data);

        return $this->success($action, 'Cập nhật hành động thành công!', 200);
    }

    #[OA\Delete(
        path: "/api/v1/actions/{id}",
        summary: "Delete action",
        tags: ["Action"],
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
                        new OA\Property(property: "message", type: "string", example: "Xóa hành động thành công!"),
                        new OA\Property(property: "data", type: "object", nullable: true, example: null),
                    ]
                )
            )
        ]
    )]
    public function delete($id)
    {
        $this->actionService->delete($id);

        return $this->success(null, 'Xóa hành động thành công!');
    }
}
