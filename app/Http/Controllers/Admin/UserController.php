<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('department');

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(fn ($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%"));
        }

        $users = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $departments = Department::orderBy('name')->get();
        return view('admin.users.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'email', 'unique:users,email'],
            'password'      => ['required', 'string', 'min:8', 'confirmed'],
            'role'          => ['required', 'in:user,helpdesk,technician,admin'],
            'department_id' => ['required', 'exists:departments,id'],
            'is_active'     => ['boolean'],
        ]);

        $user = User::create([
            ...$validated,
            'password'  => Hash::make($validated['password']),
            'is_active' => $request->boolean('is_active', true),
        ]);

        AuditLog::record('user.created', $user, null, ['email' => $user->email, 'role' => $user->role]);

        return redirect()->route('admin.users.index')->with('success', "User {$user->name} berhasil dibuat.");
    }

    public function edit(User $user)
    {
        $departments = Department::orderBy('name')->get();
        return view('admin.users.edit', compact('user', 'departments'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password'      => ['nullable', 'string', 'min:8', 'confirmed'],
            'role'          => ['required', 'in:user,helpdesk,technician,admin'],
            'department_id' => ['required', 'exists:departments,id'],
            'is_active'     => ['boolean'],
        ]);

        $old = $user->only(['name', 'email', 'role', 'is_active']);

        $user->update([
            'name'          => $validated['name'],
            'email'         => $validated['email'],
            'role'          => $validated['role'],
            'department_id' => $validated['department_id'],
            'is_active'     => $request->boolean('is_active'),
            ...($validated['password'] ? ['password' => Hash::make($validated['password'])] : []),
        ]);

        AuditLog::record('user.updated', $user, $old, $user->only(['name', 'email', 'role', 'is_active']));

        return redirect()->route('admin.users.index')->with('success', "User {$user->name} berhasil diperbarui.");
    }

    public function toggleActive(User $user)
    {
        abort_if($user->id === auth()->id(), 403, 'Tidak dapat menonaktifkan akun sendiri.');

        $user->update(['is_active' => ! $user->is_active]);

        AuditLog::record('user.toggled_active', $user, null, ['is_active' => $user->is_active]);

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Akun {$user->name} berhasil {$status}.");
    }
}
