<?php

namespace App\Http\Controllers\Apis\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Traits\ResponseHelper;

class AuthController extends Controller
{
    use ResponseHelper;
    private $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request)
    {
        try {
            $data = $request->validated();
            $user = $this->authService->register($data);
            return $this->success($user, 'Người dùng đã đăng ký thành công. Vui lòng kiểm tra email để xác thực tài khoản của bạn.', 201);
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 500);
        }
    }
}
