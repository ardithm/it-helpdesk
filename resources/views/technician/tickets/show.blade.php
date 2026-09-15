@extends('layouts.app')

@section('title', 'Tugas Tiket ' . $ticket->ticket_number)
@section('page-title', 'Tugas Tiket: ' . $ticket->ticket_number)
@section('page-subtitle', 'SLA Target: ' . ($ticket->resolution_deadline ? $ticket->resolution_deadline->format('d M Y, H:i') : 'Tidak ada'))

@section('content')

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Kiri: Detail & Form Aksi --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Panel Detail Masalah --}}
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="p-6">
                <div class="flex items-start justify-between gap-4 mb-4">
                    <h2 class="text-lg font-semibold text-slate-800">{{ $ticket->title }}</h2>
                    <div class="flex-shrink-0 flex items-center gap-2">
                        @include('components.priority-badge', ['priority' => $ticket->priority])
                        @include('components.status-badge', ['status' => $ticket->status])
                    </div>
                </div>
                
                <div class="prose prose-sm prose-slate max-w-none text-slate-600 mb-6 whitespace-pre-wrap">{{ $ticket->description }}</div>
                
                @if($ticket->attachments->isNotEmpty())
                    <div class="pt-4 border-t border-slate-100">
                        <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Lampiran Pengguna</h3>
                        <div class="flex flex-wrap gap-3">
                            @foreach($ticket->attachments as $att)
                                <a href="{{ Storage::url($att->file_path) }}" target="_blank" class="flex items-center gap-2 px-3 py-2 bg-slate-50 hover:bg-purple-50 hover:text-purple-700 text-slate-600 rounded-xl text-sm transition-colors group">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-400 group-hover:text-purple-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg>
                                    {{ $att->file_name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Action Bar: Mulai Kerja / Set Waiting --}}
            @if($ticket->status === \App\Models\Ticket::STATUS_ASSIGNED)
                <div class="px-6 py-4 bg-purple-50 border-t border-purple-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <p class="text-sm font-medium text-purple-800">Tiket baru di-assign ke Anda.</p>
                        <p class="text-xs text-purple-600 mt-0.5">Klik tombol mulai untuk mengubah status ke In Progress.</p>
                    </div>
                    <form action="{{ route('technician.tickets.start', $ticket) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full sm:w-auto px-5 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-xl shadow-sm transition-colors">
                            Mulai Dikerjakan
                        </button>
                    </form>
                </div>
            @elseif($ticket->status === \App\Models\Ticket::STATUS_IN_PROGRESS)
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100">
                    <form action="{{ route('technician.tickets.waiting', $ticket) }}" method="POST" class="flex flex-col sm:flex-row gap-3">
                        @csrf
                        <div class="flex-1">
                            <input type="text" name="reason" required placeholder="Alasan menunggu (misal: Menunggu user menyerahkan laptop)" class="w-full px-4 py-2 text-sm bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-purple-500/20 transition-colors">
                        </div>
                        <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-orange-100 text-orange-700 hover:bg-orange-200 text-sm font-medium rounded-xl transition-colors whitespace-nowrap">
                            Tandai Waiting
                        </button>
                    </form>
                </div>
            @elseif($ticket->status === \App\Models\Ticket::STATUS_WAITING)
                <div class="px-6 py-4 bg-orange-50 border-t border-orange-100 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-orange-800">Tiket sedang berstatus Waiting.</p>
                        <p class="text-xs text-orange-600 mt-0.5">Jika informasi sudah didapat, klik Mulai Dikerjakan kembali.</p>
                    </div>
                    <form action="{{ route('technician.tickets.start', $ticket) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium rounded-xl shadow-sm transition-colors">
                            Lanjut Kerjakan
                        </button>
                    </form>
                </div>
            @endif
        </div>

        {{-- Form Penyelesaian (Resolusi) - Tampil jika In Progress / Waiting --}}
        @if(in_array($ticket->status, [\App\Models\Ticket::STATUS_IN_PROGRESS, \App\Models\Ticket::STATUS_WAITING]))
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-green-100">
                <div class="px-6 py-4 bg-green-50 border-b border-green-100">
                    <h2 class="text-base font-semibold text-green-800 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>
                        Form Penyelesaian Tiket (Resolusi)
                    </h2>
                </div>
                <div class="p-6">
                    <form action="{{ route('technician.tickets.resolve', $ticket) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                        @csrf
                        
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Hasil Diagnosis <span class="text-red-500">*</span></label>
                            <textarea name="diagnosis" rows="2" required placeholder="Apa masalah sebenarnya..." class="w-full px-4 py-2.5 bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-green-500/20 text-sm transition-colors resize-none">{{ old('diagnosis') }}</textarea>
                            @error('diagnosis') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Tindakan yang Dilakukan <span class="text-red-500">*</span></label>
                            <textarea name="action_taken" rows="2" required placeholder="Langkah-langkah perbaikan..." class="w-full px-4 py-2.5 bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-green-500/20 text-sm transition-colors resize-none">{{ old('action_taken') }}</textarea>
                            @error('action_taken') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Pergantian Komponen/Sparepart (Opsional)</label>
                            <input type="text" name="sparepart" value="{{ old('sparepart') }}" placeholder="Contoh: RAM 8GB DDR4, SSD 512GB" class="w-full px-4 py-2.5 bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-green-500/20 text-sm transition-colors">
                            @error('sparepart') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Resolusi Akhir <span class="text-red-500">*</span></label>
                            <textarea name="resolution" rows="2" required placeholder="Kesimpulan akhir, misal: Laptop sudah dites dan berfungsi normal." class="w-full px-4 py-2.5 bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-green-500/20 text-sm transition-colors resize-none">{{ old('resolution') }}</textarea>
                            @error('resolution') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Lampiran Bukti Pengerjaan (Opsional)</label>
                            <input type="file" name="attachments[]" multiple class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 transition-colors">
                        </div>

                        <div class="pt-2 border-t border-slate-100 flex justify-end">
                            <button type="submit" class="px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white font-medium rounded-xl shadow-sm transition-colors">
                                Selesaikan Tiket
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        {{-- Jika Sudah Resolved --}}
        @if($ticket->resolution)
            <div class="bg-green-50 border border-green-100 rounded-2xl overflow-hidden">
                <div class="px-5 py-4 border-b border-green-200/60 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>
                    <h2 class="text-sm font-semibold text-green-800">Laporan Resolusi (Selesai)</h2>
                </div>
                <div class="p-5 space-y-4">
                    <div>
                        <p class="text-xs font-semibold text-green-700 mb-1">Diagnosis:</p>
                        <p class="text-sm text-green-900">{{ $ticket->resolution->diagnosis }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-green-700 mb-1">Tindakan:</p>
                        <p class="text-sm text-green-900">{{ $ticket->resolution->action_taken }}</p>
                    </div>
                    @if($ticket->resolution->sparepart)
                        <div>
                            <p class="text-xs font-semibold text-green-700 mb-1">Pergantian Sparepart:</p>
                            <p class="text-sm text-green-900">{{ $ticket->resolution->sparepart }}</p>
                        </div>
                    @endif
                    <div>
                        <p class="text-xs font-semibold text-green-700 mb-1">Resolusi Akhir:</p>
                        <p class="text-sm text-green-900">{{ $ticket->resolution->resolution }}</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Panel Komunikasi (Chat) --}}
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden flex flex-col" style="min-height: 500px; max-height: 800px;">
            <div class="px-6 py-4 border-b border-slate-100 flex-shrink-0 flex items-center justify-between">
                <h2 class="text-base font-semibold text-slate-800 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-purple-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                    Log Diskusi & Internal Note
                </h2>
            </div>
            
            <div class="flex-1 overflow-y-auto p-6 space-y-6 bg-slate-50/30">
                @if($ticket->comments->isEmpty())
                    <div class="h-full flex flex-col items-center justify-center text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-slate-200 mb-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                        <p class="text-slate-500 text-sm">Belum ada diskusi pada tiket ini.</p>
                    </div>
                @else
                    @foreach($ticket->comments as $comment)
                        @php 
                            $isMe = $comment->user_id === auth()->id(); 
                            $isInternal = $comment->is_internal;
                        @endphp
                        <div class="flex flex-col {{ $isMe ? 'items-end' : 'items-start' }}">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-xs font-medium text-slate-700">
                                    {{ $isMe ? 'Anda' : $comment->user->name }}
                                </span>
                                <span class="text-[10px] text-slate-400">{{ $comment->created_at->format('d M H:i') }}</span>
                            </div>
                            <div class="max-w-[85%] px-4 py-2.5 text-sm relative
                                {{ $isInternal 
                                    ? 'bg-yellow-50 text-yellow-900 border border-yellow-200/60 rounded-2xl' . ($isMe ? ' rounded-tr-sm' : ' rounded-tl-sm')
                                    : ($isMe 
                                        ? 'bg-purple-100 text-purple-900 rounded-2xl rounded-tr-sm' 
                                        : 'bg-white border border-slate-100 text-slate-800 shadow-sm rounded-2xl rounded-tl-sm') 
                                }}">
                                @if($isInternal)
                                    <div class="flex items-center gap-1 mb-1 text-[10px] font-bold text-yellow-700 uppercase tracking-wider">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                        Catatan Internal
                                    </div>
                                @endif
                                {!! nl2br(e($comment->comment)) !!}
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            @if(!in_array($ticket->status, [\App\Models\Ticket::STATUS_RESOLVED, \App\Models\Ticket::STATUS_CLOSED]))
                <div class="p-4 border-t border-slate-100 bg-white flex-shrink-0">
                    <form action="{{ route('technician.tickets.comment', $ticket) }}" method="POST">
                        @csrf
                        <div class="flex items-end gap-3">
                            <div class="flex-1">
                                <textarea name="comment" rows="1" required placeholder="Balas ke user atau buat catatan internal..." class="w-full min-h-[44px] max-h-32 px-4 py-2.5 bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-purple-500/20 text-sm transition-colors resize-y mb-2" oninput="this.style.height = '';this.style.height = this.scrollHeight + 'px'"></textarea>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="is_internal" value="1" class="w-4 h-4 text-purple-600 border-slate-300 rounded focus:ring-purple-500">
                                    <span class="text-xs font-medium text-slate-600">Simpan sebagai Catatan Internal (User tidak melihat)</span>
                                </label>
                            </div>
                            <button type="submit" class="flex-shrink-0 h-[44px] px-5 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-xl shadow-sm transition-colors mb-6">
                                Kirim
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>

    {{-- Kanan: Sidebar Info --}}
    <div class="space-y-6">
        
        {{-- Info Card --}}
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h2 class="text-sm font-semibold text-slate-800">Informasi Tiket</h2>
            </div>
            <div class="p-5 space-y-4">
                <div>
                    <p class="text-xs text-slate-400 mb-1">Pelapor</p>
                    <p class="text-sm font-medium text-slate-700">{{ $ticket->user->name }}</p>
                    <p class="text-xs text-slate-500">{{ $ticket->user->department?->name ?? 'Tanpa Departemen' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 mb-1">Kategori & Tipe</p>
                    <p class="text-sm font-medium text-slate-700">{{ $ticket->category?->name ?? '-' }} ({{ str_replace('_', ' ', $ticket->type) }})</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 mb-1">SLA Target</p>
                    <p class="text-sm font-semibold {{ $ticket->sla_status === 'breached' ? 'text-red-600' : 'text-slate-700' }}">
                        {{ $ticket->resolution_deadline ? $ticket->resolution_deadline->format('d M Y, H:i') : 'Tidak ada SLA' }}
                    </p>
                    @if($ticket->resolution_deadline && !in_array($ticket->status, ['resolved', 'closed']))
                        <p class="text-xs font-medium mt-1 {{ $ticket->sla_status === 'breached' ? 'text-red-500' : ($ticket->sla_status === 'near_deadline' ? 'text-orange-500' : 'text-green-500') }}">
                            Sisa waktu: {{ $ticket->resolution_deadline->diffForHumans() }}
                        </p>
                    @endif
                </div>
                @if($ticket->asset)
                    <div class="pt-4 border-t border-slate-100">
                        <p class="text-xs text-slate-400 mb-1">Aset Terkait</p>
                        <p class="text-sm font-medium text-slate-700">{{ $ticket->asset->asset_code }}</p>
                        <p class="text-xs text-slate-500">{{ $ticket->asset->brand }} {{ $ticket->asset->model }}</p>
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>

@endsection
