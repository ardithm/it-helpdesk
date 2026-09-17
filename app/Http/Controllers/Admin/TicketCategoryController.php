<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TicketCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TicketCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = TicketCategory::withCount('tickets')->orderBy('name');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }

        $categories = $query->paginate(15)->withQueryString();
        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => ['required', 'string', 'max:255', 'unique:ticket_categories,name'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active'   => ['boolean'],
        ]);

        TicketCategory::create([
            'name'        => $request->name,
            'description' => $request->description,
            'is_active'   => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function update(Request $request, TicketCategory $ticketCategory)
    {
        $request->validate([
            'name'        => ['required', 'string', 'max:255', Rule::unique('ticket_categories')->ignore($ticketCategory->id)],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active'   => ['boolean'],
        ]);

        $ticketCategory->update([
            'name'        => $request->name,
            'description' => $request->description,
            'is_active'   => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(TicketCategory $ticketCategory)
    {
        abort_if($ticketCategory->tickets()->exists(), 422, 'Kategori tidak dapat dihapus karena sudah digunakan pada tiket.');

        $ticketCategory->delete();

        return back()->with('success', 'Kategori berhasil dihapus.');
    }
}
