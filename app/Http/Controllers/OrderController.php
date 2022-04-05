<?php

namespace App\Http\Controllers;

use App\Models\DetailOrder;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Pengguna;
use App\Models\RiwayatPoin;
use App\Models\StatusOrder;
use App\Models\Stok;
use App\Models\Voucher;
use App\Models\VoucherOrder;
use App\Service\BroadcastService;
use App\Service\BroadcastUtils;
use App\Service\CreateSnapTokenService;
use Exception;
use Illuminate\Foundation\Auth\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use League\CommonMark\Node\Query\OrExpr;

class OrderController extends Controller
{
    protected $user;

    public function __construct()
    {
        // $this->user = JWTAuth::parseToken()->authenticate();
    }

    public function index(Request $request)

    {
        $start = $request->start ?? date('Y-m-1 00:00:00');
        $end = $request->end ?? date('Y-m-d 23:59:59');
        $status = $request->status ?? 'diproses';
        $idPengguna = $request->idPengguna;
        $data = Order::select('order.*')
            ->join('status_order', 'status_order.id', '=', DB::raw('(SELECT id FROM status_order AS s2 WHERE s2.id_order = order.id ORDER BY created_at DESC LIMIT 1)'))
            ->with('statusOrder', 'detailOrder.menu.satuan', 'voucherOrder.voucher', 'pengguna.riwayatPoin')->orderBy('order.created_at', 'desc')
            ->whereBetween('order.created_at', [$start, $end]);
        if ($status == 'diproses') {
            $data = $data->whereIn('status_order.status', ['diproses', 'reschedule']);
        } else if ($status == 'selesai') {
            $data = $data->whereIn('status_order.status', ['selesai', 'dibatalkan']);
        } else {
            $data = $data->where('status_order.status', $status);
        }
        if ($idPengguna) {
            $data = $data->where('order.id_pengguna', $idPengguna);
        }
        return response()->json([
            'success' => true,
            'message' => 'Data Order',
            'data' => $data->get()
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
                $total = $subTotal - $diskon;
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
            // DB::rollBack();
            BroadcastService::sendNotif('idUser', $order->id_pengguna, 'Order', 'Order Berhasil', 'Pesanan anda sedang diproses');
            BroadcastService::saveNotifikasi($order->id_pengguna, 'Pesanan anda sedang diproses', json_encode(['id_order' => $order->id]), 'Order');
            $users = Pengguna::where('role', 'admin')->get();
            foreach ($users as $user) {
                BroadcastService::sendNotif('idUser', $user->id, 'Order', 'Pesanan Baru', 'Ada pesanan baru');
                BroadcastService::saveNotifikasi($user->id, 'Ada pesanan baru', json_encode(['id_order' => $order->id]), 'Order');
            }
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => "Order Berhasil Dilakukan",
                'data' => $order->id
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

            $order = Order::where('id', $request->id_order)->update([
                'jam' => $request->jam,
                'tanggal' => $request->tanggal
            ]);
        } else {
            StatusOrder::create([
                'id_order' => $request->id_order,
                'status' => $request->status,
            ]);
        }
        if ($request->status == 'selesai') {
            $poin = floor($order->total / 100000);
            $order = Order::find($request->id_order);
            BroadcastService::sendNotif('idUser', $order->id_pengguna, 'Order', 'Pesanan Selesai', 'Pesanan anda sudah selesai, Terima Kasih');
            BroadcastService::saveNotifikasi($order->id_pengguna, 'Pesanan anda sudah selesai, Terima Kasih', json_encode(['id_order' => $order->id]), 'Order');
            if ($poin > 0) {
                $user = Pengguna::find($order->id_pengguna);
                $user->update([
                    'poin' => $user->poin + $poin
                ]);
                RiwayatPoin::create([
                    'id_pengguna' => $order->id_pengguna,
                    'nominal' => $poin,
                    'tipe' => 'plus',
                ]);
            }
        }
        return response()->json([
            'success' => true,
            'message' => "Status Order Berhasil Diubah"
        ], 200);
    }
    public function createPayment(Request $request)
    {
        $order = Order::find($request->id);
        $snapToken = $order->snap_token;
        if (!$snapToken) {
            $midtrans = new CreateSnapTokenService($order);
            $snapToken = $midtrans->getSnapToken();
            $order->update([
                'snap_token' => $snapToken
            ]);
        }
        return view('payment', ['snapToken' => $snapToken]);
    }
    public function webhook(Request $request)
    {
        if ($request->transaction_status == 'capture' || $request->transaction_status == 'settlement') {
            $order = Order::find($request->order_id);
            BroadcastService::sendNotif('idUser', $order->id_pengguna, 'Order', 'Pembayaran Berhasil', 'Pembayaran anda berhasil');
            BroadcastService::saveNotifikasi($order->id_pengguna, 'Pembayaran anda berhasil', json_encode(['id_order' => $order->id]), 'Order');
            $users = Pengguna::where('role', 'admin')->get();
            foreach ($users as $user) {
                BroadcastService::sendNotif('idUser', $user->id, 'Order', 'Pesanan Dibayar', 'Ada pesanan yang sudah dibayar');
                BroadcastService::saveNotifikasi($user->id, 'Ada pesanan yang sudah dibayar', json_encode(['id_order' => $order->id]), 'Order');
            }
            StatusOrder::create([
                'id_order' => $order->id,
                'status' => 'sudah bayar',
            ]);
        } else if ($request->transaction_status == 'deny' || $request->transaction_status == 'cancel' || $request->transaction_status == 'expire') {
            $order = Order::find($request->order_id);
            BroadcastService::sendNotif('idUser', $order->id_pengguna, 'Order', 'Pesanan dibatalkan', 'Pesanan anda dibatalkan');
            BroadcastService::saveNotifikasi($order->id_pengguna, 'Ada pesanan yang sudah dibayar', json_encode(['id_order' => $order->id]), 'Order');
            $users = Pengguna::where('role', 'admin')->get();
            foreach ($users as $user) {
                BroadcastService::sendNotif('idUser', $user->id, 'Order', 'Pesanan Dibatalkan', 'Ada pesanan yang dibatalkan');
                BroadcastService::saveNotifikasi($user->id, 'Ada pesanan yang dibatalkan', json_encode(['id_order' => $order->id]), 'Order');
            }
            StatusOrder::create([
                'id_order' => $order->id,
                'status' => 'dibatalkan',
            ]);
        }
        return response()->json([
            'success' => true,
            'message' => $request->transaction_status,
        ], 201);
    }
    public function count($status)
    {
        $data = Order::select('order.*')
            ->join('status_order', 'status_order.id', '=', DB::raw('(SELECT id FROM status_order AS s2 WHERE s2.id_order = order.id ORDER BY created_at DESC LIMIT 1)'))
            ->with('statusOrder', 'detailOrder.menu.satuan', 'voucherOrder.voucher', 'pengguna.riwayatPoin')->orderBy('order.created_at', 'desc');
        if ($status == 'diproses') {
            $data = $data->whereIn('status_order.status', ['diproses', 'reschedule']);
        } else if ($status == 'selesai') {
            $data = $data->whereIn('status_order.status', ['selesai', 'dibatalkan']);
        } else {
            $data = $data->where('status_order.status', $status);
        }
        return response()->json([
            'success' => true,
            'message' => 'count Order',
            'data' => (int) $data->count()
        ], 200);
    }
}
