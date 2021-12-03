<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoucherOrder extends Model
{
    use HasFactory;
    protected $table = 'voucher_order';
    protected $fillable = [
        'id_order',
        'id_voucher'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'id', 'id_order');
    }
    public function voucher()
    {
        return $this->hasOne(Voucher::class, 'id', 'id_voucher');
    }
}
