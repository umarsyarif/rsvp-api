<?php

namespace App\Http\Controllers;

use App\Models\Stok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class StokController extends Controller
{
    protected $user;
 
    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
    }

    public function index()
    {
        $data = Stok::with('menu.satuan')->paginate(10);
        return response()->json([
            'success' => true,
            'message' => 'Data Stok',
            'data' => $data
        ], 200);
    }

    public function allData()
    {
        $data = Stok::with('menu.satuan')->get();
        return response()->json([
            'success' => true,
            'message' => 'Data Semua Stok',
            'data' => $data
        ], 200);
    }
    
    public function byID(Request $request)
    {
        $data = Stok::where('id', $request->id)->with('menu.satuan')->first();
        if($data == null){
            return response()->json([
                'success' => false,
                'message' => 'ID Not Found',
            ], 404);
        }
        return response()->json([
            'success' => true,
            'message' => 'Data Stok By ID',
            'data' => $data
        ], 200);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_menu' => 'required',
            'jumlah' => 'required',
        ]);

        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $stok = Stok::where('id_menu', $request->id_menu)->first();
        if($stok == null){
            Stok::create([
                'id_menu' => $request->id_menu,
                'jumlah' => $request->jumlah,
            ]);
        }else{
            $total = $stok->jumlah + $request->jumlah;
            Stok::where('id_menu', $request->id_menu)->update([
                'jumlah' => $total
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Berhasil Menambah Stok',
        ], 201);
    }

    public function update(Request $request)
    {
        $data = Stok::where('id', $request->id)->first();

        if($data == null){
            return response()->json([
                'success' => false,
                'message' => 'ID Not Found',
            ], 404);
        }
        Stok::where('id', $data->id)->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Berhasil Mengubah Data Stok',
        ], 200);
    }

    public function delete(Request $request)
    {
        $data = Stok::where('id', $request->id)->first();

        if($data == null){
            return response()->json([
                'success' => false,
                'message' => 'ID Not Found',
            ], 400);
        }
        Stok::where('id', $data->id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil Menghapus Stok',
        ], 204);
    }
}
