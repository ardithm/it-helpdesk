<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetHistory;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AssetController extends Controller
{
    public function index(Request $request)
    {
        $query = Asset::with(['user', 'department']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(fn ($q) => $q->where('asset_code', 'like', "%{$search}%")
                ->orWhere('brand', 'like', "%{$search}%")
                ->orWhere('model', 'like', "%{$search}%")
                ->orWhere('serial_number', 'like', "%{$search}%"));
        }

        $assets      = $query->orderBy('asset_code')->paginate(15)->withQueryString();
        $departments = Department::orderBy('name')->get();

        return view('admin.assets.index', compact('assets', 'departments'));
    }

    public function create()
    {
        $departments = Department::orderBy('name')->get();
        $users       = User::where('is_active', true)->orderBy('name')->get();
        return view('admin.assets.create', compact('departments', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'asset_code'    => ['required', 'string', 'max:50', 'unique:assets,asset_code'],
            'category'      => ['required', 'string', 'max:100'],
            'brand'         => ['required', 'string', 'max:100'],
            'model'         => ['required', 'string', 'max:100'],
            'serial_number' => ['required', 'string', 'max:100'],
            'user_id'       => ['nullable', 'exists:users,id'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'purchase_date' => ['nullable', 'date'],
            'status'        => ['required', 'in:active,maintenance,retired'],
        ]);

        $asset = Asset::create($validated);

        AssetHistory::create([
            'asset_id'   => $asset->id,
            'ticket_id'  => null,
            'action'     => 'Aset ditambahkan',
            'description' => "Aset {$asset->asset_code} ({$asset->brand} {$asset->model}) ditambahkan ke inventaris.",
            'created_at' => now(),
        ]);

        return redirect()->route('admin.assets.index')->with('success', "Aset {$asset->asset_code} berhasil ditambahkan.");
    }

    public function edit(Asset $asset)
    {
        $departments = Department::orderBy('name')->get();
        $users       = User::where('is_active', true)->orderBy('name')->get();
        return view('admin.assets.edit', compact('asset', 'departments', 'users'));
    }

    public function update(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'asset_code'    => ['required', 'string', 'max:50', Rule::unique('assets')->ignore($asset->id)],
            'category'      => ['required', 'string', 'max:100'],
            'brand'         => ['required', 'string', 'max:100'],
            'model'         => ['required', 'string', 'max:100'],
            'serial_number' => ['required', 'string', 'max:100'],
            'user_id'       => ['nullable', 'exists:users,id'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'purchase_date' => ['nullable', 'date'],
            'status'        => ['required', 'in:active,maintenance,retired'],
        ]);

        $old = $asset->only(['status', 'user_id', 'department_id']);
        $asset->update($validated);

        if ($old['status'] !== $validated['status']) {
            AssetHistory::create([
                'asset_id'   => $asset->id,
                'ticket_id'  => null,
                'action'     => 'Status diubah',
                'description' => "Status aset diubah dari {$old['status']} menjadi {$validated['status']}.",
                'created_at' => now(),
            ]);
        }

        return redirect()->route('admin.assets.index')->with('success', "Aset {$asset->asset_code} berhasil diperbarui.");
    }

    public function show(Asset $asset)
    {
        $asset->load(['user', 'department', 'histories.ticket', 'tickets.user']);
        return view('admin.assets.show', compact('asset'));
    }
}
