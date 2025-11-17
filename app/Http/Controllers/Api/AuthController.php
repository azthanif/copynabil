<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Handle login request.
     */
    public function login(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 2. Cari user berdasarkan email
        $user = User::with('role')->where('email', $request->email)->first();

        // 3. Cek apakah user ada DAN passwordnya cocok
        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password yang Anda masukkan salah.'],
            ]);
        }

        // 4. Hapus token lama (opsional, agar satu device satu token)
        // $user->tokens()->delete();

        // 5. Buat token baru
        $token = $user->createToken('auth_token')->plainTextToken;

        // 6. Kembalikan response JSON
        return response()->json([
            'message' => 'Login berhasil',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }

    /**
     * Handle logout request.
     */
    public function logout(Request $request)
    {
        // Hapus token yang sedang digunakan saat ini
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil'
        ]);
    }
}