<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Pengguna;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PenggunaController extends Controller
{
    protected $user;
 
    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
    }

    public function index()
    {
        $data = Pengguna::with('riwayatPoin', 'order.detailOrder.menu.satuan', 'order.statusOrder', 'order.voucherOrder.voucher')->paginate(10);
        return response()->json([
            'success' => true,
            'message' => 'Data User',
            'data' => $data
        ], 200);
    }

    public function allData(){
        $data = Pengguna::with('riwayatPoin', 'order.detailOrder.menu.satuan', 'order.statusOrder', 'order.voucherOrder.voucher')->get();
        return response()->json([
            'success' => true,
            'message' => 'Data Semua User',
            'data' => $data
        ], 200);
    }
    
    public function byID(Request $request)
    {
        $data = Pengguna::where('id', $request->id)->with('riwayatPoin', 'order.detailOrder.menu.satuan', 'order.statusOrder', 'order.voucherOrder.voucher')->first();
        if($data == null){
            return response()->json([
                'success' => false,
                'message' => 'ID Not Found',
            ], 404);
        }
        return response()->json([
            'success' => true,
            'message' => 'Data Pengguna By ID',
            'data' => $data
        ], 200);
    }

    // public function riwayatOrder(Request $request)
    // {
    //     $data = Order::where('id_pengguna', $request->id_pengguna)->whereHas('order.statusOrder', function(Builder $query) use($request){
    //         $query->where();
    //     });
    // }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:pengguna',
            'password' => 'required|min:8',
            'role' => 'required|in:admin,pelanggan',
            'email' => 'required|unique:pengguna',
            'no_hp' => 'required',
            'alamat' => 'required',
        ]);

        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        Pengguna::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'email' => $request->email,
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
            'is_verified' => false,
            'poin' => 0
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil Menambah Pengguna',
        ], 201);
    }

    public function update(Request $request)
    {
        $data = Pengguna::where('id', $request->id)->first();

        if($data == null){
            return response()->json([
                'success' => false,
                'message' => 'ID Not Found',
            ], 404);
        }

        Pengguna::where('id', $data->id)->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Berhasil Mengubah Data Pengguna',
        ], 200);
    }

    public function delete(Request $request)
    {
        $data = Pengguna::where('id', $request->id)->first();

        if($data == null){
            return response()->json([
                'success' => false,
                'message' => 'ID Not Found',
            ], 400);
        }
        Pengguna::where('id', $data->id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil Menghapus Pengguna',
        ], 204);
    }
}
