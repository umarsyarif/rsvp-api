<?php

namespace App\Repositories\Contracts;

interface UserRepositoryInterface
{
    public function register(array $data);
    public function auth(array $data);
    public function getMe();
}
