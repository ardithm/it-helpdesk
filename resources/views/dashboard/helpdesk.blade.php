@extends('layouts.app')

@section('title', 'Dashboard Helpdesk')
@section('page-title', 'Dashboard Helpdesk')
@section('page-subtitle', 'Pantau dan kelola seluruh tiket layanan IT')

@section('content')

{{-- Stat Cards --}}
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-7 gap-4 mb-8">
    @include('components.stat-card', ['label' => 'Open',        'value' => $stats['open'],        'color' => 'blue',   'icon' => '<circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/>'])
    @include('components.stat-card', ['label' => 'Assigned',    'value' => $stats['assigned'],    'color' => 'purple', 'icon' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>'])
    @include('components.stat-card', ['label' => 'In Progress', 'value' => $stats['in_progress'], 'color' => 'orange', 'icon' => '<path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>'])
    @include('components.stat-card', ['label' => 'Waiting',     'value' => $stats['waiting'],     'color' => 'orange', 'icon' => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>'])
    @include('components.stat-card', ['label' => 'Resolved',    'value' => $stats['resolved'],    'color' => 'green',  'icon' => '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/>'])
    @include('components.stat-card', ['label' => 'Closed',      'value' => $stats['closed'],      'color' => 'green',  'icon' => '<path d="M18 6 6 18M6 6l12 12"/>'])
    @include('components.stat-card', ['label' => 'SLA Breach',  'value' => $stats['breached'],    'color' => 'red',    'icon' => '<path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><path d="M12 9v4M12 17h.01"/>'])
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    {{-- Tiket Menunggu Tindakan --}}
    <div class="xl:col-span-2 bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-base font-semibold text-slate-800">Tiket Open — Butuh Tindakan</h2>
            <a href="{{ route('helpdesk.tickets.index', ['status' => 'open']) }}" class="text-sm text-purple-600 hover:text-purple-700 font-medium">Lihat Semua</a>
        </div>

        @if($pendingTickets->isEmpty())
            <div class="px-6 py-10 text-center">
                <p class="text-sm text-slate-500">Tidak ada tiket open saat ini.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-slate-100">
                            <th class="text-left px-6 py-3 text-xs uppercase text-slate-400 font-semibold">No. Tiket</th>
                            <th class="text-left px-6 py-3 text-xs uppercase text-slate-400 font-semibold">Pelapor</th>
                            <th class="text-left px-6 py-3 text-xs uppercase text-slate-400 font-semibold">Judul</th>
                            <th class="text-left px-6 py-3 text-xs uppercase text-slate-400 font-semibold">Kategori</th>
                            <th class="text-left px-6 py-3 text-xs uppercase text-slate-400 font-semibold">Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingTickets as $ticket)
                            <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-3.5">
                                    <a href="{{ route('helpdesk.tickets.show', $ticket) }}" class="text-sm font-medium text-purple-600 hover:text-purple-700">{{ $ticket->ticket_number }}</a>
                                </td>
                                <td class="px-6 py-3.5">
                                    <p class="text-sm text-slate-700">{{ $ticket->user->name }}</p>
                                    <p class="text-xs text-slate-400">{{ $ticket->user->department?->name }}</p>
                                </td>
                                <td class="px-6 py-3.5 text-sm text-slate-600 max-w-xs truncate">{{ $ticket->title }}</td>
                                <td class="px-6 py-3.5 text-sm text-slate-500">{{ $ticket->category?->name ?? '-' }}</td>
                                <td class="px-6 py-3.5 text-sm text-slate-400">{{ $ticket->created_at->diffForHumans() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Panel Kanan --}}
    <div class="space-y-6">

        {{-- Tiket Mendekati SLA Deadline --}}
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h2 class="text-sm font-semibold text-slate-800">Peringatan SLA</h2>
            </div>
            @if($urgentTickets->isEmpty())
                <div class="px-5 py-6 text-center">
                    <p class="text-sm text-slate-400">Semua tiket aman dari SLA breach.</p>
                </div>
            @else
                <div class="divide-y divide-slate-50">
                    @foreach($urgentTickets as $ticket)
                        <a href="{{ route('helpdesk.tickets.show', $ticket) }}" class="block px-5 py-3 hover:bg-red-50/50 transition-colors">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm font-medium text-slate-700">{{ $ticket->ticket_number }}</span>
                                <span class="text-xs font-medium {{ $ticket->sla_status === 'breached' ? 'text-red-600' : 'text-orange-500' }}">
                                    {{ $ticket->sla_status === 'breached' ? 'BREACHED' : $ticket->resolution_deadline?->diffForHumans() }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 truncate">{{ $ticket->title }}</p>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Daftar Teknisi --}}
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h2 class="text-sm font-semibold text-slate-800">Beban Teknisi</h2>
            </div>
            <div class="divide-y divide-slate-50">
                @foreach($technicians as $tech)
                    <div class="px-5 py-3 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                <span class="text-xs font-semibold text-purple-600">{{ strtoupper(substr($tech->name, 0, 1)) }}</span>
                            </div>
                            <span class="text-sm text-slate-700">{{ $tech->name }}</span>
                        </div>
                        <span class="text-sm font-medium {{ $tech->active_tickets > 3 ? 'text-red-500' : ($tech->active_tickets > 0 ? 'text-orange-500' : 'text-green-500') }}">
                            {{ $tech->active_tickets }} tiket
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

    </div>
</div>

@endsection
