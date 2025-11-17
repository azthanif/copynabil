<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Siswa;
use Illuminate\Support\Facades\Hash;

class SiswaSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Pastikan Role Orang Tua ada
        $roleOrtu = Role::where('nama_role', 'Orang Tua')->first();

        // 2. Buat Akun Orang Tua (Ibu Amel)
        $ibuAmel = User::create([
            'nama' => 'Ibu Rezky Nur Amalia',
            'email' => 'amel@gmail.com', // Email untuk login
            'password' => Hash::make('password'),
            'role_id' => $roleOrtu->id
        ]);

        // 3. Buat Data Siswa (Hanif) yang terhubung ke Ibu Amel
        Siswa::create([
            'nis' => 'TP001',
            'nama_lengkap' => 'Hanif Jawahir',
            'jenis_kelamin' => 'Laki-laki',
            'tanggal_lahir' => '2014-02-11',
            'kelas' => 'Kelas 5',
            'alamat' => 'Jl. Anggrek No. 123, Makassar',
            'status' => 'Aktif',
            'orang_tua_id' => $ibuAmel->id,
            'kontak_orang_tua' => '+628111223344'
        ]);
    }
}