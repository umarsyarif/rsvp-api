<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailOrder extends Model
{
    use HasFactory;
    protected $table = 'detail_order';
    protected $fillable = [
        'id_order',
        'id_menu',
        'catatan',
        'jumlah',
        'total'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'id', 'id_order');
    }
    public function menu()
    {
        return $this->hasOne(Menu::class, 'id', 'id_menu');
    }
}
