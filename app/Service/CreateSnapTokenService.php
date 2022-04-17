<?php

namespace App\Service;

use App\Models\Pengguna;
use Midtrans\Snap;

class CreateSnapTokenService extends Midtrans
{
    protected $order;
    public function __construct($order)
    {
        parent::__construct();
        $this->order = $order;
    }
    public function getSnapToken()
    {
        $itemDetails = [];
        foreach ($this->order->detailOrder as $item) {
            $itemDetails[] = [
                'id' => $item->id_menu,
                'price' => $item->total / $item->jumlah,
                'quantity' => $item->jumlah,
                'name' => $item->menu->nama,
            ];
        }
        $customer = Pengguna::find($this->order->id_pengguna);
        $customerDetail = [
            'first_name' => $customer->nama,
            'email' => $customer->email,
            'phone' => $customer->no_hp,
            'address' => $customer->alamat,
        ];
        $voucher = $this->order->voucherOrder;
        if ($voucher) {
            $itemDetails[] = [
                'id' => 'voucher-' . $voucher->voucher->id,
                'price' => -$voucher->voucher->diskon,
                'quantity' => 1,
                'name' => $voucher->voucher->label,
            ];
        }
        $poinOrder = $this->order->poinOrder;
        if ($poinOrder) {
            $itemDetails[] = [
                'id' => 'poin-' . $poinOrder->id,
                'price' => -$poinOrder->nominal * 10000,
                'quantity' => 1,
                'name' => 'Redeem Poin',
            ];
        }
        $params = [
            'transaction_details' => [
                'order_id' => $this->order->id,
                'gross_amount' => $this->order->total,
            ],
            'item_details' => $itemDetails,
            'customer_details' => $customerDetail,
        ];
        $snapToken = Snap::getSnapToken($params);

        return $snapToken;
    }
}
