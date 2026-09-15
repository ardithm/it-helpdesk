@extends('layouts.app')

@section('title', 'Dashboard Teknisi')
@section('page-title', 'Dashboard Teknisi')
@section('page-subtitle', 'Daftar tugas dan tiket yang ditugaskan kepada Anda')

@section('content')

{{-- Stat Cards --}}
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 mb-8">
    @include('components.stat-card', ['label' => 'Assigned',      'value' => $stats['assigned'],      'color' => 'purple', 'icon' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>'])
    @include('components.stat-card', ['label' => 'In Progress',   'value' => $stats['in_progress'],   'color' => 'orange', 'icon' => '<path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>'])
    @include('components.stat-card', ['label' => 'Waiting',       'value' => $stats['waiting'],       'color' => 'blue',   'icon' => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>'])
    @include('components.stat-card', ['label' => 'SLA Breach',    'value' => $stats['breached'],      'color' => 'red',    'icon' => '<path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><path d="M12 9v4M12 17h.01"/>'])
    @include('components.stat-card', ['label' => 'Resolved Hari Ini', 'value' => $stats['resolved_today'], 'color' => 'green', 'icon' => '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/>'])
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    {{-- Tiket Aktif --}}
    <div class="xl:col-span-2 bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-base font-semibold text-slate-800">Tiket Aktif Saya</h2>
            <a href="{{ route('technician.tickets.index') }}" class="text-sm text-purple-600 hover:text-purple-700 font-medium">Lihat Semua</a>
        </div>

        @if($activeTickets->isEmpty())
            <div class="px-6 py-10 text-center">
                <p class="text-sm text-slate-500">Tidak ada tiket aktif saat ini.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-slate-100">
                            <th class="text-left px-6 py-3 text-xs uppercase text-slate-400 font-semibold">No. Tiket</th>
                            <th class="text-left px-6 py-3 text-xs uppercase text-slate-400 font-semibold">Judul</th>
                            <th class="text-left px-6 py-3 text-xs uppercase text-slate-400 font-semibold">Status</th>
                            <th class="text-left px-6 py-3 text-xs uppercase text-slate-400 font-semibold">Prioritas</th>
                            <th class="text-left px-6 py-3 text-xs uppercase text-slate-400 font-semibold">SLA</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($activeTickets as $ticket)
                            <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-3.5">
                                    <a href="{{ route('technician.tickets.show', $ticket) }}" class="text-sm font-medium text-purple-600 hover:text-purple-700">{{ $ticket->ticket_number }}</a>
                                </td>
                                <td class="px-6 py-3.5">
                                    <p class="text-sm text-slate-600 truncate max-w-xs">{{ $ticket->title }}</p>
                                    <p class="text-xs text-slate-400">{{ $ticket->user->name }} — {{ $ticket->user->department?->name }}</p>
                                </td>
                                <td class="px-6 py-3.5">
                                    @include('components.status-badge', ['status' => $ticket->status])
                                </td>
                                <td class="px-6 py-3.5">
                                    @include('components.priority-badge', ['priority' => $ticket->priority])
                                </td>
                                <td class="px-6 py-3.5">
                                    @php $sla = $ticket->sla_status; @endphp
                                    <span class="inline-flex items-center gap-1.5 text-xs font-medium {{ $sla === 'breached' ? 'text-red-600' : ($sla === 'near_deadline' ? 'text-orange-500' : 'text-green-600') }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $sla === 'breached' ? 'bg-red-500' : ($sla === 'near_deadline' ? 'bg-orange-400' : 'bg-green-500') }}"></span>
                                        {{ $sla === 'breached' ? 'Breach' : ($sla === 'near_deadline' ? $ticket->resolution_deadline?->diffForHumans() : 'On Track') }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Panel Urgent --}}
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100">
            <h2 class="text-sm font-semibold text-slate-800">Perlu Perhatian Segera</h2>
        </div>
        @if($urgentTickets->isEmpty())
            <div class="px-5 py-8 text-center">
                <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-green-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/>
                    </svg>
                </div>
                <p class="text-sm text-slate-500">Semua tiket dalam kondisi aman.</p>
            </div>
        @else
            <div class="divide-y divide-slate-50">
                @foreach($urgentTickets as $ticket)
                    <a href="{{ route('technician.tickets.show', $ticket) }}" class="block px-5 py-3.5 hover:bg-red-50/50 transition-colors">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-sm font-medium text-slate-700">{{ $ticket->ticket_number }}</span>
                            <span class="text-xs font-medium {{ $ticket->sla_status === 'breached' ? 'text-red-600' : 'text-orange-500' }}">
                                {{ $ticket->sla_status === 'breached' ? 'BREACHED' : $ticket->resolution_deadline?->diffForHumans() }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 truncate">{{ $ticket->title }}</p>
                        <p class="text-xs text-slate-400 mt-0.5">{{ $ticket->user->name }}</p>
                    </a>
                @endforeach
            </div>
        @endif
    </div>

</div>

@endsection
