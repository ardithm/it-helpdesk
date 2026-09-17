<?php

namespace App\Http\Controllers;

use App\Models\KnowledgeBase;
use App\Models\TicketCategory;
use Illuminate\Http\Request;

class KnowledgeBasePortalController extends Controller
{
    public function index(Request $request)
    {
        $query = KnowledgeBase::with(['category', 'author'])->published();

        if ($request->filled('search')) {
            $query->search($request->search);
        }
        
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $articles = $query->orderBy('created_at', 'desc')->paginate(12)->withQueryString();
        $categories = TicketCategory::where('is_active', true)->orderBy('name')->get();

        return view('knowledge.index', compact('articles', 'categories'));
    }

    public function show($id)
    {
        $article = KnowledgeBase::with(['category', 'author'])->published()->findOrFail($id);
        
        return view('knowledge.show', compact('article'));
    }
}
