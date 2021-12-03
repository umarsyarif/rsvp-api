<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        //valid credential
        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required|string|min:6|max:50',
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

        //Request is validated
        //Crean token
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Login credentials are invalid.',
                ], 400);
            }
        } catch (JWTException $e) {
            return $credentials;
            return response()->json([
                'success' => false,
                'message' => 'Could not create token.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Login Berhasil',
            'token' => $token,
            'user' => JWTAuth::user(),
        ]);
    }
    public function register(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'username' => 'required|unique:pengguna',
                'password' => 'required',
                'role' => 'required:in:admin,pelanggan',
                'email' => 'required|email|unique:pengguna',
                'alamat' => 'required',
                'no_hp' => 'required|numeric',
            ]
        );
        if ($validator->fails()) {
            return response(['message' => $validator->errors()->first()], 422);
        }
        $hashedPassword = Hash::make($request->password);
        Pengguna::create([
            'username' => $request->username,
            'password' => $hashedPassword,
            'role' => $request->role,
            'email' => $request->email,
            'alamat' => $request->alamat,
            'no_hp' => $request->no_hp,
            'is_verified' => true,
            'nama' => $request->nama,
            'poin' => 0,
        ]);
        return response([
            'message' => 'sukses register',

        ], 201);
    }
    public function logout(Request $request)
    {
        //valid credential
        $validator = Validator::make($request->only('token'), [
            'token' => 'required',
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

        //Request is validated, do logout
        try {
            JWTAuth::invalidate($request->token);

            return response()->json([
                'success' => true,
                'message' => 'User has been logged out',
            ]);
        } catch (JWTException $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, user cannot be logged out',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
