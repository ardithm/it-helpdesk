<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Halaman profil user yang sedang login.
     */
    public function show()
    {
        $user = auth()->user()->load('department');
        return view('profile.show', compact('user'));
    }

    /**
     * Update nama dan email profil.
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', \Illuminate\Validation\Rule::unique('users')->ignore($user->id)],
        ]);

        $old = $user->only(['name', 'email']);

        $user->update([
            'name'  => $request->name,
            'email' => $request->email,
        ]);

        AuditLog::record('profile.updated', $user, $old, $user->only(['name', 'email']));

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Ganti password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'password'         => ['required', 'min:8', 'confirmed'],
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'password.min'              => 'Password baru minimal 8 karakter.',
            'password.confirmed'        => 'Konfirmasi password tidak cocok.',
        ]);

        $user = auth()->user();

        if (! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        AuditLog::record('password.changed', $user);

        return back()->with('success', 'Password berhasil diubah.');
    }
}
