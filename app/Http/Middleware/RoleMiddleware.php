<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Cek apakah user sudah terautentikasi (Web Auth atau JWT Auth)
        if (!auth()->check() && !auth('api')->check()) {
            if ($request->is('api/*') || $request->wantsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('login');
        }

        // Ambil data user dari guard yang aktif
        $user = auth('api')->check() ? auth('api')->user() : auth()->user();

        // 2. Cek kesesuaian role user
        if ($user && $user->role && in_array($user->role->role_name, $roles)) {
            return $next($request);
        }

        // 3. JIKA ROLE TIDAK SESUAI
        // Jika URL diawali dengan 'api/', pastikan kembalikan JSON rapi (mencegah error panjang)
        if ($request->is('api/*') || $request->wantsJson()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Anda tidak memiliki hak akses untuk fitur ini.'
            ], 403); 
        }

        // Jika diakses via browser web biasa
        abort(403, 'Unauthorized action.');
    }
}