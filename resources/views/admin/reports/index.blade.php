@extends('layouts.app')

@section('title', 'Laporan Tiket')
@section('page-title', 'Laporan & Analitik')
@section('page-subtitle', 'Ringkasan performa tiket, SLA, dan kepuasan pengguna')

@section('content')

{{-- Summary Stats --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    @include('components.stat-card', [
        'label'  => 'Total Tiket',
        'value'  => number_format($summary['total']),
        'icon'   => '<path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/>',
        'color'  => 'purple',
    ])
    @include('components.stat-card', [
        'label'  => 'Terselesaikan',
        'value'  => number_format($summary['resolved']),
        'icon'   => '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/>',
        'color'  => 'green',
    ])
    @include('components.stat-card', [
        'label'  => 'SLA Breach',
        'value'  => number_format($summary['breached']),
        'icon'   => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>',
        'color'  => 'red',
    ])
    @include('components.stat-card', [
        'label'  => 'Rata-rata Rating',
        'value'  => $summary['avg_rating'] ? number_format($summary['avg_rating'], 1) . '/5' : '-',
        'icon'   => '<polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>',
        'color'  => 'orange',
    ])
</div>

{{-- Filters & Export Actions --}}
<div class="bg-white rounded-2xl shadow-sm overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-slate-100">
        <form method="GET" action="{{ route('admin.reports.index') }}" class="flex flex-wrap items-end gap-3">

            {{-- Date From --}}
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Dari Tanggal</label>
                <input type="date" name="from" value="{{ request('from') }}" class="px-3 py-2 text-sm bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-purple-500/20 focus:bg-white transition-colors">
            </div>

            {{-- Date To --}}
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Sampai Tanggal</label>
                <input type="date" name="to" value="{{ request('to') }}" class="px-3 py-2 text-sm bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-purple-500/20 focus:bg-white transition-colors">
            </div>

            {{-- Status --}}
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Status</label>
                <select name="status" class="py-2 pl-3 pr-8 text-sm bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-purple-500/20 transition-colors">
                    <option value="">Semua</option>
                    <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Open</option>
                    <option value="assigned" {{ request('status') === 'assigned' ? 'selected' : '' }}>Assigned</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="waiting" {{ request('status') === 'waiting' ? 'selected' : '' }}>Waiting</option>
                    <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                    <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                </select>
            </div>

            {{-- Priority --}}
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Prioritas</label>
                <select name="priority" class="py-2 pl-3 pr-8 text-sm bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-purple-500/20 transition-colors">
                    <option value="">Semua</option>
                    <option value="critical" {{ request('priority') === 'critical' ? 'selected' : '' }}>Critical</option>
                    <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
                    <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
                </select>
            </div>

            {{-- Category --}}
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Kategori</label>
                <select name="category_id" class="py-2 pl-3 pr-8 text-sm bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-purple-500/20 transition-colors">
                    <option value="">Semua</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Technician --}}
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Teknisi</label>
                <select name="technician_id" class="py-2 pl-3 pr-8 text-sm bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-purple-500/20 transition-colors">
                    <option value="">Semua</option>
                    @foreach($technicians as $tech)
                        <option value="{{ $tech->id }}" {{ request('technician_id') == $tech->id ? 'selected' : '' }}>{{ $tech->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Apply --}}
            <button type="submit" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-xl shadow-sm transition-colors">
                Terapkan Filter
            </button>

            @if(request()->hasAny(['from', 'to', 'status', 'priority', 'category_id', 'technician_id']))
                <a href="{{ route('admin.reports.index') }}" class="px-4 py-2 bg-slate-100 text-slate-600 hover:bg-slate-200 text-sm font-medium rounded-xl transition-colors">
                    Reset
                </a>
            @endif
        </form>
    </div>

    {{-- Export Buttons --}}
    <div class="px-6 py-3 bg-slate-50/50 border-b border-slate-100 flex items-center gap-3">
        <span class="text-xs font-medium text-slate-500 mr-2">Export:</span>

        <a href="{{ route('admin.reports.export', ['format' => 'excel'] + request()->all()) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-50 text-green-700 hover:bg-green-100 text-xs font-semibold rounded-lg transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/><path d="M10 12l4 6"/><path d="M14 12l-4 6"/></svg>
            Excel (.xlsx)
        </a>

        <a href="{{ route('admin.reports.export', ['format' => 'pdf'] + request()->all()) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-50 text-red-700 hover:bg-red-100 text-xs font-semibold rounded-lg transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
            PDF (.pdf)
        </a>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════════ --}}
{{-- GRAFIK VISUALISASI --}}
{{-- ══════════════════════════════════════════════════════════════════════════ --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

    {{-- Area/Line Chart: Tren Tiket Harian --}}
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-base font-semibold text-slate-800">Tren Tiket Harian</h3>
                <p class="text-xs text-slate-400 mt-0.5">Berdasarkan filter aktif</p>
            </div>
            <div class="w-10 h-10 bg-purple-50 rounded-xl flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-purple-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="m19 9-5 5-4-4-3 3"/></svg>
            </div>
        </div>
        <div class="relative" style="height: 260px;">
            <canvas id="reportChartTrend"></canvas>
        </div>
    </div>

    {{-- Doughnut Chart: Komposisi Prioritas --}}
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-base font-semibold text-slate-800">Komposisi Prioritas</h3>
                <p class="text-xs text-slate-400 mt-0.5">Berdasarkan filter aktif</p>
            </div>
            <div class="w-10 h-10 bg-red-50 rounded-xl flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.21 15.89A10 10 0 1 1 8 2.83"/><path d="M22 12A10 10 0 0 0 12 2v10z"/></svg>
            </div>
        </div>
        <div class="relative flex items-center justify-center" style="height: 260px;">
            <canvas id="reportChartPriority"></canvas>
        </div>
    </div>

</div>

{{-- Data Table --}}
<div class="bg-white rounded-2xl shadow-sm overflow-hidden">
    <div class="overflow-auto">
        @if($tickets->isEmpty())
            <div class="px-6 py-20 text-center flex flex-col items-center justify-center">
                <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
                </div>
                <p class="text-slate-600 font-medium">Tidak ada data tiket</p>
                <p class="text-sm text-slate-400 mt-1">Sesuaikan filter di atas untuk melihat hasil.</p>
            </div>
        @else
            <table class="w-full text-left">
                <thead class="sticky top-0 bg-white/95 backdrop-blur z-10 shadow-[0_1px_2px_rgba(0,0,0,0.02)]">
                    <tr>
                        <th class="px-5 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wider">Tiket</th>
                        <th class="px-5 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wider">Pelapor</th>
                        <th class="px-5 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wider">Kategori</th>
                        <th class="px-5 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wider">Prioritas</th>
                        <th class="px-5 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wider">Status</th>
                        <th class="px-5 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wider">Teknisi</th>
                        <th class="px-5 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wider">SLA</th>
                        <th class="px-5 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wider">Rating</th>
                        <th class="px-5 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wider">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($tickets as $ticket)
                        @php
                            $slaStatus = 'On Track';
                            if ($ticket->resolved_at && $ticket->resolution_deadline) {
                                $slaStatus = $ticket->resolved_at->lte($ticket->resolution_deadline) ? 'On Time' : 'Breached';
                            } elseif ($ticket->resolution_deadline && now()->gt($ticket->resolution_deadline)) {
                                $slaStatus = 'Breached';
                            }
                        @endphp
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-5 py-3.5">
                                <p class="text-sm font-semibold text-purple-600">{{ $ticket->ticket_number }}</p>
                                <p class="text-xs text-slate-500 max-w-[180px] truncate mt-0.5" title="{{ $ticket->title }}">{{ $ticket->title }}</p>
                            </td>
                            <td class="px-5 py-3.5">
                                <p class="text-sm text-slate-700">{{ $ticket->user->name }}</p>
                                <p class="text-xs text-slate-400">{{ $ticket->user->department?->name ?? '-' }}</p>
                            </td>
                            <td class="px-5 py-3.5 text-sm text-slate-600">{{ $ticket->category?->name ?? '-' }}</td>
                            <td class="px-5 py-3.5">
                                @include('components.priority-badge', ['priority' => $ticket->priority])
                            </td>
                            <td class="px-5 py-3.5">
                                @include('components.status-badge', ['status' => $ticket->status])
                            </td>
                            <td class="px-5 py-3.5 text-sm text-slate-600">
                                {{ $ticket->activeAssignment?->technician?->name ?? '-' }}
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center gap-1 text-xs font-medium {{ $slaStatus === 'Breached' ? 'text-red-600' : ($slaStatus === 'On Time' ? 'text-green-600' : 'text-slate-500') }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $slaStatus === 'Breached' ? 'bg-red-500' : ($slaStatus === 'On Time' ? 'bg-green-500' : 'bg-slate-400') }}"></span>
                                    {{ $slaStatus }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                @if($ticket->rating)
                                    <span class="text-sm font-medium text-orange-500">{{ $ticket->rating->rating }}/5</span>
                                @else
                                    <span class="text-xs text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                <p class="text-xs text-slate-500">{{ $ticket->created_at->format('d M Y') }}</p>
                                <p class="text-[10px] text-slate-400 mt-0.5">{{ $ticket->created_at->format('H:i') }}</p>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    @if($tickets->hasPages())
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
            {{ $tickets->links() }}
        </div>
    @endif
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

    // ── 1. Area/Line Chart: Tren Tiket Harian ────────────────────────────
    const trendCtx = document.getElementById('reportChartTrend').getContext('2d');
    const gradient = trendCtx.createLinearGradient(0, 0, 0, 260);
    gradient.addColorStop(0, 'rgba(139, 92, 246, 0.15)');
    gradient.addColorStop(1, 'rgba(139, 92, 246, 0)');

    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: @json($chartTrend['labels']),
            datasets: [{
                label: 'Tiket per Hari',
                data: @json($chartTrend['data']),
                borderColor: '#8B5CF6',
                backgroundColor: gradient,
                borderWidth: 2.5,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#8B5CF6',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
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
                    ticks: { color: '#94A3B8', font: { size: 11 }, maxRotation: 45 },
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

    // ── 2. Doughnut Chart: Komposisi Prioritas ───────────────────────────
    new Chart(document.getElementById('reportChartPriority'), {
        type: 'doughnut',
        data: {
            labels: @json($chartPriority['labels']),
            datasets: [{
                data: @json($chartPriority['data']),
                backgroundColor: [
                    '#EF4444', // Critical — red
                    '#F97316', // High — orange
                    '#3B82F6', // Medium — blue
                    '#94A3B8', // Low — slate
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

});
</script>
@endpush
