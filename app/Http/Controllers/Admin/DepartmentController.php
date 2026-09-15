<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::withCount('users')->orderBy('name')->paginate(15);
        return view('admin.departments.index', compact('departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => ['required', 'string', 'max:255', 'unique:departments,name'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        Department::create($request->only('name', 'description'));

        return back()->with('success', 'Departemen berhasil ditambahkan.');
    }

    public function update(Request $request, Department $department)
    {
        $request->validate([
            'name'        => ['required', 'string', 'max:255', \Illuminate\Validation\Rule::unique('departments')->ignore($department->id)],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $department->update($request->only('name', 'description'));

        return back()->with('success', 'Departemen berhasil diperbarui.');
    }

    public function destroy(Department $department)
    {
        abort_if($department->users()->exists(), 422, 'Departemen tidak dapat dihapus karena masih memiliki user.');

        $department->delete();

        return back()->with('success', 'Departemen berhasil dihapus.');
    }
}
