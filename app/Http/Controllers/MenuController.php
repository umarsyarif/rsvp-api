<?php

namespace App\Http\Controllers;

use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Menu;
use App\Models\Stok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MenuController extends Controller
{
    protected $user;

    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
    }

    public function index()
    {
        $data = Menu::with('satuan')->paginate(10);
        return response()->json([
            'success' => true,
            'message' => 'Data Menu',
            'data' => $data
        ], 200);
    }

    public function byID(Request $request)
    {
        $data = Menu::where('id', $request->id)->with('satuan')->first();
        if ($data == null) {
            return response()->json([
                'success' => false,
                'message' => 'ID Not Found',
            ], 404);
        }
        return response()->json([
            'success' => true,
            'message' => 'Data Menu By ID',
            'data' => $data
        ], 200);
    }

    public function allData(Request $request)
    {
        $data = Menu::with('satuan', 'stok')->orderBy('nama');
        if ($request->active != null) {
            $data = $data->where('is_active', 1);
        }
        return response()->json([
            'success' => true,
            'message' => 'Data Semua Menu',
            'data' => $data->get()
        ], 200);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|unique:menu',
            'foto' => 'required|image',
            'harga' => 'required',
            'diskon' => 'required',
            'id_satuan' => 'required',
            'tipe' => 'required|in:makanan,minuman'
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
        $menu = Menu::create([
            'nama' => $request->nama,
            'foto' => url($tujuan_upload . $nama_file),
            'harga' => $request->harga,
            'diskon' => $request->diskon,
            'id_satuan' => $request->id_satuan,
            'tipe' => $request->tipe
        ]);
        Stok::create([
            'id_menu' => $menu->id,
            'jumlah' => $request->stok
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Berhasil Menambah Menu',
        ], 201);
    }

    public function update(Request $request)
    {
        $data = Menu::where('id', $request->id)->first();

        if ($data == null) {
            return response()->json([
                'success' => false,
                'message' => 'ID Not Found',
            ], 404);
        }
        Menu::where('id', $data->id)->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Berhasil Mengubah Data Menu',
        ], 200);
    }

    public function delete(Request $request)
    {
        $data = Menu::where('id', $request->id)->first();

        if ($data == null) {
            return response()->json([
                'success' => false,
                'message' => 'ID Not Found',
            ], 400);
        }
        Menu::where('id', $data->id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil Menghapus Menu',
        ], 204);
    }
}
