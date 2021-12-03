<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stok extends Model
{
    use HasFactory;
    protected $table = 'stok';
    protected $fillable = [
        'id_menu',
        'jumlah'
    ];

    public function menu()
    {
        return $this->hasOne(Menu::class, 'id', 'id_menu');
    }
}
