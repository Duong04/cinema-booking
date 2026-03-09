<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ResponseHelper
{
    /**
     * Response thành công
     */
    protected function success(
        mixed $data = null,
        string $message = 'Success',
        int $status = 200
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $status);
    }

    /**
     * Response danh sách có phân trang
     */
    protected function successList(
        array $items = [],
        array $meta = [],
        string $message = 'Success',
        int $status = 200
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $items,
            'meta' => $meta
        ], $status);
    }

    /**
     * Response khi tạo mới dữ liệu
     */
    protected function created(
        mixed $data = null,
        string $message = 'Created successfully'
    ): JsonResponse {
        return $this->success($data, $message, 201);
    }

    /**
     * Response khi xóa thành công (không trả dữ liệu)
     */
    protected function noContent(): JsonResponse
    {
        return response()->noContent();
    }

    /**
     * Response lỗi chung
     */
    protected function error(
        string $message = 'Something went wrong',
        int $status = 400,
        array $errors = []
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], $status);
    }

    /**
     * 404 Not Found
     */
    protected function notFound(
        string $message = 'Resource not found'
    ): JsonResponse {
        return $this->error($message, 404);
    }

    /**
     * 403 Forbidden
     */
    protected function forbidden(
        string $message = 'Forbidden'
    ): JsonResponse {
        return $this->error($message, 403);
    }

    /**
     * 401 Unauthorized
     */
    protected function unauthorized(
        string $message = 'Unauthorized'
    ): JsonResponse {
        return $this->error($message, 401);
    }

    /**
     * 422 Validation Error
     */
    protected function validationError(
        array $errors,
        string $message = 'Validation failed'
    ): JsonResponse {
        return $this->error($message, 422, $errors);
    }

    /**
     * 500 Server Error
     */
    protected function serverError(
        string $message = 'Internal server error'
    ): JsonResponse {
        return $this->error($message, 500);
    }
}
