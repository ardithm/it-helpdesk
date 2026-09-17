@extends('layouts.app')

@section('title', 'SLA Policy')
@section('page-title', 'Service Level Agreement')
@section('page-subtitle', 'Atur target waktu respons dan penyelesaian tiket berdasarkan prioritas')

@section('content')

@php
    // Determine which priorities haven't been configured yet
    $configuredPriorities = $policies->pluck('priority')->toArray();
    $availablePriorities = array_diff($priorities, $configuredPriorities);
    
    // Priority formatting helper
    function getPriorityFormat($priority) {
        return match($priority) {
            'critical' => ['label' => 'Kritis', 'color' => 'bg-red-50 text-red-700 ring-red-600/20', 'dot' => 'bg-red-500'],
            'high'     => ['label' => 'Tinggi', 'color' => 'bg-orange-50 text-orange-700 ring-orange-600/20', 'dot' => 'bg-orange-500'],
            'medium'   => ['label' => 'Sedang', 'color' => 'bg-yellow-50 text-yellow-700 ring-yellow-600/20', 'dot' => 'bg-yellow-500'],
            'low'      => ['label' => 'Rendah', 'color' => 'bg-blue-50 text-blue-700 ring-blue-600/20', 'dot' => 'bg-blue-500'],
            default    => ['label' => ucfirst($priority), 'color' => 'bg-slate-50 text-slate-700 ring-slate-600/20', 'dot' => 'bg-slate-500'],
        };
    }
    
    // Format minutes helper
    function formatMinutes($minutes) {
        if ($minutes < 60) return $minutes . ' Menit';
        
        $hours = floor($minutes / 60);
        $remaining = $minutes % 60;
        
        if ($remaining > 0) {
            return $hours . ' Jam ' . $remaining . ' Mnt';
        }
        return $hours . ' Jam';
    }
@endphp

<div class="bg-white rounded-2xl shadow-sm overflow-hidden flex flex-col min-h-[calc(100vh-140px)]">

    {{-- Top Actions --}}
    <div class="px-6 py-4 border-b border-slate-100 flex flex-col lg:flex-row lg:items-center justify-between gap-4 flex-shrink-0">
        <div>
            <h2 class="text-sm font-semibold text-slate-800">Daftar SLA Policy</h2>
            <p class="text-xs text-slate-500 mt-0.5">SLA digunakan untuk menghitung batas waktu pengerjaan tiket.</p>
        </div>

        @if(count($availablePriorities) > 0)
            <button type="button" onclick="openCreateModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-xl shadow-sm transition-colors flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                Tambah SLA
            </button>
        @endif
    </div>

    {{-- Table --}}
    <div class="flex-1 overflow-auto">
        @if($policies->isEmpty())
            <div class="px-6 py-20 text-center flex flex-col items-center justify-center h-full">
                <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <p class="text-slate-600 font-medium">Belum ada aturan SLA</p>
                <p class="text-sm text-slate-400 mt-1">Tambahkan aturan SLA untuk menetapkan target waktu penyelesaian.</p>
            </div>
        @else
            <table class="w-full text-left">
                <thead class="sticky top-0 bg-white/95 backdrop-blur z-10 shadow-[0_1px_2px_rgba(0,0,0,0.02)]">
                    <tr>
                        <th class="px-6 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wider">Tingkat Prioritas</th>
                        <th class="px-6 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wider text-center">Waktu Respons (Maks)</th>
                        <th class="px-6 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wider text-center">Waktu Penyelesaian (Maks)</th>
                        <th class="px-6 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wider text-center">Status</th>
                        <th class="px-6 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($policies as $policy)
                        @php $fmt = getPriorityFormat($policy->priority); @endphp
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-3.5">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium ring-1 ring-inset {{ $fmt['color'] }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $fmt['dot'] }}"></span>
                                    {{ $fmt['label'] }}
                                </span>
                            </td>
                            <td class="px-6 py-3.5 text-center">
                                <p class="text-sm font-semibold text-slate-800">{{ formatMinutes($policy->response_time_minutes) }}</p>
                                <p class="text-xs text-slate-500">{{ $policy->response_time_minutes }} Menit</p>
                            </td>
                            <td class="px-6 py-3.5 text-center">
                                <p class="text-sm font-semibold text-slate-800">{{ formatMinutes($policy->resolution_time_minutes) }}</p>
                                <p class="text-xs text-slate-500">{{ $policy->resolution_time_minutes }} Menit</p>
                            </td>
                            <td class="px-6 py-3.5 text-center">
                                @if($policy->is_active)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-green-50 text-green-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-600">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-3.5 text-right">
                                <button type="button" 
                                    onclick="openEditModal({{ $policy->id }}, '{{ $policy->priority }}', '{{ $fmt['label'] }}', {{ $policy->response_time_minutes }}, {{ $policy->resolution_time_minutes }}, {{ $policy->is_active ? 'true' : 'false' }})"
                                    class="p-2 text-slate-400 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-colors" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>

