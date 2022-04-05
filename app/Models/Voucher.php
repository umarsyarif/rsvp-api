<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;
    protected $table = 'voucher';
    protected $fillable = [
        'label',
        'foto',
        'diskon',
        'is_active'
    ];

    public function voucherOrder()
    {
        return $this->belongsTo(VoucherOrder::class, 'id_voucher', 'id');
    }
}
