<?php

namespace App\Http\Controllers\Apis\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\PromotionCheckRequest;
use App\Services\PromotionService;
use App\Traits\ResponseHelper;
use OpenApi\Attributes as OA;

class PromotionController extends Controller
{
    use ResponseHelper;

    private $promotionService;

    public function __construct(PromotionService $promotionService)
    {
        $this->promotionService = $promotionService;
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
