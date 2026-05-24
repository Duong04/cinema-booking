<?php

namespace App\Http\Controllers\Apis\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentRequest;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class PaymentController extends Controller
{
    public function __construct(private PaymentService $paymentService)
    {
    }

    #[OA\Post(
        path: "/api/v1/payments",
        summary: "Create payment",
        tags: ["Payments"],
        security: [["sanctum" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["booking_id", "provider"],
                properties: [
                    new OA\Property(property: "booking_id", type: "string", format: "uuid"),
                    new OA\Property(property: "provider", type: "string", enum: ["vnpay", "momo", "zalopay", "cashier"]),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Payment created"),
            new OA\Response(response: 422, description: "Booking cannot be paid"),
        ]
    )]
    public function create(PaymentRequest $request): JsonResponse
    {
        $payment = $this->paymentService->create($request->validated(), auth()->id());

        return $this->success($payment, 'Tạo thanh toán thành công.', 201);
    }

    #[OA\Post(
        path: "/api/v1/payments/{id}/confirm",
        summary: "Confirm payment",
        tags: ["Payments"],
        security: [["sanctum" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "string", format: "uuid")),
        ],
        responses: [
            new OA\Response(response: 200, description: "Payment confirmed"),
            new OA\Response(response: 422, description: "Payment cannot be confirmed"),
        ]
    )]
    public function confirm(string $id): JsonResponse
    {
        $payment = $this->paymentService->confirm($id, auth()->id());

        return $this->success($payment, 'Thanh toán thành công.');
    }
}
