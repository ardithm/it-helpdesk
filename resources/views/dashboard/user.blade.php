@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Selamat datang, ' . auth()->user()->name)

@section('content')

{{-- Stat Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    @include('components.stat-card', [
        'label' => 'Tiket Open',
        'value' => $stats['open'],
        'color' => 'blue',
        'icon'  => '<circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/>',
    ])
    @include('components.stat-card', [
        'label' => 'Sedang Diproses',
        'value' => $stats['in_progress'],
        'color' => 'orange',
        'icon'  => '<path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>',
    ])
    @include('components.stat-card', [
        'label' => 'Resolved',
        'value' => $stats['resolved'],
        'color' => 'green',
        'icon'  => '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/>',
    ])
    @include('components.stat-card', [
        'label' => 'Closed',
        'value' => $stats['closed'],
        'color' => 'purple',
        'icon'  => '<path d="M18 6 6 18M6 6l12 12"/>',
        'subtitle' => $pendingRating > 0 ? $pendingRating . ' tiket menunggu rating' : null,
    ])
</div>

{{-- Recent Tickets --}}
<div class="bg-white rounded-2xl shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
        <h2 class="text-base font-semibold text-slate-800">Tiket Terbaru Saya</h2>
        <a href="{{ route('user.tickets.index') }}" class="text-sm text-purple-600 hover:text-purple-700 font-medium transition-colors">Lihat Semua</a>
    </div>

    @if($recentTickets->isEmpty())
        <div class="px-6 py-12 text-center">
            <div class="w-12 h-12 bg-slate-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/>
                </svg>
            </div>
            <p class="text-sm text-slate-500">Belum ada tiket. Mulai buat tiket pertama Anda.</p>
            <a href="{{ route('user.tickets.create') }}" class="inline-flex items-center gap-2 mt-4 px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-xl hover:bg-purple-700 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                Buat Tiket Baru
            </a>
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
                        <th class="text-left px-6 py-3 text-xs uppercase text-slate-400 font-semibold">Teknisi</th>
                        <th class="text-left px-6 py-3 text-xs uppercase text-slate-400 font-semibold">Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentTickets as $ticket)
                        <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-3.5">
                                <a href="{{ route('user.tickets.show', $ticket) }}" class="text-sm font-medium text-purple-600 hover:text-purple-700">{{ $ticket->ticket_number }}</a>
                            </td>
                            <td class="px-6 py-3.5 text-sm text-slate-600 max-w-xs truncate">{{ $ticket->title }}</td>
                            <td class="px-6 py-3.5">
                                @include('components.status-badge', ['status' => $ticket->status])
                            </td>
                            <td class="px-6 py-3.5">
                                @include('components.priority-badge', ['priority' => $ticket->priority])
                            </td>
                            <td class="px-6 py-3.5 text-sm text-slate-500">
                                {{ $ticket->activeAssignment?->technician?->name ?? '-' }}
                            </td>
                            <td class="px-6 py-3.5 text-sm text-slate-400">{{ $ticket->created_at->format('d M Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

@endsection
