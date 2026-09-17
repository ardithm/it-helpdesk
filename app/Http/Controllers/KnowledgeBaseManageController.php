<?php

namespace App\Http\Controllers;

use App\Models\KnowledgeBase;
use App\Models\TicketCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KnowledgeBaseManageController extends Controller
{
    public function index(Request $request)
    {
        $query = KnowledgeBase::with(['category', 'author']);

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('status')) {
            $isPublished = $request->status === 'published';
            $query->where('is_published', $isPublished);
        }

        $articles = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        
        return view('manage.knowledge.index', compact('articles'));
    }

    public function create()
    {
        $categories = TicketCategory::where('is_active', true)->orderBy('name')->get();
        return view('manage.knowledge.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'        => ['required', 'string', 'max:255'],
            'category_id'  => ['required', 'exists:ticket_categories,id'],
            'content'      => ['required', 'string'],
            'is_published' => ['boolean'],
        ]);

        $validated['author_id'] = Auth::id();
        $validated['is_published'] = $request->boolean('is_published');

        $article = KnowledgeBase::create($validated);

        return redirect()->route('manage.knowledge.index')
            ->with('success', 'Artikel Knowledge Base berhasil ditambahkan.');
    }

    public function edit(KnowledgeBase $knowledge)
    {
        $categories = TicketCategory::where('is_active', true)->orderBy('name')->get();
        return view('manage.knowledge.edit', compact('knowledge', 'categories'));
    }

    public function update(Request $request, KnowledgeBase $knowledge)
    {
        $validated = $request->validate([
            'title'        => ['required', 'string', 'max:255'],
            'category_id'  => ['required', 'exists:ticket_categories,id'],
            'content'      => ['required', 'string'],
            'is_published' => ['boolean'],
        ]);

        $validated['is_published'] = $request->boolean('is_published');

        $knowledge->update($validated);

        return redirect()->route('manage.knowledge.index')
            ->with('success', 'Artikel Knowledge Base berhasil diperbarui.');
    }

    public function destroy(KnowledgeBase $knowledge)
    {
        $knowledge->delete();
        
        return redirect()->route('manage.knowledge.index')
            ->with('success', 'Artikel Knowledge Base berhasil dihapus.');
    }
}
