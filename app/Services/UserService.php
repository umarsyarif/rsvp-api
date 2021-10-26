<?php

namespace App\Services;

use App\Repositories\Contracts\UserRepositoryInterface;

class UserService
{

    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(array $data)
    {
        return $this->userRepository->register($data);
    }

    public function auth(array $data)
    {
        return $this->userRepository->auth($data);
    }

    public function getMe()
    {
        return $this->userRepository->getMe();
    }
}
