<?php 
namespace App\Services;

use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Mail\VerifyEmailMail;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Support\Facades\Mail;
use Str;
use Auth;

class AuthService {
    private $userRepository;
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(array $data)
    {
        $data['is_active'] = false;
        $data['email_verify_token'] = Str::random(60);
        $data['token_expired_at'] = now()->addHours(24);
        $data['role_id'] = '019d06bd-ff12-71c8-8234-aa2cc7733011';

        $user = $this->userRepository->create($data);

        Mail::to($user->email)->queue(new VerifyEmailMail($user));

        return true;
    }

    public function verifyEmail($token) {
        $user = $this->userRepository->getByToken($token);

        if (!$user) {
            throw new HttpException(400, 'Token không hợp lệ!');
        }

        if ($user->is_active) {
            throw new HttpException(409, 'Tài khoản đã được xác minh trước đó!');
        }

        if ($user->token_expired_at && now()->isAfter($user->token_expired_at)) {
            throw new HttpException(410, 'Token đã hết hạn, vui lòng yêu cầu gửi lại!');
        }

        $this->userRepository->update($user->id, [
            'is_active' => true,
            'email_verify_token' => null,
            'email_verified_at' => now(),
            'token_expired_at'    => null,
        ]);

        return true;
    }

    public function resendVerifyEmail($email) {
        $user = $this->userRepository->getByEmail($email);

        if (!$user) {
            throw new HttpException(404, 'Email không tồn tại!');
        }

        if ($user->is_active) {
            throw new HttpException(409, 'Tài khoản đã được xác minh!');
        }

        $newToken = Str::random(64);

        $user = $this->userRepository->update($user->id, [
            'email_verify_token' => $newToken,
            'token_expired_at'   => now()->addHours(24),
        ]);

        Mail::to($user->email)->send(new VerifyEmailMail($user));

        return true;
    }

    public function login($data) {
        if (!Auth::attempt($data)) {
            throw new HttpException(422, 'Email hoặc mật khẩu không đúng!');
        }

        $user = Auth::user();

        if (!$user->is_active) {
            throw new HttpException(400, 'Tài khoản chưa xác thực email!');
        }

        return Auth::user();
    }

    public function logout() {
        Auth::guard('web')->logout();

        return true;
    }
}