@extends('layouts.app')

@section('title', 'Manajemen Aset IT')
@section('page-title', 'Aset IT')
@section('page-subtitle', 'Kelola inventaris perangkat keras dan lunak')

@section('content')

@php
    function getStatusFormat($status) {
        return match($status) {
            'active'      => ['label' => 'Aktif', 'color' => 'bg-green-50 text-green-700 ring-green-600/20', 'dot' => 'bg-green-500'],
            'maintenance' => ['label' => 'Maintenance', 'color' => 'bg-orange-50 text-orange-700 ring-orange-600/20', 'dot' => 'bg-orange-500'],
            'retired'     => ['label' => 'Pensiun', 'color' => 'bg-slate-50 text-slate-700 ring-slate-600/20', 'dot' => 'bg-slate-500'],
            default       => ['label' => ucfirst($status), 'color' => 'bg-slate-50 text-slate-700 ring-slate-600/20', 'dot' => 'bg-slate-500'],
        };
    }
@endphp

<div class="bg-white rounded-2xl shadow-sm overflow-hidden flex flex-col h-[calc(100vh-140px)]">

    {{-- Top Actions & Filters --}}
    <div class="px-6 py-4 border-b border-slate-100 flex flex-col lg:flex-row lg:items-center justify-between gap-4 flex-shrink-0">
        <form method="GET" action="{{ route('admin.assets.index') }}" class="flex flex-1 flex-wrap items-center gap-3">
            
            {{-- Search --}}
            <div class="relative flex-1 min-w-[200px] max-w-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode, merk, seri..." class="w-full pl-9 pr-4 py-2 text-sm bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-purple-500/20 focus:bg-white transition-colors">
            </div>

            {{-- Status Filter --}}
            <select name="status" class="py-2 pl-3 pr-8 text-sm bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-purple-500/20 focus:bg-white transition-colors" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                <option value="maintenance" {{ request('status') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                <option value="retired" {{ request('status') === 'retired' ? 'selected' : '' }}>Pensiun</option>
            </select>

            {{-- Department Filter --}}
            <select name="department_id" class="py-2 pl-3 pr-8 text-sm bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-purple-500/20 focus:bg-white transition-colors" onchange="this.form.submit()">
                <option value="">Semua Departemen</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                @endforeach
            </select>

            <button type="submit" class="px-4 py-2 bg-slate-100 text-slate-600 hover:bg-slate-200 text-sm font-medium rounded-xl transition-colors">
                Filter
            </button>
        </form>

        <a href="{{ route('admin.assets.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-xl shadow-sm transition-colors flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="3" rx="2"/><path d="M8 21h8M12 17v4"/><path d="M12 7v4"/><path d="M10 9h4"/></svg>
            Tambah Aset
        </a>
    </div>

    {{-- Table --}}
    <div class="flex-1 overflow-auto">
        @if($assets->isEmpty())
            <div class="px-6 py-20 text-center flex flex-col items-center justify-center h-full">
                <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="3" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
                </div>
                <p class="text-slate-600 font-medium">Tidak ada data aset IT</p>
                <p class="text-sm text-slate-400 mt-1">Sesuaikan filter atau tambahkan aset baru.</p>
            </div>
        @else
            <table class="w-full text-left">
                <thead class="sticky top-0 bg-white/95 backdrop-blur z-10 shadow-[0_1px_2px_rgba(0,0,0,0.02)]">
                    <tr>
                        <th class="px-6 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wider">Kode Aset</th>
                        <th class="px-6 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wider">Informasi Perangkat</th>
                        <th class="px-6 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wider">Penugasan</th>
                        <th class="px-6 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wider text-center">Status</th>
                        <th class="px-6 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($assets as $asset)
                        @php $statusFmt = getStatusFormat($asset->status); @endphp
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-3.5">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-mono font-medium bg-slate-100 text-slate-700">
                                    {{ $asset->asset_code }}
                                </span>
                            </td>
                            <td class="px-6 py-3.5">
                                <p class="text-sm font-semibold text-slate-800">{{ $asset->brand }} {{ $asset->model }}</p>
                                <p class="text-xs text-slate-500 mt-0.5">{{ $asset->category }} &bull; SN: {{ $asset->serial_number }}</p>
                            </td>
                            <td class="px-6 py-3.5">
                                @if($asset->user)
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-purple-100 flex items-center justify-center text-purple-700 font-bold text-[10px]">
                                            {{ strtoupper(substr($asset->user->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-slate-800">{{ $asset->user->name }}</p>
                                            <p class="text-xs text-slate-500">{{ $asset->department?->name ?? '-' }}</p>
                                        </div>
                                    </div>
                                @elseif($asset->department)
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center text-blue-700">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 22h14a2 2 0 0 0 2-2V7.5L14.5 2H6a2 2 0 0 0-2 2v4"/><polyline points="14 2 14 8 20 8"/><path d="M2 15h10"/><path d="m9 18 3-3-3-3"/></svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-slate-800">Umum / Divisi</p>
                                            <p class="text-xs text-slate-500">{{ $asset->department->name }}</p>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400 italic">Belum ditugaskan</span>
                                @endif
                            </td>
                            <td class="px-6 py-3.5 text-center">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium ring-1 ring-inset {{ $statusFmt['color'] }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $statusFmt['dot'] }}"></span>
                                    {{ $statusFmt['label'] }}
                                </span>
                            </td>
                            <td class="px-6 py-3.5 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.assets.show', $asset) }}" class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Lihat Detail">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </a>
                                    <a href="{{ route('admin.assets.edit', $asset) }}" class="p-2 text-slate-400 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-colors" title="Edit Aset">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
    
    @if($assets->hasPages())
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
            {{ $assets->links() }}
        </div>
    @endif
</div>

@endsection
