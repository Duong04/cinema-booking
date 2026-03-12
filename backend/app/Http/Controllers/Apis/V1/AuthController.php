<?php

namespace App\Http\Controllers\Apis\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Traits\ResponseHelper;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    use ResponseHelper;
    private $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    #[OA\Get(
        path: "/api/csrf-cookie",
        summary: "Get CSRF cookie",
        tags: ["Get CSRF"],
        responses: [
            new OA\Response(response: 204, description: "CSRF cookie set successfully"),
            new OA\Response(response: 500, description: "Internal server error"),
        ]
    )]

    #[OA\Post(
        path: "/api/v1/auth/register",
        summary: "Register a new user",
        tags: ["Auth"],
        parameters: [
            new OA\Parameter(name: "name", in: "query", required: true, schema: new OA\Schema(type: "string")),
            new OA\Parameter(name: "email", in: "query", required: true, schema: new OA\Schema(type: "string")),
            new OA\Parameter(name: "password", in: "query", required: true, schema: new OA\Schema(type: "string")),
        ],
        responses: [
            new OA\Response(response: 201, description: "Người dùng đã đăng ký thành công. Vui lòng kiểm tra email để xác thực tài khoản của bạn!"),
            new OA\Response(response: 422, description: "Validation errors"),
        ]
    )]
    public function register(RegisterRequest $request)
    {
        $data = $request->validated();
        $user = $this->authService->register($data);

        return $this->success($user, 'Người dùng đã đăng ký thành công. Vui lòng kiểm tra email để xác thực tài khoản của bạn!', 201);
    }

    public function verifyEmail($token) {
        $user = $this->authService->verifyEmail($token);

        return $this->success($user, 'Tài khoản đã được xác minh thành công!', 200);
    }

    #[OA\Post(
        path: "/api/v1/auth/login",
        summary: "Login account",
        tags: ["Auth"],
        parameters: [
            new OA\Parameter(name: "email", in: "query", required: true, schema: new OA\Schema(type: "string")),
            new OA\Parameter(name: "password", in: "query", required: true, schema: new OA\Schema(type: "string")),
        ],
        responses: [
            new OA\Response(response: 201, description: "Người dùng đã đăng nhập thành công!"),
            new OA\Response(response: 422, description: "Validation errors"),
        ]
    )]
    public function login(LoginRequest $request)
    {
        $data = $request->validated();

        $user = $this->authService->login($data);

        $request->session()->regenerate();

        return $this->success(null, 'Người dùng đã đăng nhập thành công!');
    }

    #[OA\Get(
        path: "/api/v1/auth/profile",
        summary: "Get authenticated user profile",
        tags: ["Auth"],
        security: [["sanctum" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Profile retrieved successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: ""),
                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [
                                new OA\Property(property: "id", type: "string", example: "01927..."),
                                new OA\Property(property: "name", type: "string", example: "John Doe"),
                                new OA\Property(property: "email", type: "string", example: "john@example.com"),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function profile(Request $request)
    {
        return $this->success($request->user());
    }

    #[OA\Post(
        path: "/api/v1/auth/logout",
        summary: "Logout account",
        tags: ["Auth"],
        responses: [
            new OA\Response(response: 201, description: "Đăng xuất hành công!"),
            new OA\Response(response: 500, description: "Đã có lỗi xảy ra"),
        ]
    )]
    public function logout(Request $request)
    {
        $this->authService->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return $this->success(null, 'Đã đăng xuất thành công!');
    }
}
