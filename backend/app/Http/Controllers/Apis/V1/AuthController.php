<?php

namespace App\Http\Controllers\Apis\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
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
            new OA\Response(response: 200, description: "Người dùng đã đăng nhập thành công!"),
            new OA\Response(response: 422, description: "Validation errors"),
        ]
    )]
    public function login(LoginRequest $request)
    {
        $data = $request->validated();

        $user = $this->authService->login($data);

        $request->session()->regenerate();

        return $this->success($user, 'Người dùng đã đăng nhập thành công!');
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
                        new OA\Property(property: "message", type: "string", example: "Success"),

                        new OA\Property(
                            property: "data",
                            type: "object",
                            properties: [

                                new OA\Property(property: "name", type: "string", example: "Sugar"),
                                new OA\Property(property: "email", type: "string", example: "tinhabc3009@gmail.com"),
                                new OA\Property(property: "phone", type: "string", nullable: true, example: null),
                                new OA\Property(property: "avatar", type: "string", example: "https://...png"),
                                new OA\Property(property: "date_of_birth", type: "string", nullable: true, example: null),
                                new OA\Property(property: "gender", type: "string", nullable: true, example: null),
                                new OA\Property(property: "is_active", type: "integer", example: 1),

                                new OA\Property(
                                    property: "role",
                                    type: "object",
                                    properties: [
                                        new OA\Property(property: "id", type: "string", example: "019cd38b-7fd5-726c-8ab0-81e7c17fabc5"),
                                        new OA\Property(property: "name", type: "string", example: "customer"),
                                        new OA\Property(property: "description", type: "string", example: "customer"),

                                        new OA\Property(
                                            property: "permissions",
                                            type: "array",
                                            items: new OA\Items(
                                                properties: [
                                                    new OA\Property(property: "id", type: "string", example: "019cfc0b-11f1-727d-a417-25a604446b3e"),
                                                    new OA\Property(property: "name", type: "string", example: "Post Management"),
                                                    new OA\Property(property: "key", type: "string", example: "post_management"),

                                                    new OA\Property(
                                                        property: "actions",
                                                        type: "array",
                                                        items: new OA\Items(
                                                            properties: [
                                                                new OA\Property(property: "id", type: "string", example: "019cfc04-566e-734e-91f6-8d2470bfba30"),
                                                                new OA\Property(property: "name", type: "string", example: "Update"),
                                                                new OA\Property(property: "key", type: "string", example: "update"),
                                                            ]
                                                        )
                                                    ),
                                                ]
                                            )
                                        ),
                                    ]
                                ),
                            ]
                        ),
                    ]
                )
            ),

            new OA\Response(
                response: 401,
                description: "Unauthenticated",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Unauthenticated"),
                    ]
                )
            ),
        ]
    )]
    public function profile(Request $request)
    {
        return $this->success(new UserResource($request->user()->load('role.permissions.actions')));
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
