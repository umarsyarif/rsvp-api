<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\UserService;
use App\Http\Requests\AuthRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\UserResource;

class AuthController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function register(StoreUserRequest $request)
    {
        if (!$user = $this->userService->register($request->all())) {
            return response()->json(['message' => 'failed to try to register user']);
        }
        return new UserResource($user);
    }

    public function auth(AuthRequest $request)
    {
        if (!$token = $this->userService->auth($request->All())) {
            return response()->json(['message' => 'invalid credentials'], 404);
        }

        return response()->json(['token' => $token]);
    }

    public function getMe()
    {
        $user = $this->userService->getMe();
        return new UserResource($user);
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        // Revoque All Tokens User

        $user->tokens()->delete();
        return response()->json(['message' => 'logged out']);
    }
}
