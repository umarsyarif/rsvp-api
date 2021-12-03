<?php

namespace App\Http\Controllers;

use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Satuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SatuanController extends Controller
{
    protected $user;
 
    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
    }

    public function index()
    {
        $data = Satuan::paginate(10);
        return response()->json([
            'success' => true,
            'message' => 'Data Satuan',
            'data' => $data
        ], 200);
    }

    public function allData()
    {
        $data = Satuan::get();
        return response()->json([
            'success' => true,
            'message' => 'Data Semua Satuan',
            'data' => $data
        ], 200);
    }
    
    public function byID(Request $request)
    {
        $data = Satuan::where('id', $request->id)->first();
        if($data == null){
            return response()->json([
                'success' => false,
                'message' => 'ID Not Found',
            ], 404);
        }
        return response()->json([
            'success' => true,
            'message' => 'Data Satuan By ID',
            'data' => $data
        ], 200);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|unique:satuan',
        ]);

        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        Satuan::create([
            'nama' => $request->nama,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil Menambah Satuan',
        ], 201);
    }

    public function update(Request $request)
    {
        $data = Satuan::where('id', $request->id)->first();

        if($data == null){
            return response()->json([
                'success' => false,
                'message' => 'ID Not Found',
            ], 404);
        }
        Satuan::where('id', $data->id)->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Berhasil Mengubah Data Satuan',
        ], 200);
    }

    public function delete(Request $request)
    {
        $data = Satuan::where('id', $request->id)->first();

        if($data == null){
            return response()->json([
                'success' => false,
                'message' => 'ID Not Found',
            ], 400);
        }
        Satuan::where('id', $data->id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil Menghapus Satuan',
        ], 204);
    }
}
