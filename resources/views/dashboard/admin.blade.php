@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard Admin')
@section('page-subtitle', 'Ringkasan performa layanan IT')

@section('content')

{{-- KPI Cards --}}
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
    @include('components.stat-card', ['label' => 'Total Tiket',  'value' => $stats['total'],       'color' => 'purple', 'icon' => '<path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/>'])
    @include('components.stat-card', ['label' => 'Open',         'value' => $stats['open'],         'color' => 'blue',   'icon' => '<circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/>'])
    @include('components.stat-card', ['label' => 'In Progress',  'value' => $stats['in_progress'],  'color' => 'orange', 'icon' => '<path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>'])
    @include('components.stat-card', ['label' => 'Resolved',     'value' => $stats['resolved'],     'color' => 'green',  'icon' => '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/>'])
    @include('components.stat-card', ['label' => 'Closed',       'value' => $stats['closed'],       'color' => 'green',  'icon' => '<path d="M18 6 6 18M6 6l12 12"/>'])
    @include('components.stat-card', ['label' => 'SLA Breach',   'value' => $stats['breached'],     'color' => 'red',    'icon' => '<path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><path d="M12 9v4M12 17h.01"/>'])
</div>

{{-- KPI Row 2: SLA + Rating --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 mb-8">

    {{-- SLA Compliance --}}
    <div class="bg-white rounded-2xl p-5 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <p class="text-sm text-slate-500 font-medium">SLA Compliance (Bulan Ini)</p>
            <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-purple-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/><path d="m9 12 2 2 4-4"/>
                </svg>
            </div>
        </div>
        <p class="text-3xl font-bold {{ $slaCompliance >= 80 ? 'text-green-600' : ($slaCompliance >= 50 ? 'text-orange-500' : 'text-red-600') }}">
            {{ $slaCompliance }}%
        </p>
        <div class="mt-3 w-full bg-slate-100 rounded-full h-2">
            <div class="h-2 rounded-full transition-all duration-500 {{ $slaCompliance >= 80 ? 'bg-green-500' : ($slaCompliance >= 50 ? 'bg-orange-400' : 'bg-red-500') }}"
                 style="width: {{ min($slaCompliance, 100) }}%"></div>
        </div>
    </div>

    {{-- Customer Satisfaction --}}
    <div class="bg-white rounded-2xl p-5 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <p class="text-sm text-slate-500 font-medium">Customer Satisfaction</p>
            <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-green-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a.53.53 0 0 0 .4.29l5.16.756a.53.53 0 0 1 .294.904l-3.733 3.638a.53.53 0 0 0-.153.469l.882 5.14a.53.53 0 0 1-.77.56l-4.614-2.425a.53.53 0 0 0-.494 0L6.44 18.73a.53.53 0 0 1-.77-.56l.882-5.14a.53.53 0 0 0-.153-.47L2.665 8.924a.53.53 0 0 1 .294-.903l5.16-.756a.53.53 0 0 0 .4-.29z"/>
                </svg>
            </div>
        </div>
        <p class="text-3xl font-bold text-slate-800">
            {{ $avgRating ? number_format($avgRating, 1) : '-' }}
            <span class="text-base font-medium text-slate-400">/ 5</span>
        </p>
        @if($avgRating)
            <div class="flex gap-1 mt-3">
                @for($i = 1; $i <= 5; $i++)
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 {{ $i <= round($avgRating) ? 'text-yellow-400' : 'text-slate-200' }}" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a.53.53 0 0 0 .4.29l5.16.756a.53.53 0 0 1 .294.904l-3.733 3.638a.53.53 0 0 0-.153.469l.882 5.14a.53.53 0 0 1-.77.56l-4.614-2.425a.53.53 0 0 0-.494 0L6.44 18.73a.53.53 0 0 1-.77-.56l.882-5.14a.53.53 0 0 0-.153-.47L2.665 8.924a.53.53 0 0 1 .294-.903l5.16-.756a.53.53 0 0 0 .4-.29z"/>
                    </svg>
                @endfor
            </div>
        @endif
    </div>

    {{-- Distribusi Priority --}}
    <div class="bg-white rounded-2xl p-5 shadow-sm">
        <p class="text-sm text-slate-500 font-medium mb-4">Distribusi Prioritas</p>
        @php
            $priorityLabels = ['critical' => 'Critical', 'high' => 'High', 'medium' => 'Medium', 'low' => 'Low'];
            $priorityColors = ['critical' => 'bg-red-500', 'high' => 'bg-orange-500', 'medium' => 'bg-blue-500', 'low' => 'bg-slate-400'];
            $totalP = max($byPriority->sum(), 1);
        @endphp
        <div class="space-y-3">
            @foreach($priorityLabels as $key => $label)
                @php $count = $byPriority[$key] ?? 0; $pct = round(($count / $totalP) * 100); @endphp
                <div>
                    <div class="flex items-center justify-between text-sm mb-1">
                        <span class="text-slate-600">{{ $label }}</span>
                        <span class="text-slate-500 font-medium">{{ $count }}</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-1.5">
                        <div class="h-1.5 rounded-full {{ $priorityColors[$key] }} transition-all duration-500" style="width: {{ $pct }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    {{-- Tiket Terbaru --}}
    <div class="xl:col-span-2 bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-base font-semibold text-slate-800">Tiket Terbaru</h2>
            <a href="{{ route('helpdesk.tickets.index') }}" class="text-sm text-purple-600 hover:text-purple-700 font-medium">Lihat Semua</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-slate-100">
                        <th class="text-left px-6 py-3 text-xs uppercase text-slate-400 font-semibold">No. Tiket</th>
                        <th class="text-left px-6 py-3 text-xs uppercase text-slate-400 font-semibold">Pelapor</th>
                        <th class="text-left px-6 py-3 text-xs uppercase text-slate-400 font-semibold">Status</th>
                        <th class="text-left px-6 py-3 text-xs uppercase text-slate-400 font-semibold">Prioritas</th>
                        <th class="text-left px-6 py-3 text-xs uppercase text-slate-400 font-semibold">Teknisi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentTickets as $ticket)
                        <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-3.5">
                                <a href="{{ route('helpdesk.tickets.show', $ticket) }}" class="text-sm font-medium text-purple-600 hover:text-purple-700">{{ $ticket->ticket_number }}</a>
                            </td>
                            <td class="px-6 py-3.5">
                                <p class="text-sm text-slate-700">{{ $ticket->user->name }}</p>
                                <p class="text-xs text-slate-400">{{ $ticket->user->department?->name }}</p>
                            </td>
                            <td class="px-6 py-3.5">
                                @include('components.status-badge', ['status' => $ticket->status])
                            </td>
                            <td class="px-6 py-3.5">
                                @include('components.priority-badge', ['priority' => $ticket->priority])
                            </td>
                            <td class="px-6 py-3.5 text-sm text-slate-500">
                                {{ $ticket->activeAssignment?->technician?->name ?? '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Top Technician --}}
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100">
            <h2 class="text-sm font-semibold text-slate-800">Performa Teknisi</h2>
        </div>
        @if($technicianPerformance->isEmpty())
            <div class="px-5 py-6 text-center">
                <p class="text-sm text-slate-400">Belum ada data performa.</p>
            </div>
        @else
            <div class="divide-y divide-slate-50">
                @foreach($technicianPerformance as $i => $tech)
                    <div class="px-5 py-3.5 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-semibold {{ $i === 0 ? 'bg-purple-100 text-purple-600' : 'bg-slate-100 text-slate-500' }}">
                                {{ $i + 1 }}
                            </span>
                            <span class="text-sm text-slate-700">{{ $tech->name }}</span>
                        </div>
                        <span class="text-sm font-medium text-slate-500">{{ $tech->total_handled }} selesai</span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>

@endsection
