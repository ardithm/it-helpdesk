<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman login.
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user()->role);
        }

        return view('auth.login');
    }

    /**
     * Proses login.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        // Coba autentikasi
        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'Email atau password yang Anda masukkan salah.',
            ]);
        }

        $user = Auth::user();

        // Cek status akun
        if (! $user->is_active) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => 'Akun Anda telah dinonaktifkan. Hubungi administrator.',
            ]);
        }

        $request->session()->regenerate();

        // Catat audit log login
        AuditLog::record(
            action: 'auth.login',
            auditable: $user,
            newValues: ['role' => $user->role, 'email' => $user->email],
            userId: $user->id
        );

        return $this->redirectByRole($user->role);
    }

    /**
     * Proses logout.
     */
    public function logout(Request $request)
    {
        $user = Auth::user();

        // Catat audit log logout
        if ($user) {
            AuditLog::record(
                action: 'auth.logout',
                auditable: $user,
                userId: $user->id
            );
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil logout.');
    }

    /**
     * Arahkan user ke dashboard yang sesuai berdasarkan role.
     */
    private function redirectByRole(string $role): \Illuminate\Http\RedirectResponse
    {
        return match ($role) {
            'admin'      => redirect()->route('admin.dashboard'),
            'helpdesk'   => redirect()->route('helpdesk.dashboard'),
            'technician' => redirect()->route('technician.dashboard'),
            default      => redirect()->route('user.dashboard'),
        };
    }
}
