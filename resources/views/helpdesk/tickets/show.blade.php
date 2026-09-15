@extends('layouts.app')

@section('title', 'Kelola Tiket ' . $ticket->ticket_number)
@section('page-title', 'Manajemen Tiket: ' . $ticket->ticket_number)
@section('page-subtitle', 'Pelapor: ' . $ticket->user->name . ' (' . ($ticket->user->department?->name ?? '-') . ')')

@section('content')

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Kiri: Detail & Chat --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Panel Detail Utama --}}
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
            
            {{-- Action Bar Bawah (Update Status Manual) --}}
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
                <span class="text-sm font-medium text-slate-600">Ubah Status Manual:</span>
                <form action="{{ route('helpdesk.tickets.status', $ticket) }}" method="POST" class="flex items-center gap-3">
                    @csrf
                    @method('PATCH')
                    <select name="status" class="py-2 pl-3 pr-8 text-sm bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-purple-500/20 transition-colors">
                        <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>Open</option>
                        <option value="assigned" {{ $ticket->status === 'assigned' ? 'selected' : '' }}>Assigned</option>
                        <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="waiting" {{ $ticket->status === 'waiting' ? 'selected' : '' }}>Waiting</option>
                        <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                        <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                    <button type="submit" class="px-3 py-2 bg-slate-200 text-slate-700 hover:bg-slate-300 text-sm font-medium rounded-xl transition-colors">Update</button>
                </form>
            </div>
        </div>

        {{-- Panel Komunikasi (Chat Internal & Eksternal) --}}
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
                        <p class="text-slate-500 text-sm">Belum ada diskusi atau catatan internal.</p>
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
                                    @if($comment->user->role !== 'user' && !$isMe) <span class="text-[10px] text-purple-600 bg-purple-50 px-1.5 py-0.5 rounded ml-1">{{ $comment->user->role }}</span> @endif
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
                                        Catatan Internal (Teknisi/Helpdesk)
                                    </div>
                                @endif
                                {!! nl2br(e($comment->comment)) !!}
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            <div class="p-4 border-t border-slate-100 bg-white flex-shrink-0">
                <form action="{{ route('helpdesk.tickets.comment', $ticket) }}" method="POST">
                    @csrf
                    <div class="flex items-end gap-3">
                        <div class="flex-1">
                            <textarea name="comment" rows="1" required placeholder="Ketik balasan atau catatan internal..." class="w-full min-h-[44px] max-h-32 px-4 py-2.5 bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-purple-500/20 text-sm transition-colors resize-y mb-2" oninput="this.style.height = '';this.style.height = this.scrollHeight + 'px'"></textarea>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="is_internal" value="1" class="w-4 h-4 text-purple-600 border-slate-300 rounded focus:ring-purple-500">
                                <span class="text-xs font-medium text-slate-600">Simpan sebagai Catatan Internal (User tidak bisa melihat ini)</span>
                            </label>
                        </div>
                        <button type="submit" class="flex-shrink-0 h-[44px] px-5 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-xl shadow-sm transition-colors mb-6">
                            Kirim
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Kanan: Sidebar Aksi (Assign & Klasifikasi) --}}
    <div class="space-y-6">

        {{-- Panel Assignment --}}
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-purple-100">
            <div class="px-5 py-4 bg-purple-50 border-b border-purple-100 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-purple-800">Penugasan Teknisi</h2>
                @if($ticket->activeAssignment)
                    <span class="w-2 h-2 rounded-full bg-green-500" title="Assigned"></span>
                @else
                    <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse" title="Not Assigned"></span>
                @endif
            </div>
            <div class="p-5">
                @if($ticket->activeAssignment)
                    <div class="flex items-center gap-3 mb-4 p-3 bg-slate-50 rounded-xl border border-slate-100">
                        <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-sm font-bold text-purple-600">{{ strtoupper(substr($ticket->activeAssignment->technician->name, 0, 1)) }}</span>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-800">{{ $ticket->activeAssignment->technician->name }}</p>
                            <p class="text-[10px] text-slate-500 mt-0.5">Assigned: {{ $ticket->activeAssignment->created_at->format('d M H:i') }}</p>
                        </div>
                    </div>
                @endif

                <form action="{{ route('helpdesk.tickets.assign', $ticket) }}" method="POST">
                    @csrf
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ $ticket->activeAssignment ? 'Ubah Penugasan Ke:' : 'Pilih Teknisi:' }}</label>
                    <div class="flex gap-2">
                        <select name="technician_id" required class="flex-1 px-3 py-2 text-sm bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-purple-500/20 transition-colors">
                            <option value="">-- Pilih Teknisi --</option>
                            @foreach($technicians as $tech)
                                <option value="{{ $tech->id }}" {{ $ticket->activeAssignment?->technician_id === $tech->id ? 'selected' : '' }}>
                                    {{ $tech->name }} ({{ $tech->active_tickets ?? 0 }} aktif)
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-xl hover:bg-purple-700 transition-colors shadow-sm">
                            Assign
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Panel Klasifikasi --}}
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h2 class="text-sm font-semibold text-slate-800">Klasifikasi & SLA</h2>
            </div>
            <div class="p-5">
                <form action="{{ route('helpdesk.tickets.classify', $ticket) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1.5">Tipe Tiket</label>
                        <select name="type" required class="w-full px-3 py-2 text-sm bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-purple-500/20 transition-colors">
                            <option value="incident" {{ $ticket->type === 'incident' ? 'selected' : '' }}>Incident</option>
                            <option value="service_request" {{ $ticket->type === 'service_request' ? 'selected' : '' }}>Service Request</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1.5">Kategori</label>
                        <select name="category_id" required class="w-full px-3 py-2 text-sm bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-purple-500/20 transition-colors">
                            <option value="">-- Pilih --</option>
                            @foreach(\App\Models\TicketCategory::active()->get() as $cat)
                                <option value="{{ $cat->id }}" {{ $ticket->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1.5">Prioritas (Mempengaruhi SLA)</label>
                        <select name="priority" required class="w-full px-3 py-2 text-sm bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-purple-500/20 transition-colors">
                            <option value="critical" {{ $ticket->priority === 'critical' ? 'selected' : '' }}>Critical</option>
                            <option value="high" {{ $ticket->priority === 'high' ? 'selected' : '' }}>High</option>
                            <option value="medium" {{ $ticket->priority === 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="low" {{ $ticket->priority === 'low' ? 'selected' : '' }}>Low</option>
                        </select>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full py-2 bg-slate-800 text-white text-sm font-medium rounded-xl hover:bg-slate-900 transition-colors">
                            Simpan Klasifikasi
                        </button>
                    </div>
                </form>

                {{-- Info Deadline Saat ini --}}
                @if($ticket->resolution_deadline)
                    <div class="mt-4 pt-4 border-t border-slate-100">
                        <p class="text-xs text-slate-400 mb-1">Target Resolusi (SLA)</p>
                        <p class="text-sm font-semibold {{ $ticket->sla_status === 'breached' ? 'text-red-600' : 'text-slate-700' }}">
                            {{ $ticket->resolution_deadline->format('d M Y, H:i') }}
                        </p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Resolusi --}}
        @if($ticket->resolution)
            <div class="bg-green-50 border border-green-100 rounded-2xl overflow-hidden">
                <div class="px-5 py-4 border-b border-green-200/60 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>
                    <h2 class="text-sm font-semibold text-green-800">Laporan Resolusi</h2>
                </div>
                <div class="p-5">
                    <p class="text-xs text-green-600 mb-1">Teknisi: {{ $ticket->resolution->technician->name }}</p>
                    <div class="prose prose-sm prose-green max-w-none text-green-900 whitespace-pre-wrap">{{ $ticket->resolution->resolution }}</div>
                </div>
            </div>
        @endif

    </div>
</div>

@endsection
