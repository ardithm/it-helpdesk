@extends('layouts.app')

@section('title', 'Detail Aset - ' . $asset->asset_code)
@section('page-title', 'Detail Aset')
@section('page-subtitle', 'Informasi lengkap dan riwayat perangkat')

@section('content')

@php
    function getStatusFormat($status) {
        return match($status) {
            'active'      => ['label' => 'Aktif (Siap Digunakan)', 'color' => 'bg-green-50 text-green-700 ring-green-600/20', 'dot' => 'bg-green-500'],
            'maintenance' => ['label' => 'Maintenance (Diperbaiki)', 'color' => 'bg-orange-50 text-orange-700 ring-orange-600/20', 'dot' => 'bg-orange-500'],
            'retired'     => ['label' => 'Pensiun (Nonaktif)', 'color' => 'bg-slate-50 text-slate-700 ring-slate-600/20', 'dot' => 'bg-slate-500'],
            default       => ['label' => ucfirst($status), 'color' => 'bg-slate-50 text-slate-700 ring-slate-600/20', 'dot' => 'bg-slate-500'],
        };
    }
    
    $statusFmt = getStatusFormat($asset->status);
@endphp

<div class="mb-6 flex items-center justify-between">
    <a href="{{ route('admin.assets.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-purple-600 transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 19-7-7 7-7"/><path d="M19 12H5"/></svg>
        Kembali ke Daftar Aset
    </a>
    
    <a href="{{ route('admin.assets.edit', $asset) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-sm font-medium rounded-xl shadow-sm transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
        Edit Aset
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    {{-- Kolom Kiri: Info Aset --}}
    <div class="lg:col-span-1 space-y-6">
        
        {{-- Card: Ringkasan --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 text-center border-b border-slate-100 bg-slate-50/50">
                <div class="w-20 h-20 mx-auto bg-white border border-slate-200 rounded-2xl flex items-center justify-center shadow-sm mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="3" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
                </div>
                <h2 class="text-lg font-bold text-slate-800">{{ $asset->brand }} {{ $asset->model }}</h2>
                <p class="text-sm text-slate-500 mt-1 font-mono">{{ $asset->asset_code }}</p>
                
                <div class="mt-4 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium ring-1 ring-inset {{ $statusFmt['color'] }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $statusFmt['dot'] }}"></span>
                    {{ $statusFmt['label'] }}
                </div>
            </div>
            
            <div class="p-6">
                <h3 class="text-xs font-bold tracking-wider text-slate-400 uppercase mb-4">Spesifikasi Detail</h3>
                <dl class="space-y-4">
                    <div>
                        <dt class="text-xs font-medium text-slate-500">Kategori</dt>
                        <dd class="text-sm font-semibold text-slate-800 mt-0.5">{{ $asset->category }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-slate-500">Serial Number (SN)</dt>
                        <dd class="text-sm font-mono font-medium text-slate-800 mt-0.5">{{ $asset->serial_number }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-slate-500">Tanggal Pembelian</dt>
                        <dd class="text-sm font-medium text-slate-800 mt-0.5">
                            {{ $asset->purchase_date ? $asset->purchase_date->translatedFormat('d F Y') : '-' }}
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        {{-- Card: Penugasan --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100">
                <h3 class="text-sm font-bold text-slate-800">Ditugaskan Kepada</h3>
            </div>
            <div class="p-6">
                @if($asset->user)
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center text-purple-700 font-bold text-lg">
                            {{ strtoupper(substr($asset->user->name, 0, 2)) }}
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-800">{{ $asset->user->name }}</p>
                            <p class="text-xs text-slate-500 mt-0.5">{{ $asset->user->email }}</p>
                            <p class="text-xs text-slate-500 mt-0.5">{{ $asset->department?->name ?? 'Tanpa Departemen' }}</p>
                        </div>
                    </div>
                @elseif($asset->department)
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 22h14a2 2 0 0 0 2-2V7.5L14.5 2H6a2 2 0 0 0-2 2v4"/><polyline points="14 2 14 8 20 8"/><path d="M2 15h10"/><path d="m9 18 3-3-3-3"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-800">Departemen Umum</p>
                            <p class="text-xs text-slate-500 mt-0.5">{{ $asset->department->name }}</p>
                        </div>
                    </div>
                @else
                    <div class="text-center py-4">
                        <div class="w-10 h-10 mx-auto bg-slate-50 rounded-full flex items-center justify-center mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" x2="19" y1="8" y2="14"/><line x1="22" x2="16" y1="11" y2="11"/></svg>
                        </div>
                        <p class="text-sm font-medium text-slate-600">Belum ditugaskan ke siapapun</p>
                    </div>
                @endif
            </div>
        </div>

    </div>
    
    {{-- Kolom Kanan: Histori & Tiket --}}
    <div class="lg:col-span-2 space-y-6">
        
        {{-- Card: Histori Perubahan --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-purple-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/><path d="M12 7v5l4 2"/></svg>
                    Riwayat Aset (History)
                </h3>
            </div>
            <div class="p-6">
                @if($asset->histories->isEmpty())
                    <p class="text-sm text-slate-500 italic">Belum ada riwayat tercatat.</p>
                @else
                    <div class="relative border-l-2 border-slate-100 ml-3 space-y-8">
                        @foreach($asset->histories->sortByDesc('created_at') as $history)
                            <div class="relative pl-6">
                                <span class="absolute -left-[9px] top-1 w-4 h-4 rounded-full bg-white border-2 border-purple-500"></span>
                                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-2 mb-1">
                                    <h4 class="text-sm font-semibold text-slate-800">{{ $history->action }}</h4>
                                    <span class="text-xs font-medium text-slate-400 bg-slate-50 px-2 py-0.5 rounded-md whitespace-nowrap">
                                        {{ $history->created_at->translatedFormat('d M Y, H:i') }}
                                    </span>
                                </div>
                                <p class="text-sm text-slate-600 leading-relaxed">{{ $history->description }}</p>
                                
                                @if($history->ticket_id)
                                    <a href="{{ route('helpdesk.tickets.show', $history->ticket_id) }}" class="inline-flex items-center gap-1.5 mt-2 text-xs font-medium text-purple-600 hover:text-purple-700 bg-purple-50 hover:bg-purple-100 px-2.5 py-1 rounded-md transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="M15 2H9a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1Z"/></svg>
                                        Lihat Tiket #{{ $history->ticket_id }}
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
        
        {{-- Card: Tiket Terkait --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-orange-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.54 15H17a2 2 0 0 0-2 2v4.54"/><path d="M7 3.34V5a3 3 0 0 0 3 3v0a2 2 0 0 1 2 2v0c0 1.1.9 2 2 2v0a2 2 0 0 0 2-2v0c0-1.1.9-2 2-2h1.66"/><path d="M11 21.95V18a2 2 0 0 0-2-2v0a2 2 0 0 1-2-2v-1a2 2 0 0 0-2-2 2 2 0 0 1-2-2 2 2 0 0 0-2-2H2.05"/><circle cx="12" cy="12" r="10"/></svg>
                    Tiket Terkait Perangkat Ini
                </h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    @if($asset->tickets->isEmpty())
                        <tr>
                            <td class="p-8 text-center text-sm text-slate-500">Belum ada tiket yang diajukan untuk perangkat ini.</td>
                        </tr>
                    @else
                        <thead class="bg-slate-50/50">
                            <tr>
                                <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Tiket</th>
                                <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase">Pelapor</th>
                                <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($asset->tickets->sortByDesc('created_at') as $ticket)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-3">
                                        <p class="text-sm font-semibold text-slate-800">{{ $ticket->title }}</p>
                                        <p class="text-xs text-slate-500">{{ $ticket->created_at->translatedFormat('d M Y') }}</p>
                                    </td>
                                    <td class="px-6 py-3">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-700">
                                            {{ ucfirst($ticket->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3">
                                        <p class="text-sm text-slate-700">{{ $ticket->user->name }}</p>
                                    </td>
                                    <td class="px-6 py-3 text-right">
                                        <a href="{{ route('helpdesk.tickets.show', $ticket) }}" class="text-xs font-medium text-purple-600 hover:text-purple-700">Detail &rarr;</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    @endif
                </table>
            </div>
        </div>

    </div>
</div>

@endsection
