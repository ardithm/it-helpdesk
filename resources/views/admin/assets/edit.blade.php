@extends('layouts.app')

@section('title', 'Edit Aset IT')
@section('page-title', 'Edit Aset IT')
@section('page-subtitle', 'Perbarui informasi atau status perangkat keras/lunak')

@section('content')

<div class="max-w-4xl mx-auto">
    <form action="{{ route('admin.assets.update', $asset) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-2xl shadow-sm overflow-hidden mb-6">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <h2 class="text-base font-semibold text-slate-800">Informasi Perangkat</h2>
                <a href="{{ route('admin.assets.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-purple-600 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 19-7-7 7-7"/><path d="M19 12H5"/></svg>
                    Kembali
                </a>
            </div>
            
            <div class="p-6 space-y-6">
                
                {{-- Baris 1: Kode Aset & Kategori --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="asset_code" class="block text-sm font-medium text-slate-700 mb-1.5">Kode Aset <span class="text-red-500">*</span></label>
                        <input type="text" id="asset_code" name="asset_code" value="{{ old('asset_code', $asset->asset_code) }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 focus:bg-white text-sm transition-colors outline-none">
                        @error('asset_code') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="category" class="block text-sm font-medium text-slate-700 mb-1.5">Kategori Perangkat <span class="text-red-500">*</span></label>
                        <input type="text" id="category" name="category" value="{{ old('category', $asset->category) }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 focus:bg-white text-sm transition-colors outline-none">
                        @error('category') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Baris 2: Merk, Model & SN --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="brand" class="block text-sm font-medium text-slate-700 mb-1.5">Merk <span class="text-red-500">*</span></label>
                        <input type="text" id="brand" name="brand" value="{{ old('brand', $asset->brand) }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 focus:bg-white text-sm transition-colors outline-none">
                        @error('brand') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="model" class="block text-sm font-medium text-slate-700 mb-1.5">Model / Tipe <span class="text-red-500">*</span></label>
                        <input type="text" id="model" name="model" value="{{ old('model', $asset->model) }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 focus:bg-white text-sm transition-colors outline-none">
                        @error('model') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="serial_number" class="block text-sm font-medium text-slate-700 mb-1.5">Serial Number <span class="text-red-500">*</span></label>
                        <input type="text" id="serial_number" name="serial_number" value="{{ old('serial_number', $asset->serial_number) }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 focus:bg-white text-sm transition-colors outline-none">
                        @error('serial_number') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>

            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm overflow-hidden mb-6">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <h2 class="text-base font-semibold text-slate-800">Status & Penugasan</h2>
                <span class="text-xs text-slate-500 bg-slate-100 px-2 py-1 rounded-md">Pembaruan status akan otomatis dicatat di History.</span>
            </div>
            
            <div class="p-6 space-y-6">
                
                {{-- Baris 3: Tanggal Beli & Status --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="purchase_date" class="block text-sm font-medium text-slate-700 mb-1.5">Tanggal Pembelian</label>
                        <input type="date" id="purchase_date" name="purchase_date" value="{{ old('purchase_date', $asset->purchase_date?->format('Y-m-d')) }}" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 focus:bg-white text-sm transition-colors outline-none">
                        @error('purchase_date') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-slate-700 mb-1.5">Status Saat Ini <span class="text-red-500">*</span></label>
                        <select id="status" name="status" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 focus:bg-white text-sm transition-colors outline-none">
                            <option value="active" {{ old('status', $asset->status) === 'active' ? 'selected' : '' }}>Aktif (Siap Digunakan)</option>
                            <option value="maintenance" {{ old('status', $asset->status) === 'maintenance' ? 'selected' : '' }}>Maintenance (Dalam Perbaikan)</option>
                            <option value="retired" {{ old('status', $asset->status) === 'retired' ? 'selected' : '' }}>Pensiun (Tidak Digunakan Lagi)</option>
                        </select>
                        @error('status') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                    <p class="text-sm font-semibold text-slate-800 mb-1">Ditugaskan Kepada</p>
                    <p class="text-xs text-slate-500 mb-4">Ubah penugasan aset ke divisi atau user tertentu.</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="user_id" class="block text-xs font-medium text-slate-700 mb-1.5">Pegawai (User)</label>
                            <select id="user_id" name="user_id" class="w-full px-4 py-2 bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 text-sm transition-colors outline-none">
                                <option value="">-- Tidak ada --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id', $asset->user_id) == $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                            @error('user_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        
                        <div>
                            <label for="department_id" class="block text-xs font-medium text-slate-700 mb-1.5">Departemen (Divisi Umum)</label>
                            <select id="department_id" name="department_id" class="w-full px-4 py-2 bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 text-sm transition-colors outline-none">
                                <option value="">-- Tidak ada --</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ old('department_id', $asset->department_id) == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                                @endforeach
                            </select>
                            @error('department_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

            </div>
            
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.assets.index') }}" class="px-4 py-2.5 bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-800 text-sm font-medium rounded-xl transition-all shadow-sm">
                    Batalkan
                </a>
                <button type="submit" class="px-5 py-2.5 bg-purple-600 hover:bg-purple-700 hover:shadow-md hover:shadow-purple-500/20 text-white text-sm font-medium rounded-xl transition-all">
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script>
    const userId = document.getElementById('user_id');
    const deptId = document.getElementById('department_id');

    userId.addEventListener('change', function() {
        if(this.value) deptId.value = '';
    });

    deptId.addEventListener('change', function() {
        if(this.value) userId.value = '';
    });
</script>
@endpush
