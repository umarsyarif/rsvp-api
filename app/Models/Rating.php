<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Rating extends Model
{
    use HasFactory;
    protected $table = 'rating';
    protected $fillable = [
        'id_user',
        'rating',
        'catatan'
    ];
    public function user(): HasOne
    {
        return $this->hasOne(Pengguna::class, 'id', 'id_user');
    }
}
