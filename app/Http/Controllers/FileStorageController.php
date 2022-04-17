<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FileStorageController extends Controller
{
    //
    public function store(Request $request)
    {
        $file = $request->file('foto');
        $nama_file = time() . "_" . $request->nama . '.' . $file->getClientOriginalExtension();
        if (!is_dir(public_path('img'))) {
            mkdir(public_path('img'), 0755);
        }
        $tujuan_upload = 'public/img/voucher/';
        $file->move($tujuan_upload, $nama_file);
        return Response()->json([
            'success' => true,
            'message' => 'Berhasil upload gambar',
            'data' => url($tujuan_upload . $nama_file),
        ], 201);
    }
}
