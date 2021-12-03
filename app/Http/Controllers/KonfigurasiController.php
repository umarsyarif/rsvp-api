<?php

namespace App\Http\Controllers;

use App\Models\Konfigurasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class KonfigurasiController extends Controller
{
    protected $user;

    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
    }

    public function index()
    {
        $data = Konfigurasi::paginate(10);
        return response()->json([
            'success' => true,
            'message' => 'Data Konfigurasi',
            'data' => $data
        ], 200);
    }
    public function show($id)
    {
        $data = Konfigurasi::find($id);
        return response()->json([
            'success' => true,
            'message' => 'Data Konfigurasi',
            'data' => $data
        ], 200);
    }

    public function allData()
    {
        $data = Konfigurasi::get();
        return response()->json([
            'success' => true,
            'message' => 'Data Semua Konfigurasi',
            'data' => $data
        ], 200);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'buka' => 'required',
            'tutup' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }


        Konfigurasi::create([
            'buka' => $request->buka,
            'tutup' => $request->tutup,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil Menambah Konfigurasi',
        ], 201);
    }

    public function update(Request $request)
    {
        $data = Konfigurasi::where('id', $request->id)->first();

        if ($data == null) {
            return response()->json([
                'success' => false,
                'message' => 'ID Not Found',
            ], 404);
        }
        Konfigurasi::where('id', $data->id)->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Berhasil Mengubah Data Konfigurasi',
        ], 200);
    }

    public function delete(Request $request)
    {
        $data = Konfigurasi::where('id', $request->id)->first();

        if ($data == null) {
            return response()->json([
                'success' => false,
                'message' => 'ID Not Found',
            ], 400);
        }
        Konfigurasi::where('id', $data->id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil Menghapus Konfigurasi',
        ], 204);
    }
}
