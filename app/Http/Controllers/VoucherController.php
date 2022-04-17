<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use App\Models\RiwayatPoin;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VoucherController extends Controller
{
    protected $user;

    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
    }

    public function index()
    {
        $data = Voucher::paginate(10);
        return response()->json([
            'success' => true,
            'message' => 'Data Voucher',
            'data' => $data
        ], 200);
    }
    public function poinPengguna($idPengguna)
    {
        $pengguna = Pengguna::find($idPengguna);
        if (!$pengguna) {
            return response()->json([
                'success' => false,
                'message' => 'ID Not Found',
            ], 404);
        }
        $poin = RiwayatPoin::where('id_pengguna', $idPengguna)->latest()->get();
        return response()->json([
            'success' => true,
            'message' => 'Data Poin Pengguna',
            'data' => $poin
        ], 200);
    }

    public function allData(Request $request)
    {
        $data = Voucher::latest();
        if ($request->active != null) {
            $data = $data->where('is_active', 1);
        }
        return response()->json([
            'success' => true,
            'message' => 'Data Semua Voucher',
            'data' => $data->get()
        ], 200);
    }

    public function byID(Request $request)
    {
        $data = Voucher::where('id', $request->id)->first();
        if ($data == null) {
            return response()->json([
                'success' => false,
                'message' => 'ID Not Found',
            ], 404);
        }
        return response()->json([
            'success' => true,
            'message' => 'Data Voucher By ID',
            'data' => $data
        ], 200);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'label' => 'required|unique:voucher',
            'foto' => 'required|image',
            'diskon' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $file = $request->file('foto');
        $nama_file = time() . "_" . $request->nama . '.' . $file->getClientOriginalExtension();
        if (!is_dir(public_path('img'))) {
            mkdir(public_path('img'), 0755);
        }
        $tujuan_upload = 'public/img/voucher/';
        $file->move($tujuan_upload, $nama_file);

        Voucher::create([
            'label' => $request->label,
            'foto' => url($tujuan_upload . $nama_file),
            'diskon' => $request->diskon
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil Menambah Voucher',
        ], 201);
    }

    public function update(Request $request)
    {
        $data = Voucher::where('id', $request->id)->first();

        if ($data == null) {
            return response()->json([
                'success' => false,
                'message' => 'ID Not Found',
            ], 404);
        }
        Voucher::where('id', $data->id)->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Berhasil Mengubah Data Voucher',
        ], 200);
    }

    public function delete(Request $request)
    {
        $data = Voucher::where('id', $request->id)->first();

        if ($data == null) {
            return response()->json([
                'success' => false,
                'message' => 'ID Not Found',
            ], 400);
        }
        Voucher::where('id', $data->id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil Menghapus Voucher',
        ], 203);
    }
}
