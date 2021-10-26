<?php

namespace App\Repositories;

use App\Repositories\Contracts\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserRepositoryInterface
{
    protected $entity;

    public function __construct(User $user)
    {
        $this->entity = $user;
    }

    public function register(array $data)
    {
        //$data['password'] = bcrypt($data['password']);

        return $this->entity->create($data);
    }

    public function auth(array $data)
    {
        $user = $this->entity->where('email', $data['email'])->first();

        if ($user && Hash::check($data['password'], $user->password)) {
            return $user->createToken($data['email'])->plainTextToken;
        }
    }

    public function getMe()
    {
        return Auth::user();
    }
}
