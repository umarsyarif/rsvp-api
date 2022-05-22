<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class RatController extends Controller
{
    protected $user;

    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
    }
    public function index()
    {
        $rating = Rating::with('user')->latest()->get();
        return response()->json([
            'success' => true,
            'message' => 'Data Rating',
            'data' => $rating
        ], 200);
    }
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_user' => 'required',
            'rating' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Data Rating Gagal Ditambahkan',
                'errors' => $validator->errors()
            ], 400);
        }
        $rating = Rating::create($request->all());
        return response()->json([
            'success' => true,
            'message' => 'Data Rating Berhasil Ditambahkan',
            'data' => $rating
        ], 201);
    }
    public function check($id)
    {
        $user = Rating::where('id_user', $id)->first();
        return response()->json([
            'success' => true,
            'message' => 'Data Rating',
            'data' => $user == null ? true : false
        ], 200);
    }
}
