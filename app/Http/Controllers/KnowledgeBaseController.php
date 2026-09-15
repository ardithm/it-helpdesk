<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\KnowledgeBase;
use App\Models\TicketCategory;
use Illuminate\Http\Request;

class KnowledgeBaseController extends Controller
{
    /**
     * Daftar artikel yang dipublikasikan — bisa diakses semua role.
     */
    public function index(Request $request)
    {
        $query = KnowledgeBase::published()->with(['category', 'author']);

        // Admin, Helpdesk, dan Technician bisa lihat draft juga
        if (auth()->user()->role !== 'user') {
            $query = KnowledgeBase::with(['category', 'author']);
        }

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $articles   = $query->latest()->paginate(12)->withQueryString();
        $categories = TicketCategory::active()->orderBy('name')->get();

        return view('kb.index', compact('articles', 'categories'));
    }

    /**
     * Detail artikel.
     */
    public function show(KnowledgeBase $knowledgeBase)
    {
        // User biasa hanya bisa lihat yang published
        if (auth()->user()->role === 'user') {
            abort_unless($knowledgeBase->is_published, 404);
        }

        $knowledgeBase->load(['category', 'author']);

        return view('kb.show', compact('knowledgeBase'));
    }

    /**
     * Form buat artikel — hanya admin, helpdesk, technician.
     */
    public function create()
    {
        abort_if(auth()->user()->role === 'user', 403);

        $categories = TicketCategory::active()->orderBy('name')->get();
        return view('kb.create', compact('categories'));
    }

    /**
     * Simpan artikel baru.
     */
    public function store(Request $request)
    {
        abort_if(auth()->user()->role === 'user', 403);

        $validated = $request->validate([
            'category_id'  => ['required', 'exists:ticket_categories,id'],
            'title'        => ['required', 'string', 'max:255'],
            'content'      => ['required', 'string', 'min:30'],
            'is_published' => ['boolean'],
        ]);

        $article = KnowledgeBase::create([
            ...$validated,
            'author_id'    => auth()->id(),
            'is_published' => $request->boolean('is_published'),
        ]);

        AuditLog::record('kb.created', $article, null, ['title' => $article->title]);

        return redirect()->route('kb.show', $article)->with('success', 'Artikel berhasil disimpan.');
    }

    /**
     * Form edit artikel.
     */
    public function edit(KnowledgeBase $knowledgeBase)
    {
        abort_if(auth()->user()->role === 'user', 403);

        $categories = TicketCategory::active()->orderBy('name')->get();
        return view('kb.edit', compact('knowledgeBase', 'categories'));
    }

    /**
     * Update artikel.
     */
    public function update(Request $request, KnowledgeBase $knowledgeBase)
    {
        abort_if(auth()->user()->role === 'user', 403);

        $validated = $request->validate([
            'category_id'  => ['required', 'exists:ticket_categories,id'],
            'title'        => ['required', 'string', 'max:255'],
            'content'      => ['required', 'string', 'min:30'],
            'is_published' => ['boolean'],
        ]);

        $knowledgeBase->update([
            ...$validated,
            'is_published' => $request->boolean('is_published'),
        ]);

        return redirect()->route('kb.show', $knowledgeBase)->with('success', 'Artikel berhasil diperbarui.');
    }

    /**
     * Hapus artikel — hanya admin.
     */
    public function destroy(KnowledgeBase $knowledgeBase)
    {
        abort_unless(auth()->user()->role === 'admin', 403);

        $knowledgeBase->delete();

        return redirect()->route('kb.index')->with('success', 'Artikel berhasil dihapus.');
    }
}
