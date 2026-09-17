@extends('layouts.app')

@section('title', 'Manajemen Artikel Knowledge Base')
@section('page-title', 'Knowledge Base')
@section('page-subtitle', 'Kelola artikel panduan dan solusi untuk pengguna')

@section('content')

<div class="bg-white rounded-2xl shadow-sm overflow-hidden flex flex-col h-[calc(100vh-140px)]">

    {{-- Top Actions & Filters --}}
    <div class="px-6 py-4 border-b border-slate-100 flex flex-col lg:flex-row lg:items-center justify-between gap-4 flex-shrink-0">
        <form method="GET" action="{{ route('manage.knowledge.index') }}" class="flex flex-1 flex-wrap items-center gap-3">
            
            {{-- Search --}}
            <div class="relative flex-1 min-w-[200px] max-w-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul atau isi artikel..." class="w-full pl-9 pr-4 py-2 text-sm bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-purple-500/20 focus:bg-white transition-colors">
            </div>

            {{-- Status Filter --}}
            <select name="status" class="py-2 pl-3 pr-8 text-sm bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-purple-500/20 focus:bg-white transition-colors" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Dipublikasikan</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
            </select>

            <button type="submit" class="px-4 py-2 bg-slate-100 text-slate-600 hover:bg-slate-200 text-sm font-medium rounded-xl transition-colors">
                Filter
            </button>
        </form>

        <a href="{{ route('manage.knowledge.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-xl shadow-sm transition-colors flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14"/><path d="M5 12h14"/></svg>
            Tulis Artikel
        </a>
    </div>

    {{-- Table --}}
    <div class="flex-1 overflow-auto">
        @if($articles->isEmpty())
            <div class="px-6 py-20 text-center flex flex-col items-center justify-center h-full">
                <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                </div>
                <p class="text-slate-600 font-medium">Belum ada artikel</p>
                <p class="text-sm text-slate-400 mt-1">Buat artikel pertama untuk membantu pengguna menyelesaikan masalah.</p>
            </div>
        @else
            <table class="w-full text-left">
                <thead class="sticky top-0 bg-white/95 backdrop-blur z-10 shadow-[0_1px_2px_rgba(0,0,0,0.02)]">
                    <tr>
                        <th class="px-6 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wider">Judul Artikel</th>
                        <th class="px-6 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wider">Kategori</th>
                        <th class="px-6 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wider text-center">Status</th>
                        <th class="px-6 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wider">Penulis</th>
                        <th class="px-6 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($articles as $article)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-3.5">
                                <p class="text-sm font-semibold text-slate-800 line-clamp-1">{{ $article->title }}</p>
                                <p class="text-xs text-slate-500 mt-0.5">{{ $article->created_at->translatedFormat('d M Y') }}</p>
                            </td>
                            <td class="px-6 py-3.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-700">
                                    {{ $article->category->name }}
                                </span>
                            </td>
                            <td class="px-6 py-3.5 text-center">
                                @if($article->is_published)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-green-50 text-green-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Dipublikasikan
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-600">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Draft
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-3.5">
                                <p class="text-sm text-slate-700">{{ $article->author->name }}</p>
                            </td>
                            <td class="px-6 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('manage.knowledge.edit', $article) }}" class="p-2 text-slate-400 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-colors" title="Edit Artikel">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                                    </a>
                                    <form action="{{ route('manage.knowledge.destroy', $article) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus artikel ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
    
    @if($articles->hasPages())
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
            {{ $articles->links() }}
        </div>
    @endif
</div>

@endsection
