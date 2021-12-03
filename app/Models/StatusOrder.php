<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusOrder extends Model
{
    use HasFactory;
    protected $table = 'status_order';
    protected $fillable = [
        'id_order',
        'status',
        'jam',
        'tanggal'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'id', 'id_order');
    }
}
