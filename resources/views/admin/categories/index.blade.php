@extends('layouts.app')

@section('title', 'Manajemen Kategori Tiket')
@section('page-title', 'Kategori Tiket')
@section('page-subtitle', 'Kelola daftar kategori untuk klasifikasi tiket')

@section('content')

<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div class="flex-1 max-w-md">
        {{-- Search (Optional/Future use if needed, for now just a placeholder or disabled if not in controller) --}}
    </div>
    
    <button type="button" onclick="openCreateModal()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-xl shadow-sm transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
        Tambah Kategori
    </button>
</div>

<div class="bg-white rounded-2xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Nama Kategori</th>
                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Deskripsi</th>
                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider text-center">Jumlah Tiket</th>
                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider text-center">Status</th>
                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($categories as $category)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <p class="text-sm font-semibold text-slate-800">{{ $category->name }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm text-slate-600 truncate max-w-md" title="{{ $category->description }}">{{ $category->description ?? '-' }}</p>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700">
                                {{ $category->tickets_count }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($category->is_active)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-green-50 text-green-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-600">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Nonaktif
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button type="button" 
                                    onclick="openEditModal({{ $category->id }}, '{{ addslashes($category->name) }}', '{{ addslashes($category->description) }}', {{ $category->is_active ? 'true' : 'false' }})"
                                    class="p-2 text-slate-400 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-colors" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                                </button>
                                
                                @if($category->tickets_count == 0)
                                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus kategori ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                                        </button>
                                    </form>
                                @else
                                    <button type="button" class="p-2 text-slate-200 cursor-not-allowed rounded-lg" title="Kategori sudah digunakan pada tiket">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center mb-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20h16a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.93a2 2 0 0 1-1.66-.9l-.82-1.2A2 2 0 0 0 7.93 3H4a2 2 0 0 0-2 2v13c0 1.1.9 2 2 2Z"/></svg>
                                </div>
                                <p class="text-sm font-medium text-slate-600">Belum ada kategori tiket</p>
                                <p class="text-xs text-slate-400 mt-1">Klik tombol "Tambah Kategori" untuk membuat baru.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($categories->hasPages())
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
            {{ $categories->links() }}
        </div>
    @endif
</div>

{{-- Modal Form Tambah/Edit Kategori --}}
<div id="categoryModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>

    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-100">
                <form id="categoryForm" action="{{ route('admin.categories.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    
                    <div class="bg-white px-6 pb-4 pt-5 sm:p-6 sm:pb-4 border-b border-slate-100">
                        <div class="flex items-center justify-between mb-5">
                            <h3 class="text-lg font-bold text-slate-800" id="modalTitle">Tambah Kategori Baru</h3>
                            <button type="button" onclick="closeModal()" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-xl transition-colors">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                            </button>
                        </div>
                        
                        <div class="space-y-5 mt-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-slate-700 mb-1.5">Nama Kategori <span class="text-red-500">*</span></label>
                                <input type="text" name="name" id="name" required placeholder="Contoh: Hardware, Software..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 focus:bg-white text-sm transition-all outline-none">
                            </div>
                            
                            <div>
                                <label for="description" class="block text-sm font-medium text-slate-700 mb-1.5">Deskripsi Singkat</label>
                                <textarea name="description" id="description" rows="3" placeholder="Jelaskan fungsi kategori ini..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 focus:bg-white text-sm transition-all outline-none resize-none"></textarea>
                            </div>
                            
                            <div class="flex items-center justify-between p-4 bg-slate-50/80 rounded-xl border border-slate-100">
                                <div>
                                    <p class="text-sm font-semibold text-slate-800">Status Aktif</p>
                                    <p class="text-xs text-slate-500 mt-0.5">Kategori nonaktif tidak bisa dipilih saat membuat tiket.</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="hidden" name="is_active" value="0">
                                    <input type="checkbox" name="is_active" id="is_active" value="1" checked class="sr-only peer">
                                    <div class="w-11 h-6 bg-slate-200 rounded-full peer peer-checked:bg-purple-600 peer-focus:ring-4 peer-focus:ring-purple-500/20 after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-slate-50/50 px-6 py-4 flex items-center justify-end gap-3 rounded-b-2xl border-t border-slate-100">
                        <button type="button" onclick="closeModal()" class="px-4 py-2.5 bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-800 text-sm font-medium rounded-xl transition-all shadow-sm">
                            Batalkan
                        </button>
                        <button type="submit" id="submitBtn" class="px-5 py-2.5 bg-purple-600 hover:bg-purple-700 hover:shadow-md hover:shadow-purple-500/20 text-white text-sm font-medium rounded-xl transition-all">
                            Simpan Kategori
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
    const modal = document.getElementById('categoryModal');
    const form = document.getElementById('categoryForm');
    const modalTitle = document.getElementById('modalTitle');
    const formMethod = document.getElementById('formMethod');
    const submitBtn = document.getElementById('submitBtn');
    
    // Inputs
    const inputName = document.getElementById('name');
    const inputDescription = document.getElementById('description');
    const inputIsActive = document.getElementById('is_active');
    
    function openCreateModal() {
        // Reset form
        form.reset();
        form.action = "{{ route('admin.categories.store') }}";
        formMethod.value = "POST";
        
        modalTitle.textContent = "Tambah Kategori Baru";
        submitBtn.textContent = "Simpan Kategori";
        inputIsActive.checked = true;
        
        // Show modal
        modal.classList.remove('hidden');
        setTimeout(() => inputName.focus(), 100);
    }
    
    function openEditModal(id, name, description, isActive) {
        // Set form data
        form.action = `/admin/categories/${id}`;
        formMethod.value = "PUT";
        
        inputName.value = name;
        inputDescription.value = description;
        inputIsActive.checked = isActive;
        
        modalTitle.textContent = "Edit Kategori";
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
                // Ideally we'd know which ID to edit, but fallback to create view for simple errors
                // In a robust app, we might store the edit ID in session
                openCreateModal();
            @else
                openCreateModal();
            @endif
            
            // Re-fill old data
            inputName.value = "{{ old('name') }}";
            inputDescription.value = "{{ old('description') }}";
            inputIsActive.checked = {{ old('is_active', true) ? 'true' : 'false' }};
        });
    @endif
</script>
@endpush
