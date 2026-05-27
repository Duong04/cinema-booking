<?php

namespace App\Http\Controllers\Apis\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\PromotionCheckRequest;
use App\Http\Requests\PromotionRequest;
use App\Http\Requests\QueryRequest;
use App\Services\PromotionService;
use App\Traits\PaginationTrait;
use App\Traits\ResponseHelper;
use OpenApi\Attributes as OA;

class PromotionController extends Controller
{
    use ResponseHelper, PaginationTrait;

    public function __construct(
        private PromotionService $promotionService
    ) {
    }

    #[OA\Get(
        path: "/api/v1/promotions",
        summary: "Get list of promotions",
        tags: ["Promotions"],
        security: [["sanctum" => []]],
        parameters: [
            new OA\Parameter(name: "limit", in: "query", required: false, schema: new OA\Schema(type: "integer", example: 15)),
            new OA\Parameter(name: "page", in: "query", required: false, schema: new OA\Schema(type: "integer", example: 1)),
            new OA\Parameter(name: "q", in: "query", required: false, schema: new OA\Schema(type: "string", example: "SALE")),
            new OA\Parameter(name: "status", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["active", "paused", "expired"])),
            new OA\Parameter(name: "applicable_to", in: "query", required: false, schema: new OA\Schema(type: "string", enum: ["booking", "ticket", "combo"])),
        ],
        responses: [
            new OA\Response(response: 200, description: "List retrieved successfully")
        ]
    )]
    public function paginate(QueryRequest $request)
    {
        $data = $request->validated();
        $limit = $data['limit'] ?? 15;
        $q = $data['q'] ?? null;
        $status = $data['status'] ?? null;
        $applicableTo = $data['applicable_to'] ?? null;

        $promotions = $this->promotionService->paginate($limit, $q, $status, $applicableTo);

        return $this->successList($promotions->items(), $this->paginationMeta($promotions));
    }

    #[OA\Post(
        path: "/api/v1/promotions",
        summary: "Create promotion",
        tags: ["Promotions"],
        security: [["sanctum" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["code", "discount_type", "discount_value", "start_date", "end_date", "applicable_to", "status"],
                properties: [
                    new OA\Property(property: "code", type: "string", example: "SALE10"),
                    new OA\Property(property: "description", type: "string", nullable: true),
                    new OA\Property(property: "discount_type", type: "string", enum: ["percentage", "fixed_amount"], example: "percentage"),
                    new OA\Property(property: "discount_value", type: "number", format: "float", example: 10),
                    new OA\Property(property: "start_date", type: "string", format: "date-time", example: "2026-05-25 00:00:00"),
                    new OA\Property(property: "end_date", type: "string", format: "date-time", example: "2026-06-25 23:59:59"),
                    new OA\Property(property: "usage_limit", type: "integer", nullable: true, example: 100),
                    new OA\Property(property: "per_user_limit", type: "integer", nullable: true, example: 1),
                    new OA\Property(property: "applicable_to", type: "string", enum: ["booking", "ticket", "combo"], example: "booking"),
                    new OA\Property(property: "status", type: "string", enum: ["active", "paused", "expired"], example: "active"),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Created successfully"),
            new OA\Response(response: 422, description: "Validation error")
        ]
    )]
    public function create(PromotionRequest $request)
    {
        $promotion = $this->promotionService->create($request->validated());

        return $this->success($promotion, 'Tạo mã khuyến mãi thành công!', 201);
    }

    #[OA\Get(
        path: "/api/v1/promotions/{id}",
        summary: "Get promotion detail",
        tags: ["Promotions"],
        security: [["sanctum" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "string", format: "uuid"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Success"),
            new OA\Response(response: 404, description: "Resource not found")
        ]
    )]
    public function show($id)
    {
        $promotion = $this->promotionService->find($id);

        return $this->success($promotion);
    }

    #[OA\Put(
        path: "/api/v1/promotions/{id}",
        summary: "Update promotion",
        tags: ["Promotions"],
        security: [["sanctum" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "string", format: "uuid"))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["code", "discount_type", "discount_value", "start_date", "end_date", "applicable_to", "status"],
                properties: [
                    new OA\Property(property: "code", type: "string", example: "SALE10"),
                    new OA\Property(property: "description", type: "string", nullable: true),
                    new OA\Property(property: "discount_type", type: "string", enum: ["percentage", "fixed_amount"], example: "fixed_amount"),
                    new OA\Property(property: "discount_value", type: "number", format: "float", example: 50000),
                    new OA\Property(property: "start_date", type: "string", format: "date-time"),
                    new OA\Property(property: "end_date", type: "string", format: "date-time"),
                    new OA\Property(property: "usage_limit", type: "integer", nullable: true),
                    new OA\Property(property: "per_user_limit", type: "integer", nullable: true),
                    new OA\Property(property: "applicable_to", type: "string", enum: ["booking", "ticket", "combo"]),
                    new OA\Property(property: "status", type: "string", enum: ["active", "paused", "expired"]),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Updated successfully"),
            new OA\Response(response: 422, description: "Validation error")
        ]
    )]
    public function update(PromotionRequest $request, $id)
    {
        $promotion = $this->promotionService->update($id, $request->validated());

        return $this->success($promotion, 'Cập nhật mã khuyến mãi thành công!');
    }

    #[OA\Delete(
        path: "/api/v1/promotions/{id}",
        summary: "Delete promotion",
        tags: ["Promotions"],
        security: [["sanctum" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "string", format: "uuid"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Deleted successfully")
        ]
    )]
    public function delete($id)
    {
        $this->promotionService->delete($id);

        return $this->success(null, 'Xóa mã khuyến mãi thành công!');
    }

    #[OA\Post(
        path: "/api/v1/promotions/check",
        summary: "Check promotion code",
        tags: ["Promotions"],
        security: [["sanctum" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["code"],
                properties: [
                    new OA\Property(property: "code", type: "string", example: "SALE10"),
                    new OA\Property(property: "ticket_amount", type: "number", format: "float", nullable: true, example: 180000),
                    new OA\Property(property: "combo_amount", type: "number", format: "float", nullable: true, example: 89000),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Promotion code is valid",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Mã khuyến mãi hợp lệ."),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(property: "promotion", type: "object"),
                                new OA\Property(property: "ticket_amount", type: "number", format: "float", example: 180000),
                                new OA\Property(property: "combo_amount", type: "number", format: "float", example: 89000),
                                new OA\Property(property: "subtotal", type: "number", format: "float", example: 269000),
                                new OA\Property(property: "discount_amount", type: "number", format: "float", example: 26900),
                                new OA\Property(property: "total_amount", type: "number", format: "float", example: 242100),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Unauthenticated"),
            new OA\Response(response: 422, description: "Invalid promotion code")
        ]
    )]
    public function check(PromotionCheckRequest $request)
    {
        $data = $this->promotionService->check($request->validated(), auth()->id());

        return $this->success($data, 'Mã khuyến mãi hợp lệ.');
    }
}
