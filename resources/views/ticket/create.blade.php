@extends('layouts.app')

@section('title', 'Buat Tiket Baru')
@section('page-title', 'Buat Tiket Baru')
@section('page-subtitle', 'Jelaskan masalah atau permintaan layanan IT Anda secara detail')

@section('content')

<div class="w-full">
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <form action="{{ route('user.tickets.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="p-6 md:p-8 space-y-6">
                {{-- Kategori & Tipe --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Kategori <span class="text-red-500">*</span></label>
                        <select name="category_id" required class="w-full px-4 py-2.5 bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-purple-500/20 focus:bg-white text-sm transition-colors">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('category_id') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Tipe Tiket <span class="text-red-500">*</span></label>
                        <div class="flex gap-4">
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" name="type" value="incident" class="peer sr-only" required {{ old('type', 'incident') === 'incident' ? 'checked' : '' }}>
                                <div class="px-4 py-2.5 rounded-xl border border-slate-200 text-center text-sm font-medium text-slate-600 peer-checked:border-purple-500 peer-checked:bg-purple-50 peer-checked:text-purple-700 transition-all">
                                    Laporan Masalah (Incident)
                                </div>
                            </label>
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" name="type" value="service_request" class="peer sr-only" required {{ old('type') === 'service_request' ? 'checked' : '' }}>
                                <div class="px-4 py-2.5 rounded-xl border border-slate-200 text-center text-sm font-medium text-slate-600 peer-checked:border-purple-500 peer-checked:bg-purple-50 peer-checked:text-purple-700 transition-all">
                                    Permintaan Layanan
                                </div>
                            </label>
                        </div>
                        @error('type') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Judul --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Judul Tiket <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" required placeholder="Contoh: Layar monitor berkedip dan mati"
                        class="w-full px-4 py-2.5 bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-purple-500/20 focus:bg-white text-sm transition-colors">
                    @error('title') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>

                {{-- Deskripsi --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Deskripsi Detail <span class="text-red-500">*</span></label>
                    <textarea name="description" rows="5" required placeholder="Jelaskan secara rinci masalah yang terjadi atau layanan yang Anda butuhkan..."
                        class="w-full px-4 py-3 bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-purple-500/20 focus:bg-white text-sm transition-colors resize-none">{{ old('description') }}</textarea>
                    @error('description') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>

                {{-- Aset & Prioritas --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Prioritas <span class="text-red-500">*</span></label>
                        <select name="priority" required class="w-full px-4 py-2.5 bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-purple-500/20 focus:bg-white text-sm transition-colors">
                            <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low (Rendah)</option>
                            <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>Medium (Sedang)</option>
                            <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High (Tinggi)</option>
                            <option value="critical" {{ old('priority') == 'critical' ? 'selected' : '' }}>Critical (Kritis)</option>
                        </select>
                        <p class="mt-1.5 text-xs text-slate-400">Pilih "Critical" hanya untuk masalah yang menghentikan operasional divisi.</p>
                        @error('priority') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Aset Terkait (Opsional)</label>
                        <select name="asset_id" class="w-full px-4 py-2.5 bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-purple-500/20 focus:bg-white text-sm transition-colors">
                            <option value="">-- Tidak ada aset terkait --</option>
                            @foreach($assets as $asset)
                            <option value="{{ $asset->id }}" {{ old('asset_id') == $asset->id ? 'selected' : '' }}>
                                {{ $asset->asset_code }} — {{ $asset->brand }} {{ $asset->model }}
                            </option>
                            @endforeach
                        </select>
                        @error('asset_id') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Attachments --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Lampiran / Screenshot (Opsional)</label>
                    <input type="file" name="attachments[]" multiple accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx"
                        class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 transition-colors">
                    <p class="mt-1.5 text-xs text-slate-400">Format: JPG, PNG, PDF, Word, Excel. Maksimal 5MB/file (Max 5 file).</p>
                    @error('attachments.*') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('user.tickets.index') }}" class="px-4 py-2.5 text-sm font-medium text-slate-600 hover:text-slate-800 transition-colors">Batal</a>
                <button type="submit" class="px-5 py-2.5 bg-purple-600 text-white text-sm font-medium rounded-xl hover:bg-purple-700 focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors shadow-sm">
                    Kirim Tiket
                </button>
            </div>
        </form>
    </div>
</div>

@endsection