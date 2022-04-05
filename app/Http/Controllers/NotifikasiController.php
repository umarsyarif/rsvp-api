<?php

namespace App\Http\Controllers;

use App\Models\NotifikasiUser;
use Illuminate\Http\Request;

class NotifikasiController extends Controller
{
    //
    public function getByUser($idUser)
    {
        $notifikasi = NotifikasiUser::where([['id_user', $idUser], ['seen', 0]])->get();
        return response()->json([
            'success' => true,
            'message' => "data notifikasi",
            'data' => $notifikasi
        ], 200);
    }
    public function update($id)
    {
        $notifikasi = NotifikasiUser::find($id);
        $notifikasi->seen = 1;
        $notifikasi->save();
        return response()->json([
            'success' => true,
            'message' => "data notifikasi",
            'data' => $notifikasi
        ], 200);
    }
}
