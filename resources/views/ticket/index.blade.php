@extends('layouts.app')

@section('title', 'Tiket Saya')
@section('page-title', 'Tiket Saya')
@section('page-subtitle', 'Daftar semua permintaan layanan IT Anda')

@section('content')

<div class="bg-white rounded-2xl shadow-sm overflow-hidden flex flex-col h-[calc(100vh-140px)]">
    
    {{-- Top Actions & Filters --}}
    <div class="px-6 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 flex-shrink-0">
        <form method="GET" action="{{ route('user.tickets.index') }}" class="flex flex-1 items-center gap-3">
            <div class="relative w-full max-w-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor atau judul tiket..." class="w-full pl-9 pr-4 py-2 text-sm bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-purple-500/20 focus:bg-white transition-colors">
            </div>
            
            <select name="status" class="py-2 pl-3 pr-8 text-sm bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-purple-500/20 focus:bg-white transition-colors" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Open</option>
                <option value="assigned" {{ request('status') === 'assigned' ? 'selected' : '' }}>Assigned</option>
                <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="waiting" {{ request('status') === 'waiting' ? 'selected' : '' }}>Waiting</option>
                <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
            </select>
        </form>

        <a href="{{ route('user.tickets.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-xl hover:bg-purple-700 transition-colors flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
            Buat Tiket Baru
        </a>
    </div>

    {{-- Table / Data --}}
    <div class="flex-1 overflow-auto">
        @if($tickets->isEmpty())
            <div class="px-6 py-20 text-center flex flex-col items-center justify-center h-full">
                <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>
                </div>
                <p class="text-slate-600 font-medium">Belum ada tiket</p>
                <p class="text-sm text-slate-400 mt-1">Anda belum membuat permintaan layanan apapun.</p>
            </div>
        @else
            <table class="w-full text-left">
                <thead class="sticky top-0 bg-white/95 backdrop-blur z-10 shadow-[0_1px_2px_rgba(0,0,0,0.02)]">
                    <tr>
                        <th class="px-6 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wider">No. Tiket</th>
                        <th class="px-6 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wider">Judul & Kategori</th>
                        <th class="px-6 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wider">Prioritas</th>
                        <th class="px-6 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($tickets as $ticket)
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-6 py-4">
                                <a href="{{ route('user.tickets.show', $ticket) }}" class="text-sm font-semibold text-purple-600 hover:text-purple-700">
                                    {{ $ticket->ticket_number }}
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-medium text-slate-700 max-w-sm truncate">{{ $ticket->title }}</p>
                                <p class="text-xs text-slate-400 mt-0.5">{{ $ticket->category?->name ?? 'Tanpa Kategori' }}</p>
                            </td>
                            <td class="px-6 py-4">
                                @include('components.status-badge', ['status' => $ticket->status])
                            </td>
                            <td class="px-6 py-4">
                                @include('components.priority-badge', ['priority' => $ticket->priority])
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-slate-600">{{ $ticket->created_at->format('d M Y') }}</p>
                                <p class="text-xs text-slate-400">{{ $ticket->created_at->format('H:i') }}</p>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('user.tickets.show', $ticket) }}" class="inline-flex items-center justify-center p-2 text-slate-400 hover:text-purple-600 hover:bg-purple-50 rounded-xl transition-colors opacity-0 group-hover:opacity-100 focus:opacity-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    {{-- Pagination --}}
    @if($tickets->hasPages())
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 flex-shrink-0">
            {{ $tickets->links() }}
        </div>
    @endif
</div>

@endsection
