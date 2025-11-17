<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (!$user || !$user->relationLoaded('role')) {
            $user?->load('role');
        }

        $userRole = $user?->role?->nama_role;

        if (!$userRole || !in_array($userRole, $roles, true)) {
            return response()->json([
                'message' => 'Akses ditolak. Modul ini hanya tersedia untuk ' . implode(' / ', $roles)
            ], 403);
        }

        return $next($request);
    }
}
