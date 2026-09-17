@extends('layouts.app')

@section('title', 'Manajemen Departemen')
@section('page-title', 'Departemen')
@section('page-subtitle', 'Kelola daftar departemen dan unit kerja')

@section('content')

<div class="bg-white rounded-2xl shadow-sm overflow-hidden flex flex-col h-[calc(100vh-140px)]">

    {{-- Top Actions & Filters --}}
    <div class="px-6 py-4 border-b border-slate-100 flex flex-col lg:flex-row lg:items-center justify-between gap-4 flex-shrink-0">
        <form method="GET" action="{{ route('admin.departments.index') }}" class="flex flex-1 flex-wrap items-center gap-3">
            
            {{-- Search --}}
            <div class="relative flex-1 min-w-[200px] max-w-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari departemen..." class="w-full pl-9 pr-4 py-2 text-sm bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-purple-500/20 focus:bg-white transition-colors">
            </div>

            <button type="submit" class="px-4 py-2 bg-slate-100 text-slate-600 hover:bg-slate-200 text-sm font-medium rounded-xl transition-colors">
                Cari
            </button>
        </form>

        <button type="button" onclick="openCreateModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-xl shadow-sm transition-colors flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
            Tambah Departemen
        </button>
    </div>

    {{-- Table --}}
    <div class="flex-1 overflow-auto">
        @if($departments->isEmpty())
            <div class="px-6 py-20 text-center flex flex-col items-center justify-center h-full">
                <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20h16a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.93a2 2 0 0 1-1.66-.9l-.82-1.2A2 2 0 0 0 7.93 3H4a2 2 0 0 0-2 2v13c0 1.1.9 2 2 2Z"/></svg>
                </div>
                <p class="text-slate-600 font-medium">Tidak ada data departemen</p>
                <p class="text-sm text-slate-400 mt-1">Sesuaikan filter atau tambahkan departemen baru.</p>
            </div>
        @else
            <table class="w-full text-left">
                <thead class="sticky top-0 bg-white/95 backdrop-blur z-10 shadow-[0_1px_2px_rgba(0,0,0,0.02)]">
                    <tr>
                        <th class="px-6 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wider">Nama Departemen</th>
                        <th class="px-6 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wider">Deskripsi</th>
                        <th class="px-6 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wider text-center">Jumlah Pegawai</th>
                        <th class="px-6 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($departments as $department)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-3.5">
                                <p class="text-sm font-semibold text-slate-800">{{ $department->name }}</p>
                            </td>
                            <td class="px-6 py-3.5">
                                <p class="text-sm text-slate-600 truncate max-w-md" title="{{ $department->description }}">{{ $department->description ?? '-' }}</p>
                            </td>
                            <td class="px-6 py-3.5 text-center">
                                <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700">
                                    {{ $department->users_count }} Pegawai
                                </span>
                            </td>
                            <td class="px-6 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button type="button" 
                                        onclick="openEditModal({{ $department->id }}, '{{ addslashes($department->name) }}', '{{ addslashes($department->description) }}')"
                                        class="p-2 text-slate-400 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-colors" title="Edit">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                                    </button>
                                    
                                    @if($department->users_count == 0)
                                        <form action="{{ route('admin.departments.destroy', $department) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus departemen ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                                            </button>
                                        </form>
                                    @else
                                        <button type="button" class="p-2 text-slate-200 cursor-not-allowed rounded-lg" title="Departemen sudah memiliki pegawai">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
    
    @if($departments->hasPages())
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
            {{ $departments->links() }}
        </div>
    @endif
</div>

{{-- Modal Form Tambah/Edit Departemen --}}
<div id="departmentModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>

    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-100">
                <form id="departmentForm" action="{{ route('admin.departments.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    
                    <div class="bg-white px-6 pb-4 pt-5 sm:p-6 sm:pb-4 border-b border-slate-100">
                        <div class="flex items-center justify-between mb-5">
                            <h3 class="text-lg font-bold text-slate-800" id="modalTitle">Tambah Departemen Baru</h3>
                            <button type="button" onclick="closeModal()" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-xl transition-colors">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                            </button>
                        </div>
                        
                        <div class="space-y-5 mt-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-slate-700 mb-1.5">Nama Departemen <span class="text-red-500">*</span></label>
                                <input type="text" name="name" id="name" required placeholder="Contoh: IT, HRD, Finance..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 focus:bg-white text-sm transition-all outline-none">
                                @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>
                            
                            <div>
                                <label for="description" class="block text-sm font-medium text-slate-700 mb-1.5">Deskripsi Singkat</label>
                                <textarea name="description" id="description" rows="3" placeholder="Jelaskan ruang lingkup departemen ini..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 focus:bg-white text-sm transition-all outline-none resize-none"></textarea>
                                @error('description') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-slate-50/50 px-6 py-4 flex items-center justify-end gap-3 rounded-b-2xl border-t border-slate-100">
                        <button type="button" onclick="closeModal()" class="px-4 py-2.5 bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-800 text-sm font-medium rounded-xl transition-all shadow-sm">
                            Batalkan
                        </button>
                        <button type="submit" id="submitBtn" class="px-5 py-2.5 bg-purple-600 hover:bg-purple-700 hover:shadow-md hover:shadow-purple-500/20 text-white text-sm font-medium rounded-xl transition-all">
                            Simpan Departemen
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const modal = document.getElementById('departmentModal');
    const form = document.getElementById('departmentForm');
    const modalTitle = document.getElementById('modalTitle');
    const formMethod = document.getElementById('formMethod');
    const submitBtn = document.getElementById('submitBtn');
    
    // Inputs
    const inputName = document.getElementById('name');
    const inputDescription = document.getElementById('description');
    
    function openCreateModal() {
        // Reset form
        form.reset();
        form.action = "{{ route('admin.departments.store') }}";
        formMethod.value = "POST";
        
        modalTitle.textContent = "Tambah Departemen Baru";
        submitBtn.textContent = "Simpan Departemen";
        
        // Show modal
        modal.classList.remove('hidden');
        setTimeout(() => inputName.focus(), 100);
    }
    
    function openEditModal(id, name, description) {
        // Set form data
        form.action = `/admin/departments/${id}`;
        formMethod.value = "PUT";
        
        inputName.value = name;
        inputDescription.value = description;
        
        modalTitle.textContent = "Edit Departemen";
        submitBtn.textContent = "Simpan Perubahan";
        
        // Show modal
        modal.classList.remove('hidden');
        setTimeout(() => inputName.focus(), 100);
    }
    
    function closeModal() {
        modal.classList.add('hidden');
    }
    
    // Auto-open modal if there are validation errors
    @if($errors->any())
        document.addEventListener('DOMContentLoaded', function() {
            @if(old('_method') === 'PUT')
                // Fallback for simple errors. (Could store exact ID if needed)
                openCreateModal();
            @else
                openCreateModal();
            @endif
            
            // Re-fill old data
            inputName.value = "{{ old('name') }}";
            inputDescription.value = "{{ old('description') }}";
        });
    @endif
</script>
@endpush
