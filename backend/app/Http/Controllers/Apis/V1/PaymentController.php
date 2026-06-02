<?php

namespace App\Http\Controllers\Apis\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentRequest;
use App\Http\Requests\QueryRequest;
use App\Services\PaymentService;
use OpenApi\Attributes as OA;
use App\Traits\PaginationTrait;
use App\Traits\ResponseHelper;

class PaymentController extends Controller
{
    use ResponseHelper, PaginationTrait;

    public function __construct(
        private PaymentService $paymentService
    ) {
    }

    public function paginate(QueryRequest $request)
    {
        $query = $request->validated();
        $limit = $query['limit'] ?? 15;
        $q = $query['q'] ?? null;
        $status = $query['status'] ?? null;
        $provider = $query['provider'] ?? null;
        $fromDate = $query['from_date'] ?? null;
        $toDate = $query['to_date'] ?? null;

        $payments = $this->paymentService->paginate($limit, $q, $status, $provider, $fromDate, $toDate);

        return $this->successList($payments->items(), $this->paginationMeta($payments));
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
                    new OA\Property(property: "provider", type: "string", enum: ["vnpay", "momo", "zalopay"]),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Payment created"),
            new OA\Response(response: 422, description: "Booking cannot be paid"),
        ]
    )]
    public function create(PaymentRequest $request)
    {
        $payment = $this->paymentService->create($request->validated(), auth()->id());

        return $this->success($payment, 'Tạo thanh toán thành công.', 201);
    }

    #[OA\Get(
        path: "/api/v1/payments/{id}",
        summary: "Get payment detail",
        tags: ["Payments"],
        security: [["sanctum" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "string", format: "uuid")),
        ],
        responses: [
            new OA\Response(response: 200, description: "Payment detail retrieved"),
            new OA\Response(response: 403, description: "Forbidden"),
            new OA\Response(response: 404, description: "Payment not found"),
        ]
    )]
    public function show(string $id)
    {
        $payment = $this->paymentService->find($id, auth()->id());

        return $this->success($payment);
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
    public function confirm(string $id)
    {
        $payment = $this->paymentService->confirm($id, auth()->id());

        return $this->success($payment, 'Thanh toán thành công.');
    }
}
