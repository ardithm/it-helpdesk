@extends('layouts.app')

@section('title', 'Kelola Tiket (Helpdesk)')
@section('page-title', 'Kelola Tiket Masuk')
@section('page-subtitle', 'Pantau, klasifikasikan, dan tugaskan tiket ke teknisi')

@section('content')

<div class="bg-white rounded-2xl shadow-sm overflow-hidden flex flex-col h-[calc(100vh-140px)]">
    
    {{-- Top Actions & Filters --}}
    <div class="px-6 py-4 border-b border-slate-100 flex flex-col lg:flex-row lg:items-center justify-between gap-4 flex-shrink-0">
        <form method="GET" action="{{ route('helpdesk.tickets.index') }}" class="flex flex-1 flex-wrap items-center gap-3">
            
            {{-- Search --}}
            <div class="relative flex-1 min-w-[200px] max-w-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor, judul, pelapor..." class="w-full pl-9 pr-4 py-2 text-sm bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-purple-500/20 focus:bg-white transition-colors">
            </div>
            
            {{-- Status Filter --}}
            <select name="status" class="py-2 pl-3 pr-8 text-sm bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-purple-500/20 focus:bg-white transition-colors" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Open</option>
                <option value="assigned" {{ request('status') === 'assigned' ? 'selected' : '' }}>Assigned</option>
                <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="waiting" {{ request('status') === 'waiting' ? 'selected' : '' }}>Waiting</option>
                <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
            </select>

            {{-- Priority Filter --}}
            <select name="priority" class="py-2 pl-3 pr-8 text-sm bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-purple-500/20 focus:bg-white transition-colors" onchange="this.form.submit()">
                <option value="">Semua Prioritas</option>
                <option value="critical" {{ request('priority') === 'critical' ? 'selected' : '' }}>Critical</option>
                <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
                <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
            </select>

            {{-- Category Filter --}}
            <select name="category_id" class="py-2 pl-3 pr-8 text-sm bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-purple-500/20 focus:bg-white transition-colors" onchange="this.form.submit()">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </form>
    </div>

    {{-- Table / Data --}}
    <div class="flex-1 overflow-auto">
        @if($tickets->isEmpty())
            <div class="px-6 py-20 text-center flex flex-col items-center justify-center h-full">
                <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>
                </div>
                <p class="text-slate-600 font-medium">Tidak ada tiket ditemukan</p>
                <p class="text-sm text-slate-400 mt-1">Sesuaikan filter atau kata kunci pencarian Anda.</p>
            </div>
        @else
            <table class="w-full text-left">
                <thead class="sticky top-0 bg-white/95 backdrop-blur z-10 shadow-[0_1px_2px_rgba(0,0,0,0.02)]">
                    <tr>
                        <th class="px-6 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wider">Tiket</th>
                        <th class="px-6 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wider">Pelapor</th>
                        <th class="px-6 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wider">Status & SLA</th>
                        <th class="px-6 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wider">Kategori / Prioritas</th>
                        <th class="px-6 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wider">Penugasan</th>
                        <th class="px-6 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($tickets as $ticket)
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            
                            {{-- Info Tiket --}}
                            <td class="px-6 py-4">
                                <a href="{{ route('helpdesk.tickets.show', $ticket) }}" class="text-sm font-semibold text-purple-600 hover:text-purple-700 block mb-1">
                                    {{ $ticket->ticket_number }}
                                </a>
                                <p class="text-xs text-slate-500 max-w-[200px] truncate" title="{{ $ticket->title }}">{{ $ticket->title }}</p>
                                <p class="text-[10px] text-slate-400 mt-1">{{ $ticket->created_at->format('d M Y H:i') }}</p>
                            </td>
                            
                            {{-- Info Pelapor --}}
                            <td class="px-6 py-4">
                                <p class="text-sm font-medium text-slate-700">{{ $ticket->user->name }}</p>
                                <p class="text-xs text-slate-500 mt-0.5">{{ $ticket->user->department?->name ?? 'Tanpa Departemen' }}</p>
                            </td>
                            
                            {{-- Status & SLA --}}
                            <td class="px-6 py-4">
                                <div class="mb-1.5">
                                    @include('components.status-badge', ['status' => $ticket->status])
                                </div>
                                @if(!in_array($ticket->status, ['resolved', 'closed']))
                                    @php $sla = $ticket->sla_status; @endphp
                                    <span class="inline-flex items-center gap-1 text-[11px] font-medium {{ $sla === 'breached' ? 'text-red-600' : ($sla === 'near_deadline' ? 'text-orange-500' : 'text-slate-500') }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                        {{ $sla === 'breached' ? 'SLA Breached' : ($ticket->resolution_deadline ? $ticket->resolution_deadline->diffForHumans() : 'No SLA') }}
                                    </span>
                                @endif
                            </td>
                            
                            {{-- Kategori & Prioritas --}}
                            <td class="px-6 py-4">
                                <p class="text-sm text-slate-600 mb-1.5">{{ $ticket->category?->name ?? 'Belum ada' }}</p>
                                @include('components.priority-badge', ['priority' => $ticket->priority])
                            </td>
                            
                            {{-- Penugasan --}}
                            <td class="px-6 py-4">
                                @if($ticket->activeAssignment)
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 bg-purple-100 rounded-full flex items-center justify-center">
                                            <span class="text-[10px] font-semibold text-purple-600">{{ strtoupper(substr($ticket->activeAssignment->technician->name, 0, 1)) }}</span>
                                        </div>
                                        <p class="text-sm text-slate-700">{{ $ticket->activeAssignment->technician->name }}</p>
                                    </div>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2 py-1 bg-yellow-50 text-yellow-700 text-xs font-medium rounded-lg border border-yellow-200/60">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" x2="19" y1="8" y2="14"/><line x1="22" x2="16" y1="11" y2="11"/></svg>
                                        Belum di-assign
                                    </span>
                                @endif
                            </td>
                            
                            {{-- Aksi --}}
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('helpdesk.tickets.show', $ticket) }}" class="inline-flex items-center justify-center px-3 py-1.5 text-sm font-medium text-purple-600 bg-purple-50 hover:bg-purple-100 rounded-lg transition-colors">
                                    Kelola
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
