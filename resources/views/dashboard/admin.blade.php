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

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- GRAFIK VISUALISASI --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

    {{-- Line Chart: Tren Tiket 7 Hari Terakhir --}}
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-base font-semibold text-slate-800">Tren Tiket Masuk</h3>
                <p class="text-xs text-slate-400 mt-0.5">7 hari terakhir</p>
            </div>
            <div class="w-10 h-10 bg-purple-50 rounded-xl flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-purple-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="m19 9-5 5-4-4-3 3"/></svg>
            </div>
        </div>
        <div class="relative" style="height: 260px;">
            <canvas id="chartTrend"></canvas>
        </div>
    </div>

    {{-- Doughnut Chart: Distribusi Status --}}
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-base font-semibold text-slate-800">Status Tiket</h3>
                <p class="text-xs text-slate-400 mt-0.5">Distribusi saat ini</p>
            </div>
            <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.21 15.89A10 10 0 1 1 8 2.83"/><path d="M22 12A10 10 0 0 0 12 2v10z"/></svg>
            </div>
        </div>
        <div class="relative flex items-center justify-center" style="height: 260px;">
            <canvas id="chartStatus"></canvas>
        </div>
    </div>

</div>

{{-- Row: Kategori Chart + Technician Performance --}}
<div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-8">

    {{-- Doughnut Chart: Distribusi Kategori --}}
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-base font-semibold text-slate-800">Tiket per Kategori</h3>
                <p class="text-xs text-slate-400 mt-0.5">Top 6 kategori</p>
            </div>
            <div class="w-10 h-10 bg-green-50 rounded-xl flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-green-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2H2v10l9.29 9.29c.94.94 2.48.94 3.42 0l6.58-6.58c.94-.94.94-2.48 0-3.42L12 2Z"/><path d="M7 7h.01"/></svg>
            </div>
        </div>
        <div class="relative flex items-center justify-center" style="height: 260px;">
            <canvas id="chartCategory"></canvas>
        </div>
    </div>

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
</div>

{{-- Performa Teknisi (Horizontal Bar Chart) --}}
<div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-base font-semibold text-slate-800">Performa Teknisi</h3>
                <p class="text-xs text-slate-400 mt-0.5">Top 5 — Tiket diselesaikan</p>
            </div>
            <div class="w-10 h-10 bg-orange-50 rounded-xl flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-orange-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
        </div>
        <div class="relative" style="height: 220px;">
            <canvas id="chartTechnician"></canvas>
        </div>
    </div>

    {{-- Placeholder / Summary --}}
    <div class="bg-white rounded-2xl shadow-sm p-6 flex flex-col justify-center">
        <div class="text-center">
            <div class="w-16 h-16 mx-auto bg-purple-50 rounded-2xl flex items-center justify-center mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-purple-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/><path d="M12 18v-6"/><path d="M8 18v-1"/><path d="M16 18v-3"/></svg>
            </div>
            <h3 class="text-lg font-bold text-slate-800 mb-2">Laporan Lengkap</h3>
            <p class="text-sm text-slate-500 mb-4">Lihat laporan detail dengan filter lanjutan, export ke Excel & PDF.</p>
            <a href="{{ route('admin.reports.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-xl shadow-sm transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
                Buka Laporan
            </a>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {

    const fontFamily = "'Inter', sans-serif";
    Chart.defaults.font.family = fontFamily;
    Chart.defaults.font.size = 12;
    Chart.defaults.color = '#94A3B8';

    // ── 1. Line Chart: Tren Tiket ────────────────────────────────────────
    const trendCtx = document.getElementById('chartTrend').getContext('2d');
    const gradient = trendCtx.createLinearGradient(0, 0, 0, 260);
    gradient.addColorStop(0, 'rgba(139, 92, 246, 0.15)');
    gradient.addColorStop(1, 'rgba(139, 92, 246, 0)');

    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: @json($chartTrend['labels']),
            datasets: [{
                label: 'Tiket Masuk',
                data: @json($chartTrend['data']),
                borderColor: '#8B5CF6',
                backgroundColor: gradient,
                borderWidth: 2.5,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#8B5CF6',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1E293B',
                    titleColor: '#F8FAFC',
                    bodyColor: '#CBD5E1',
                    padding: 12,
                    cornerRadius: 10,
                    displayColors: false,
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: '#94A3B8', font: { size: 11 } },
                },
                y: {
                    beginAtZero: true,
                    grid: { color: '#F1F5F9', drawBorder: false },
                    ticks: {
                        color: '#94A3B8',
                        font: { size: 11 },
                        stepSize: 1,
                        callback: v => Number.isInteger(v) ? v : null,
                    },
                    border: { display: false },
                }
            }
        }
    });

    // ── 2. Doughnut Chart: Distribusi Status ──────────────────────────────
    new Chart(document.getElementById('chartStatus'), {
        type: 'doughnut',
        data: {
            labels: @json($chartStatus['labels']),
            datasets: [{
                data: @json($chartStatus['data']),
                backgroundColor: [
                    '#3B82F6', // Open — blue
                    '#8B5CF6', // Assigned — purple
                    '#F59E0B', // In Progress — amber
                    '#F97316', // Waiting — orange
                    '#10B981', // Resolved — emerald
                    '#6B7280', // Closed — gray
                ],
                borderWidth: 0,
                hoverOffset: 8,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '68%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 12,
                        usePointStyle: true,
                        pointStyle: 'circle',
                        font: { size: 11 }
                    }
                },
                tooltip: {
                    backgroundColor: '#1E293B',
                    padding: 10,
                    cornerRadius: 8,
                }
            }
        }
    });

    // ── 3. Doughnut Chart: Distribusi Kategori ───────────────────────────
    new Chart(document.getElementById('chartCategory'), {
        type: 'doughnut',
        data: {
            labels: @json($chartCategory['labels']),
            datasets: [{
                data: @json($chartCategory['data']),
                backgroundColor: [
                    '#8B5CF6', '#6366F1', '#3B82F6', '#14B8A6', '#F59E0B', '#EF4444'
                ],
                borderWidth: 0,
                hoverOffset: 8,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '68%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 12,
                        usePointStyle: true,
                        pointStyle: 'circle',
                        font: { size: 11 }
                    }
                },
                tooltip: {
                    backgroundColor: '#1E293B',
                    padding: 10,
                    cornerRadius: 8,
                }
            }
        }
    });

    // ── 4. Horizontal Bar Chart: Performa Teknisi ────────────────────────
    new Chart(document.getElementById('chartTechnician'), {
        type: 'bar',
        data: {
            labels: @json($technicianPerformance->pluck('name')),
            datasets: [{
                label: 'Tiket Selesai',
                data: @json($technicianPerformance->pluck('total_handled')),
                backgroundColor: [
                    'rgba(139, 92, 246, 0.8)',
                    'rgba(99, 102, 241, 0.8)',
                    'rgba(59, 130, 246, 0.8)',
                    'rgba(20, 184, 166, 0.8)',
                    'rgba(245, 158, 11, 0.8)',
                ],
                borderRadius: 8,
                borderSkipped: false,
                barThickness: 28,
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1E293B',
                    padding: 10,
                    cornerRadius: 8,
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    grid: { color: '#F1F5F9', drawBorder: false },
                    ticks: {
                        stepSize: 1,
                        callback: v => Number.isInteger(v) ? v : null,
                        font: { size: 11 }
                    },
                    border: { display: false },
                },
                y: {
                    grid: { display: false },
                    ticks: { font: { size: 12 }, color: '#475569' },
                }
            }
        }
    });

});
</script>
@endpush
