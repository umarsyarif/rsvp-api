<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $table = 'order';
    protected $fillable = [
        'id_pengguna',
        'jumlah_orang',
        'jam',
        'tanggal',
        'sub_total',
        'diskon',
        'total',
        'tipe',
        'snap_token'
    ];

    public function detailOrder()
    {
        return $this->hasMany(DetailOrder::class, 'id_order', 'id');
    }
    public function statusOrder()
    {
        return $this->hasMany(StatusOrder::class, 'id_order', 'id')->latest();
    }
    public function voucherOrder()
    {
        return $this->hasOne(VoucherOrder::class, 'id_order', 'id');
    }
    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna', 'id');
    }
}
