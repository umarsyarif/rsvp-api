<?php

namespace App\Http\Controllers;

use App\Models\DetailOrder;
use App\Models\Menu;
use App\Models\Order;
use App\Models\StatusOrder;
use App\Models\Stok;
use App\Models\Voucher;
use App\Models\VoucherOrder;
use Exception;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    protected $user;

    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
    }

    public function index()
    {
        $data = Order::with('statusOrder', 'detailOrder.menu.satuan', 'voucherOrder.voucher', 'pengguna.riwayatPoin')->get();

        return response()->json([
            'success' => true,
            'message' => 'Data Order',
            'data' => $data
        ], 200);
    }

    public function byID($id)
    {
        $data = Order::where('id', $id)->with('statusOrder', 'detailOrder.menu.satuan', 'voucherOrder.voucher', 'pengguna.riwayatPoin')->first();
        if ($data == null) {
            return response()->json([
                'success' => false,
                'message' => 'ID Not Found',
            ], 404);
        }
        return response()->json([
            'success' => true,
            'message' => 'Data Order By ID',
            'data' => $data
        ], 200);
    }

    public function create(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            'id_pengguna' => 'required',
            'jumlah_orang' => 'required|min:1',
            'jam' => 'required',
            'tanggal' => 'required',
            'tipe' => 'required|in:dine in,take away',
            'id_menu' => 'required',
            'catatan' => 'required',
            'jumlah' => 'required',
            'id_voucher' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }
        try {
            DB::beginTransaction();

            $order = Order::create([
                'id_pengguna' => $request->id_pengguna,
                'jumlah_orang' => $request->jumlah_orang,
                'jam' => $request->jam,
                'tanggal' => $request->tanggal,
                'tipe' => $request->tipe,
            ]);

            $subTotal = 0;
            foreach ($request->id_menu as $key => $menu) {
                $dataMenu = Menu::where('id', $menu)->first();
                $diskon = (($dataMenu->harga * $dataMenu->diskon) / 100);
                $harga = $dataMenu->harga - $diskon;
                $hargaTotal = $harga * $request->jumlah[$key];
                $subTotal += $hargaTotal;
                DetailOrder::create([
                    'id_order' => $order->id,
                    'id_menu' => $menu,
                    'catatan' => $request->catatan[$key],
                    'jumlah' => $request->jumlah[$key],
                    'total' => $hargaTotal
                ]);

                $stok = Stok::where('id_menu', $menu)->first();
                $kurang = $stok->jumlah - $request->jumlah[$key];
                if ($kurang < 0) {
                    return response()->json([
                        'success' => false,
                        'message' => "Stok tidak cukup"
                    ], 422);
                } else {
                    Stok::where('id_menu', $menu)->update([
                        'jumlah' => $kurang,
                    ]);
                }
            }

            if ($request->id_voucher != "-") {
                $voucher = Voucher::where('id', $request->id_voucher)->first();
                $diskon = $voucher->diskon;
                $jumlahDiskon = ($subTotal * $diskon) / 100;
                $total = $subTotal - $jumlahDiskon;
                VoucherOrder::create([
                    'id_order' => $order->id,
                    'id_voucher' => $request->id_voucher,
                ]);
            } else {
                $diskon = 0;
                $total = $subTotal;
            }

            Order::where('id', $order->id)->update([
                'subtotal' => $subTotal,
                'diskon' => $diskon,
                'total' => $total
            ]);

            StatusOrder::create([
                'id_order' => $order->id,
                'status' => 'diproses'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Order Berhasil Dilakukan"
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function changeStatus(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            'id_order' => 'required',
            'status' => 'required|in:selesai,dibatalkan,reschedule,sudah bayar',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        if ($request->status == 'reschedule') {
            StatusOrder::create([
                'id_order' => $request->id_order,
                'status' => $request->status,
                'jam' => $request->jam,
                'tanggal' => $request->tanggal
            ]);

            Order::where('id', $request->id_order)->update([
                'jam' => $request->jam,
                'tanggal' => $request->tanggal
            ]);
        } else {
            StatusOrder::create([
                'id_order' => $request->id_order,
                'status' => $request->status,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => "Status Order Berhasil Diubah"
        ], 200);
    }
}
