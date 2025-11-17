<?php

namespace App\Models;

// 1. Pastikan baris ini ada!
use Laravel\Sanctum\HasApiTokens; 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    // 2. Pastikan HasApiTokens ada di dalam array use ini!
    use HasApiTokens, HasFactory, Notifiable; 

    protected $fillable = [
        'nama',
        'email',
        'password',
        'role_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function berita(): HasMany
    {
        return $this->hasMany(Berita::class, 'penulis_id');
    }

    public function anak(): HasOne
    {
        return $this->hasOne(Siswa::class, 'orang_tua_id');
    }
}