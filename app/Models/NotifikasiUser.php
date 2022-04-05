<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotifikasiUser extends Model
{
    use HasFactory;
    protected $table = 'notifikasi_user';
    protected $fillable = [
        'id_user',
        'isi',
        'keterangan',
        'type',
    ];
}