{{-- Modal Form Tambah/Edit SLA --}}
<div id="slaModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>

    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-100">
                <form id="slaForm" action="{{ route('admin.sla.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    
                    <div class="bg-white px-6 pb-4 pt-5 sm:p-6 sm:pb-4 border-b border-slate-100">
                        <div class="flex items-center justify-between mb-5">
                            <h3 class="text-lg font-bold text-slate-800" id="modalTitle">Tambah SLA Policy Baru</h3>
                            <button type="button" onclick="closeModal()" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-xl transition-colors">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                            </button>
                        </div>
                        
                        <div class="space-y-5 mt-4">
                            
                            {{-- Priority Field --}}
                            <div id="priorityContainer">
                                <label for="priority" class="block text-sm font-medium text-slate-700 mb-1.5">Tingkat Prioritas <span class="text-red-500">*</span></label>
                                <select name="priority" id="priority" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 focus:bg-white text-sm transition-all outline-none">
                                    <option value="">-- Pilih Prioritas --</option>
                                    @foreach($availablePriorities as $p)
                                        <option value="{{ $p }}">
                                            {{ match($p) {
                                                'critical' => 'Kritis (Critical)',
                                                'high' => 'Tinggi (High)',
                                                'medium' => 'Sedang (Medium)',
                                                'low' => 'Rendah (Low)',
                                                default => ucfirst($p)
                                            } }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            {{-- Edit Priority Display (Readonly) --}}
                            <div id="editPriorityDisplay" class="hidden">
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">Tingkat Prioritas</label>
                                <div class="w-full px-4 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-slate-600 text-sm cursor-not-allowed">
                                    <span id="editPriorityLabel"></span>
                                </div>
                            </div>
                            
                            {{-- Response Time --}}
                            <div>
                                <label for="response_time_minutes" class="block text-sm font-medium text-slate-700 mb-1.5">Maks. Waktu Respons (Menit) <span class="text-red-500">*</span></label>
                                <input type="number" name="response_time_minutes" id="response_time_minutes" min="1" required placeholder="Contoh: 15" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 focus:bg-white text-sm transition-all outline-none">
                                <p class="text-xs text-slate-500 mt-1">Batas waktu untuk membalas/menugaskan tiket pertama kali.</p>
                            </div>
                            
                            {{-- Resolution Time --}}
                            <div>
                                <label for="resolution_time_minutes" class="block text-sm font-medium text-slate-700 mb-1.5">Maks. Waktu Penyelesaian (Menit) <span class="text-red-500">*</span></label>
                                <input type="number" name="resolution_time_minutes" id="resolution_time_minutes" min="1" required placeholder="Contoh: 1440" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 focus:bg-white text-sm transition-all outline-none">
                                <p class="text-xs text-slate-500 mt-1">Batas waktu tiket harus selesai/ditutup (Contoh: 1440 = 24 Jam).</p>
                            </div>
                            
                            {{-- Active Toggle --}}
                            <div class="flex items-center justify-between p-4 bg-slate-50/80 rounded-xl border border-slate-100">
                                <div>
                                    <p class="text-sm font-semibold text-slate-800">Status Aktif</p>
                                    <p class="text-xs text-slate-500 mt-0.5">Jika nonaktif, sistem tidak melacak target waktu.</p>
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
                            Simpan SLA
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
    const modal = document.getElementById('slaModal');
    const form = document.getElementById('slaForm');
    const modalTitle = document.getElementById('modalTitle');
    const formMethod = document.getElementById('formMethod');
    const submitBtn = document.getElementById('submitBtn');
    
    // Inputs
    const priorityContainer = document.getElementById('priorityContainer');
    const inputPriority = document.getElementById('priority');
    const editPriorityDisplay = document.getElementById('editPriorityDisplay');
    const editPriorityLabel = document.getElementById('editPriorityLabel');
    
    const inputResponse = document.getElementById('response_time_minutes');
    const inputResolution = document.getElementById('resolution_time_minutes');
    const inputIsActive = document.getElementById('is_active');
    
    function openCreateModal() {
        // Reset form
        form.reset();
        form.action = "{{ route('admin.sla.store') }}";
        formMethod.value = "POST";
        
        modalTitle.textContent = "Tambah SLA Policy Baru";
        submitBtn.textContent = "Simpan SLA";
        
        priorityContainer.classList.remove('hidden');
        inputPriority.required = true;
        editPriorityDisplay.classList.add('hidden');
        inputIsActive.checked = true;
        
        // Show modal
        modal.classList.remove('hidden');
    }
    
    function openEditModal(id, priorityVal, priorityLabel, responseTime, resolutionTime, isActive) {
        // Set form data
        form.action = `/admin/sla/${id}`;
        formMethod.value = "PUT";
        
        priorityContainer.classList.add('hidden');
        inputPriority.required = false;
        
        editPriorityDisplay.classList.remove('hidden');
        editPriorityLabel.textContent = priorityLabel + " (" + priorityVal + ")";
        
        inputResponse.value = responseTime;
        inputResolution.value = resolutionTime;
        inputIsActive.checked = isActive;
        
        modalTitle.textContent = "Edit SLA Policy";
        submitBtn.textContent = "Simpan Perubahan";
        
        // Show modal
        modal.classList.remove('hidden');
    }
    
    function closeModal() {
        modal.classList.add('hidden');
    }
    
    // Auto-open modal if there are validation errors
    @if($errors->any())
        document.addEventListener('DOMContentLoaded', function() {
            @if(old('_method') === 'PUT')
                // Basic fallback
                openCreateModal();
            @else
                openCreateModal();
            @endif
        });
    @endif
</script>
@endpush
