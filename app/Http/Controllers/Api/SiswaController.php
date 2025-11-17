<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SiswaController extends Controller
{
    public function index(Request $request)
    {
        $keyword = (string) $request->query('keyword', '');
        $perPage = (int) $request->query('per_page', 10);
        $perPage = max(5, min($perPage, 50));

        $query = Siswa::with('orangTua')->latest();

        if ($keyword !== '') {
            $query->where(function ($builder) use ($keyword) {
                $builder->where('nama_lengkap', 'like', "%{$keyword}%")
                    ->orWhere('nis', 'like', "%{$keyword}%")
                    ->orWhere('kelas', 'like', "%{$keyword}%")
                    ->orWhereHas('orangTua', function ($parentQuery) use ($keyword) {
                        $parentQuery->where('nama', 'like', "%{$keyword}%")
                            ->orWhere('email', 'like', "%{$keyword}%");
                    });
            });
        }

        $students = $query->paginate($perPage);
        $students->getCollection()->transform(fn ($item) => $this->transformSiswa($item));

        return response()->json($students);
    }

    public function store(Request $request)
    {
        $data = $this->validatePayload($request);

        $orangTua = $this->resolveOrangTua($data['orang_tua_id']);
        if (!$orangTua) {
            return response()->json([
                'message' => 'User yang dipilih bukan akun Orang Tua yang valid'
            ], 422);
        }

        $siswa = DB::transaction(fn () => Siswa::create($data));

        return response()->json([
            'message' => 'Data siswa berhasil ditambahkan',
            'data' => $this->transformSiswa($siswa->load('orangTua')),
        ], 201);
    }

    public function show(Siswa $siswa)
    {
        return response()->json($this->transformSiswa($siswa->load('orangTua')));
    }

    public function update(Request $request, Siswa $siswa)
    {
        $data = $this->validatePayload($request, $siswa->id);

        $orangTua = $this->resolveOrangTua($data['orang_tua_id']);
        if (!$orangTua) {
            return response()->json([
                'message' => 'User yang dipilih bukan akun Orang Tua yang valid'
            ], 422);
        }

        DB::transaction(fn () => $siswa->update($data));

        return response()->json([
            'message' => 'Data siswa berhasil diperbarui',
            'data' => $this->transformSiswa($siswa->fresh('orangTua')),
        ]);
    }

    public function destroy(Siswa $siswa)
    {
        $siswa->delete();

        return response()->json([
            'message' => 'Data siswa berhasil dihapus'
        ]);
    }

    protected function validatePayload(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'nis' => [
                'required',
                'string',
                'max:50',
                Rule::unique('siswa', 'nis')->ignore($id),
            ],
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'jenis_kelamin' => ['required', Rule::in(['Laki-laki', 'Perempuan'])],
            'tanggal_lahir' => ['nullable', 'date'],
            'kelas' => ['nullable', 'string', 'max:100'],
            'alamat' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['Aktif', 'Cuti', 'Lulus'])],
            'orang_tua_id' => ['required', 'exists:users,id'],
            'kontak_orang_tua' => ['nullable', 'string', 'max:50'],
        ]);
    }

    protected function resolveOrangTua(int $orangTuaId): ?User
    {
        $orangTua = User::with('role')->find($orangTuaId);

        if (!$orangTua) {
            return null;
        }

        return $orangTua->role && $orangTua->role->nama_role === 'Orang Tua'
            ? $orangTua
            : null;
    }

    protected function transformSiswa(Siswa $siswa): array
    {
        return [
            'id' => $siswa->id,
            'nis' => $siswa->nis,
            'nama_lengkap' => $siswa->nama_lengkap,
            'jenis_kelamin' => $siswa->jenis_kelamin,
            'tanggal_lahir' => $siswa->tanggal_lahir?->toDateString(),
            'kelas' => $siswa->kelas,
            'alamat' => $siswa->alamat,
            'status' => $siswa->status,
            'kontak_orang_tua' => $siswa->kontak_orang_tua,
            'orang_tua' => $siswa->orangTua ? [
                'id' => $siswa->orangTua->id,
                'nama' => $siswa->orangTua->nama,
                'email' => $siswa->orangTua->email,
            ] : null,
            'created_at' => optional($siswa->created_at)->toDateTimeString(),
            'updated_at' => optional($siswa->updated_at)->toDateTimeString(),
        ];
    }
}
