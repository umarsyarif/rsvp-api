<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatPoin extends Model
{
    use HasFactory;
    protected $table = 'riwayat_poin';
    protected $fillable = [
        'id_pengguna',
        'nominal',
        'tipe',
        'id_order',
    ];

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'id', 'id_pengguna');
    }
}
