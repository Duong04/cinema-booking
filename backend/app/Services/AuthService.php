<?php 
namespace App\Services;

use App\Mail\VerifyEmailMail;
use App\Repositories\UserRepository\UserRepositoryInterface;
use Illuminate\Support\Facades\Mail;
use Str;
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
        $data['role_id'] = '019cd38b-7fd5-726c-8ab0-81e7c17fabc5';

        $user = $this->userRepository->create($data);

        Mail::to($user->email)->queue(new VerifyEmailMail($user));

        return $user;
    }
}