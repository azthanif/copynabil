<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Siswa extends Model
{
    use HasFactory;

    protected $table = 'siswa';

    protected $fillable = [
        'nis',
        'nama_lengkap',
        'jenis_kelamin',
        'tanggal_lahir',
        'kelas',
        'alamat',
        'status',
        'orang_tua_id',
        'kontak_orang_tua',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    public function orangTua(): BelongsTo
    {
        return $this->belongsTo(User::class, 'orang_tua_id');
    }

    public function catatan(): HasMany
    {
        return $this->hasMany(CatatanGuru::class);
    }

    public function kehadiran(): HasMany
    {
        return $this->hasMany(Kehadiran::class);
    }
}