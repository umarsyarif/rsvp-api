<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use Tymon\JWTAuth\Contracts\JWTSubject;

class Pengguna extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'pengguna';
    protected $fillable = [
        'username',
        'password',
        'role',
        'email',
        'no_hp',
        'alamat',
        'nama',
        'is_verified',
        'poin'
    ];

    protected $hidden = [
        'password',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function riwayatPoin()
    {
        return $this->hasMany(RiwayatPoin::class, 'id_pengguna', 'id');
    }
    public function order()
    {
        return $this->hasMany(Order::class, 'id_pengguna', 'id');
    }
}
