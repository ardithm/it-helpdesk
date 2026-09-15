@extends('layouts.app')

@section('title', 'Detail Tiket ' . $ticket->ticket_number)
@section('page-title', $ticket->ticket_number)
@section('page-subtitle', 'Dibuat pada ' . $ticket->created_at->format('d M Y, H:i'))

@section('content')

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Kiri: Konten Utama & Komentar --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Panel Detail Masalah --}}
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="p-6">
                <div class="flex items-start justify-between gap-4 mb-4">
                    <h2 class="text-lg font-semibold text-slate-800">{{ $ticket->title }}</h2>
                    <div class="flex-shrink-0">
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
            
            {{-- Action Bar: Konfirmasi Selesai --}}
            @if($ticket->status === \App\Models\Ticket::STATUS_RESOLVED)
                <div class="px-6 py-4 bg-green-50/50 border-t border-green-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <p class="text-sm font-medium text-green-800">Teknisi menyatakan masalah telah selesai.</p>
                        <p class="text-xs text-green-600 mt-0.5">Mohon konfirmasi jika layanan sudah berfungsi normal.</p>
                    </div>
                    <form action="{{ route('user.tickets.confirm', $ticket) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full sm:w-auto px-5 py-2 bg-green-500 hover:bg-green-600 text-white text-sm font-medium rounded-xl shadow-sm transition-colors">
                            Konfirmasi Selesai
                        </button>
                    </form>
                </div>
            @endif
        </div>

        {{-- Panel Komunikasi (Chat style) --}}
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden flex flex-col" style="min-height: 500px; max-height: 800px;">
            <div class="px-6 py-4 border-b border-slate-100 flex-shrink-0">
                <h2 class="text-base font-semibold text-slate-800 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-purple-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                    Diskusi Tiket
                </h2>
            </div>
            
            <div class="flex-1 overflow-y-auto p-6 space-y-6">
                @if($ticket->comments->where('is_internal', false)->isEmpty())
                    <div class="h-full flex flex-col items-center justify-center text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-slate-200 mb-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                        <p class="text-slate-500 text-sm">Belum ada diskusi pada tiket ini.</p>
                    </div>
                @else
                    @foreach($ticket->comments->where('is_internal', false) as $comment)
                        @php 
                            $isMe = $comment->user_id === auth()->id(); 
                        @endphp
                        <div class="flex flex-col {{ $isMe ? 'items-end' : 'items-start' }}">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-xs font-medium text-slate-700">{{ $isMe ? 'Anda' : $comment->user->name }}</span>
                                <span class="text-[10px] text-slate-400">{{ $comment->created_at->format('H:i') }}</span>
                            </div>
                            <div class="max-w-[85%] px-4 py-2.5 text-sm 
                                {{ $isMe 
                                    ? 'bg-purple-100 text-purple-900 rounded-2xl rounded-tr-sm' 
                                    : 'bg-slate-100 text-slate-800 rounded-2xl rounded-tl-sm' 
                                }}">
                                {!! nl2br(e($comment->comment)) !!}
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            @if(!in_array($ticket->status, [\App\Models\Ticket::STATUS_RESOLVED, \App\Models\Ticket::STATUS_CLOSED]))
                <div class="p-4 border-t border-slate-100 bg-slate-50/50 flex-shrink-0">
                    <form action="{{ route('user.tickets.comment', $ticket) }}" method="POST" class="flex gap-3">
                        @csrf
                        <textarea name="comment" rows="1" required placeholder="Ketik pesan balasan..." class="flex-1 min-h-[44px] max-h-32 px-4 py-2.5 bg-white border-none rounded-xl shadow-sm focus:ring-2 focus:ring-purple-500/20 text-sm transition-colors resize-y" oninput="this.style.height = '';this.style.height = this.scrollHeight + 'px'"></textarea>
                        <button type="submit" class="flex-shrink-0 h-[44px] px-5 bg-purple-600 hover:bg-purple-700 text-white rounded-xl shadow-sm transition-colors flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg>
                        </button>
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
                    <p class="text-xs text-slate-400 mb-1">Tipe</p>
                    <p class="text-sm font-medium text-slate-700 capitalize">{{ str_replace('_', ' ', $ticket->type) }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 mb-1">Kategori</p>
                    <p class="text-sm font-medium text-slate-700">{{ $ticket->category?->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 mb-1">Prioritas</p>
                    @include('components.priority-badge', ['priority' => $ticket->priority])
                </div>
                @if($ticket->asset)
                    <div>
                        <p class="text-xs text-slate-400 mb-1">Aset Terkait</p>
                        <p class="text-sm font-medium text-slate-700">{{ $ticket->asset->asset_code }}</p>
                        <p class="text-xs text-slate-500">{{ $ticket->asset->brand }} {{ $ticket->asset->model }}</p>
                    </div>
                @endif
                <div>
                    <p class="text-xs text-slate-400 mb-1">Ditangani Oleh</p>
                    <div class="flex items-center gap-2 mt-1">
                        @if($ticket->activeAssignment)
                            <div class="w-6 h-6 bg-purple-100 rounded-full flex items-center justify-center">
                                <span class="text-[10px] font-semibold text-purple-600">{{ strtoupper(substr($ticket->activeAssignment->technician->name, 0, 1)) }}</span>
                            </div>
                            <span class="text-sm font-medium text-slate-700">{{ $ticket->activeAssignment->technician->name }}</span>
                        @else
                            <span class="text-sm font-medium text-slate-500 italic">Belum ada</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Resolusi (Jika ada) --}}
        @if($ticket->resolution)
            <div class="bg-green-50 border border-green-100 rounded-2xl overflow-hidden">
                <div class="px-5 py-4 border-b border-green-200/60 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>
                    <h2 class="text-sm font-semibold text-green-800">Resolusi Teknisi</h2>
                </div>
                <div class="p-5">
                    <div class="prose prose-sm prose-green max-w-none text-green-900 whitespace-pre-wrap">{{ $ticket->resolution->resolution }}</div>
                    @if($ticket->resolution->sparepart)
                        <div class="mt-3 pt-3 border-t border-green-200/60">
                            <p class="text-xs font-semibold text-green-700 mb-1">Pergantian Komponen:</p>
                            <p class="text-sm text-green-800">{{ $ticket->resolution->sparepart }}</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- Log Histori (Opsional/Singkat) --}}
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h2 class="text-sm font-semibold text-slate-800">Riwayat Perubahan</h2>
            </div>
            <div class="p-5">
                <div class="space-y-4">
                    @foreach($ticket->histories()->latest()->take(5)->get() as $history)
                        <div class="relative pl-4 border-l border-slate-100 last:border-0">
                            <span class="absolute -left-1 top-1.5 w-2 h-2 rounded-full bg-slate-200"></span>
                            <p class="text-xs text-slate-500 mb-0.5">{{ $history->created_at->format('d M H:i') }}</p>
                            <p class="text-sm text-slate-700">
                                @if($history->action === 'status.changed')
                                    Status diubah ke <span class="font-medium text-slate-800 uppercase">{{ $history->new_value }}</span>
                                @elseif($history->action === 'ticket.assigned')
                                    {{ $history->new_value }}
                                @else
                                    {{ $history->new_value }}
                                @endif
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
</div>

@endsection
