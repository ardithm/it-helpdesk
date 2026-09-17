@extends('layouts.app')

@section('title', $article->title . ' - Knowledge Base')
@section('page-title', 'Baca Artikel')
@section('page-subtitle', 'Pusat Bantuan')

@section('content')

<div class="max-w-4xl mx-auto">
    
    {{-- Tombol Kembali --}}
    <a href="{{ route('knowledge.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-purple-600 transition-colors mb-6">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 19-7-7 7-7"/><path d="M19 12H5"/></svg>
        Kembali ke Pencarian
    </a>

    {{-- Main Article Card --}}
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        
        {{-- Header Artikel --}}
        <div class="p-8 md:p-12 border-b border-slate-100 bg-slate-50/50">
            <div class="flex items-center gap-3 mb-6">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-700 tracking-wide uppercase">
                    {{ $article->category->name }}
                </span>
            </div>
            
            <h1 class="text-3xl md:text-4xl font-extrabold text-slate-800 leading-tight mb-6">
                {{ $article->title }}
            </h1>
            
            <div class="flex flex-wrap items-center gap-6 text-sm font-medium text-slate-500">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center text-slate-600 font-bold text-xs">
                        {{ strtoupper(substr($article->author->name, 0, 2)) }}
                    </div>
                    <span>Ditulis oleh <span class="text-slate-700">{{ $article->author->name }}</span></span>
                </div>
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                    Terakhir diperbarui: {{ $article->updated_at->translatedFormat('d F Y') }}
                </div>
            </div>
        </div>
        
        {{-- Konten Artikel --}}
        <div class="p-8 md:p-12 prose prose-slate max-w-none prose-headings:text-slate-800 prose-headings:font-bold prose-a:text-purple-600 hover:prose-a:text-purple-700 prose-p:leading-relaxed prose-li:leading-relaxed">
            {!! nl2br(e($article->content)) !!}
        </div>
        
        {{-- Footer Artikel --}}
        <div class="px-8 md:px-12 py-6 bg-slate-50 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-sm font-medium text-slate-600">Apakah artikel ini membantu?</p>
            <div class="flex items-center gap-2">
                <button type="button" class="px-4 py-2 bg-white border border-slate-200 hover:border-green-300 hover:bg-green-50 text-slate-600 hover:text-green-700 text-sm font-medium rounded-xl transition-all shadow-sm flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"/></svg>
                    Ya, Sangat Membantu
                </button>
                <button type="button" class="px-4 py-2 bg-white border border-slate-200 hover:border-red-300 hover:bg-red-50 text-slate-600 hover:text-red-700 text-sm font-medium rounded-xl transition-all shadow-sm flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 15v4a3 3 0 0 0 3 3l4-9V2H5.72a2 2 0 0 0-2 1.7l-1.38 9a2 2 0 0 0 2 2.3zm7-13h2.67A2.31 2.31 0 0 1 22 4v7a2.31 2.31 0 0 1-2.33 2H17"/></svg>
                    Tidak Terlalu
                </button>
            </div>
        </div>
    </div>
    
</div>

@endsection
