@extends('layouts.app')

@section('title', 'Knowledge Base (Pusat Bantuan)')
@section('page-title', 'Knowledge Base')
@section('page-subtitle', 'Temukan panduan, solusi, dan jawaban atas pertanyaan umum')

@section('content')

{{-- Hero Search Section --}}
<div class="bg-gradient-to-br from-purple-600 to-indigo-700 rounded-3xl shadow-lg mb-8 overflow-hidden relative">
    <!-- Decorative background elements -->
    <div class="absolute top-0 right-0 -mr-8 -mt-8 w-64 h-64 rounded-full bg-white/10 blur-3xl"></div>
    <div class="absolute bottom-0 left-0 -ml-8 -mb-8 w-40 h-40 rounded-full bg-indigo-900/30 blur-2xl"></div>
    
    <div class="relative px-6 py-16 md:py-24 text-center max-w-3xl mx-auto">
        <h2 class="text-3xl md:text-4xl font-extrabold text-white mb-4">Apa yang bisa kami bantu?</h2>
        <p class="text-purple-100 text-lg mb-8">Cari panduan teknis, solusi masalah, atau dokumentasi sistem.</p>
        
        <form action="{{ route('knowledge.index') }}" method="GET" class="relative max-w-2xl mx-auto">
            <div class="relative flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-4 w-6 h-6 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik kata kunci masalah Anda..." class="w-full pl-12 pr-32 py-4 bg-white rounded-2xl shadow-xl text-slate-800 focus:outline-none focus:ring-4 focus:ring-white/20 text-lg transition-shadow">
                <button type="submit" class="absolute right-2 top-2 bottom-2 px-6 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-xl transition-colors">
                    Cari
                </button>
            </div>
            @if(request('category_id'))
                <input type="hidden" name="category_id" value="{{ request('category_id') }}">
            @endif
        </form>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
    
    {{-- Sidebar Kategori --}}
    <div class="lg:col-span-1 space-y-4">
        <h3 class="font-bold text-slate-800 text-lg mb-4 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-purple-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2H2v10l9.29 9.29c.94.94 2.48.94 3.42 0l6.58-6.58c.94-.94.94-2.48 0-3.42L12 2Z"/><path d="M7 7h.01"/></svg>
            Kategori
        </h3>
        
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <a href="{{ route('knowledge.index') }}" class="flex items-center justify-between px-4 py-3 {{ !request('category_id') ? 'bg-purple-50 border-l-4 border-purple-600 text-purple-700 font-semibold' : 'text-slate-600 hover:bg-slate-50' }} transition-colors">
                Semua Kategori
            </a>
            @foreach($categories as $category)
                <a href="{{ route('knowledge.index', ['category_id' => $category->id] + request()->except('category_id')) }}" 
                   class="flex items-center justify-between px-4 py-3 border-t border-slate-50 {{ request('category_id') == $category->id ? 'bg-purple-50 border-l-4 border-purple-600 text-purple-700 font-semibold' : 'text-slate-600 hover:bg-slate-50 border-l-4 border-transparent' }} transition-colors">
                    <span class="truncate pr-2">{{ $category->name }}</span>
                </a>
            @endforeach
        </div>
        
        @if(request('search') || request('category_id'))
            <a href="{{ route('knowledge.index') }}" class="block text-center text-sm text-purple-600 hover:text-purple-700 font-medium py-2">
                Reset Filter & Pencarian
            </a>
        @endif
    </div>
    
    {{-- Main Content --}}
    <div class="lg:col-span-3">
        
        <div class="flex items-center justify-between mb-6">
            <h3 class="font-bold text-slate-800 text-lg">
                @if(request('search'))
                    Hasil pencarian untuk: <span class="text-purple-600">"{{ request('search') }}"</span>
                @elseif(request('category_id'))
                    Artikel Kategori: <span class="text-purple-600">{{ $categories->where('id', request('category_id'))->first()->name ?? '' }}</span>
                @else
                    Artikel Terbaru
                @endif
            </h3>
            <span class="text-sm font-medium text-slate-500 bg-white px-3 py-1 rounded-full shadow-sm">{{ $articles->total() }} Artikel</span>
        </div>

        @if($articles->isEmpty())
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-12 text-center">
                <div class="w-20 h-20 mx-auto bg-slate-50 rounded-full flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                </div>
                <h3 class="text-lg font-bold text-slate-800">Artikel tidak ditemukan</h3>
                <p class="text-slate-500 mt-2">Maaf, tidak ada panduan yang sesuai dengan pencarian atau filter Anda.</p>
                <a href="{{ route('knowledge.index') }}" class="inline-block mt-6 px-6 py-2.5 bg-purple-100 text-purple-700 hover:bg-purple-200 font-medium rounded-xl transition-colors">
                    Lihat Semua Artikel
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($articles as $article)
                    <a href="{{ route('knowledge.show', $article->id) }}" class="group block bg-white rounded-2xl shadow-sm border border-slate-100 hover:shadow-md hover:border-purple-200 p-6 transition-all">
                        <div class="flex items-start justify-between mb-4">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-purple-50 text-purple-700">
                                {{ $article->category->name }}
                            </span>
                        </div>
                        <h4 class="text-lg font-bold text-slate-800 group-hover:text-purple-700 transition-colors mb-2 line-clamp-2">
                            {{ $article->title }}
                        </h4>
                        <p class="text-sm text-slate-500 line-clamp-3 mb-6">
                            {{ strip_tags($article->content) }}
                        </p>
                        <div class="flex items-center justify-between text-xs text-slate-400 mt-auto pt-4 border-t border-slate-50">
                            <div class="flex items-center gap-1.5">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                {{ $article->author->name }}
                            </div>
                            <div class="flex items-center gap-1.5">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                                {{ $article->created_at->translatedFormat('d M Y') }}
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
            
            @if($articles->hasPages())
                <div class="mt-8">
                    {{ $articles->links() }}
                </div>
            @endif
        @endif
        
    </div>
</div>

@endsection
