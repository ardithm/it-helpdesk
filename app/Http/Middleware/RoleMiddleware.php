<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Izinkan akses hanya jika role user cocok dengan salah satu role yang diizinkan.
     *
     * Contoh penggunaan di route:
     *   ->middleware('role:admin')
     *   ->middleware('role:admin,helpdesk')
     *   ->middleware('role:helpdesk,technician')
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Akun nonaktif tidak diizinkan masuk
        if (! $user->is_active) {
            auth()->logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'Akun Anda telah dinonaktifkan. Hubungi administrator.']);
        }

        // Periksa apakah role user ada di daftar role yang diizinkan
        if (! in_array($user->role, $roles)) {
            abort(403, 'Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        return $next($request);
    }
}
