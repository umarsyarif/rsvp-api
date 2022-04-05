<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;
    protected $table = 'menu';
    protected $fillable = [
        'nama',
        'foto',
        'harga',
        'diskon',
        'id_satuan',
        'tipe',
        'is_active'
    ];

    public function satuan()
    {
        return $this->hasOne(Satuan::class, 'id', 'id_satuan');
    }
    public function detailOrder()
    {
        return $this->belongsTo(detailOrder::class, 'id_menu', 'id');
    }
    public function stok()
    {
        return $this->belongsTo(Stok::class, 'id_menu', 'id');
    }
}
