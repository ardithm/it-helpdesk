@extends('layouts.app')

@section('title', 'Tulis Artikel Knowledge Base')
@section('page-title', 'Tulis Artikel')
@section('page-subtitle', 'Buat panduan atau solusi baru untuk pengguna')

@section('content')

<div class="max-w-4xl mx-auto">
    <form action="{{ route('manage.knowledge.store') }}" method="POST">
        @csrf

        <div class="bg-white rounded-2xl shadow-sm overflow-hidden mb-6">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <h2 class="text-base font-semibold text-slate-800">Konten Artikel</h2>
                <a href="{{ route('manage.knowledge.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-purple-600 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 19-7-7 7-7"/><path d="M19 12H5"/></svg>
                    Batal
                </a>
            </div>
            
            <div class="p-6 space-y-6">
                
                <div>
                    <label for="title" class="block text-sm font-medium text-slate-700 mb-1.5">Judul Artikel <span class="text-red-500">*</span></label>
                    <input type="text" id="title" name="title" value="{{ old('title') }}" required placeholder="Contoh: Cara Reset Password Email" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 focus:bg-white text-sm transition-colors outline-none">
                    @error('title') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="category_id" class="block text-sm font-medium text-slate-700 mb-1.5">Kategori <span class="text-red-500">*</span></label>
                    <select id="category_id" name="category_id" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 focus:bg-white text-sm transition-colors outline-none">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="content" class="block text-sm font-medium text-slate-700 mb-1.5">Isi Artikel <span class="text-red-500">*</span></label>
                    <textarea id="content" name="content" required rows="12" placeholder="Tulis langkah-langkah atau solusi di sini..." class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 focus:bg-white text-sm transition-colors outline-none resize-y">{{ old('content') }}</textarea>
                    <p class="text-xs text-slate-500 mt-1.5">Gunakan bahasa yang mudah dipahami oleh user.</p>
                    @error('content') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center justify-between p-4 bg-slate-50/80 rounded-xl border border-slate-100">
                    <div>
                        <p class="text-sm font-semibold text-slate-800">Publikasikan Langsung?</p>
                        <p class="text-xs text-slate-500 mt-0.5">Jika aktif, pengguna dapat langsung membaca artikel ini di portal.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="is_published" value="0">
                        <input type="checkbox" name="is_published" id="is_published" value="1" checked class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-200 rounded-full peer peer-checked:bg-purple-600 peer-focus:ring-4 peer-focus:ring-purple-500/20 after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
                    </label>
                </div>

            </div>
            
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('manage.knowledge.index') }}" class="px-4 py-2.5 bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-800 text-sm font-medium rounded-xl transition-all shadow-sm">
                    Batalkan
                </a>
                <button type="submit" class="px-5 py-2.5 bg-purple-600 hover:bg-purple-700 hover:shadow-md hover:shadow-purple-500/20 text-white text-sm font-medium rounded-xl transition-all">
                    Simpan Artikel
                </button>
            </div>
        </div>
    </form>
</div>

@endsection
