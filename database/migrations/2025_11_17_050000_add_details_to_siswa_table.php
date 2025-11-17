<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('siswa', function (Blueprint $table) {
            $table->string('nis')->nullable()->unique()->after('id');
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan'])->default('Laki-laki')->after('nama_lengkap');
            $table->date('tanggal_lahir')->nullable()->after('jenis_kelamin');
            $table->string('kelas')->nullable()->after('tanggal_lahir');
            $table->text('alamat')->nullable()->after('kelas');
            $table->string('kontak_orang_tua')->nullable()->after('orang_tua_id');
        });

        DB::table('siswa')->select('id')->orderBy('id')->get()->each(function ($row) {
            DB::table('siswa')
                ->where('id', $row->id)
                ->update([
                    'nis' => 'TMP-' . str_pad((string) $row->id, 4, '0', STR_PAD_LEFT),
                ]);
        });
    }

    public function down(): void
    {
        Schema::table('siswa', function (Blueprint $table) {
            $table->dropColumn([
                'nis',
                'jenis_kelamin',
                'tanggal_lahir',
                'kelas',
                'alamat',
                'kontak_orang_tua',
            ]);
        });
    }
};
