<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminKelolaSiswaTest extends TestCase
{
    use RefreshDatabase;

    protected Role $adminRole;
    protected Role $parentRole;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminRole = Role::create(['nama_role' => 'Admin']);
        $this->parentRole = Role::create(['nama_role' => 'Orang Tua']);
    }

    public function test_admin_can_crud_siswa(): void
    {
        Sanctum::actingAs($this->createAdminUser());

        $parent = $this->createParentUser();

        $createPayload = [
            'nis' => 'TP100',
            'nama_lengkap' => 'Siswa Baru',
            'jenis_kelamin' => 'Perempuan',
            'tanggal_lahir' => '2013-08-10',
            'kelas' => 'Kelas 6',
            'alamat' => 'Jl. Pendidikan 12',
            'status' => 'Aktif',
            'orang_tua_id' => $parent->id,
            'kontak_orang_tua' => '+628123456789'
        ];

        $createResponse = $this->postJson('/api/admin/siswa', $createPayload);
        $createResponse->assertCreated()
            ->assertJsonPath('data.nama_lengkap', 'Siswa Baru');

        $siswaId = $createResponse->json('data.id');

        $updateResponse = $this->putJson("/api/admin/siswa/{$siswaId}", array_merge($createPayload, [
            'status' => 'Cuti',
            'kelas' => 'Kelas 6B',
        ]));

        $updateResponse->assertOk()
            ->assertJsonPath('data.status', 'Cuti')
            ->assertJsonPath('data.kelas', 'Kelas 6B');

        $this->deleteJson("/api/admin/siswa/{$siswaId}")
            ->assertOk()
            ->assertJson(['message' => 'Data siswa berhasil dihapus']);
    }

    public function test_admin_can_filter_siswa_by_keyword(): void
    {
        Sanctum::actingAs($this->createAdminUser());

        $parent = $this->createParentUser();

        Siswa::create([
            'nis' => 'TP200',
            'nama_lengkap' => 'Alya Rahma',
            'jenis_kelamin' => 'Perempuan',
            'status' => 'Aktif',
            'orang_tua_id' => $parent->id,
        ]);

        $response = $this->getJson('/api/admin/siswa?keyword=Alya');
        $response->assertOk()
            ->assertJsonFragment(['nama_lengkap' => 'Alya Rahma']);
    }

    public function test_non_admin_cannot_access_siswa_module(): void
    {
        Sanctum::actingAs($this->createParentUser());

        $this->getJson('/api/admin/siswa')->assertForbidden();
    }

    protected function createAdminUser(): User
    {
        return User::factory()->create([
            'nama' => 'Admin',
            'email' => 'admin@example.com',
            'role_id' => $this->adminRole->id,
        ]);
    }

    protected function createParentUser(): User
    {
        return User::factory()->create([
            'nama' => 'Orang Tua',
            'email' => Str::uuid().'@example.com',
            'role_id' => $this->parentRole->id,
        ]);
    }
}
