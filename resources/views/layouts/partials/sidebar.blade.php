{{-- Sidebar Navigation — design_style.md Section 4.A --}}
@php
    $role = auth()->user()->role;
    $currentRoute = request()->route()?->getName() ?? '';
@endphp

<aside
    id="sidebar"
    class="fixed lg:static inset-y-0 left-0 z-40 w-64 bg-white flex flex-col transition-transform duration-200 ease-in-out -translate-x-full lg:translate-x-0 shadow-sm"
>
    {{-- Logo --}}
    <div class="flex items-center gap-3 px-5 py-5 border-b border-slate-100">
        <div class="w-9 h-9 bg-purple-600 rounded-xl flex items-center justify-center flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect width="20" height="14" x="2" y="3" rx="2"/><path d="M8 21h8M12 17v4"/>
            </svg>
        </div>
        <span class="text-lg font-semibold text-slate-800 tracking-tight">ITDesk</span>
    </div>

    {{-- Menu --}}
    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">

        {{-- ── Semua Role: Dashboard ──────────────────────────────────────── --}}
        @php
            $dashboardRoute = match ($role) {
                'admin'      => 'admin.dashboard',
                'helpdesk'   => 'helpdesk.dashboard',
                'technician' => 'technician.dashboard',
                default      => 'user.dashboard',
            };
        @endphp
        @include('layouts.partials.sidebar-item', [
            'route'  => $dashboardRoute,
            'label'  => 'Dashboard',
            'icon'   => '<rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/>',
            'active' => str_contains($currentRoute, '.dashboard'),
        ])

        {{-- ── User: Tiket Saya ───────────────────────────────────────────── --}}
        @if(in_array($role, ['user', 'helpdesk', 'technician', 'admin']))
            <p class="px-3 pt-4 pb-1 text-xs font-semibold text-slate-400 uppercase tracking-wider">Tiket</p>

            @include('layouts.partials.sidebar-item', [
                'route'  => 'user.tickets.index',
                'label'  => 'Tiket Saya',
                'icon'   => '<path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/>',
                'active' => str_starts_with($currentRoute, 'user.tickets'),
            ])

            @if($role === 'user')
                @include('layouts.partials.sidebar-item', [
                    'route'  => 'user.tickets.create',
                    'label'  => 'Buat Tiket Baru',
                    'icon'   => '<path d="M5 12h14"/><path d="M12 5v14"/>',
                    'active' => $currentRoute === 'user.tickets.create',
                ])
            @endif
        @endif

        {{-- ── Helpdesk: Kelola Tiket ─────────────────────────────────────── --}}
        @if(in_array($role, ['helpdesk', 'admin']))
            <p class="px-3 pt-4 pb-1 text-xs font-semibold text-slate-400 uppercase tracking-wider">Helpdesk</p>

            @include('layouts.partials.sidebar-item', [
                'route'  => 'helpdesk.tickets.index',
                'label'  => 'Kelola Tiket',
                'icon'   => '<path d="M16 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V8Z"/><path d="M15 3v4a2 2 0 0 0 2 2h4"/><path d="M10 16h4"/><path d="M10 12h4"/>',
                'active' => str_starts_with($currentRoute, 'helpdesk.tickets'),
            ])
        @endif

        {{-- ── Technician: Tugas Saya ─────────────────────────────────────── --}}
        @if(in_array($role, ['technician', 'admin']))
            <p class="px-3 pt-4 pb-1 text-xs font-semibold text-slate-400 uppercase tracking-wider">Teknisi</p>

            @include('layouts.partials.sidebar-item', [
                'route'  => 'technician.tickets.index',
                'label'  => 'Tugas Saya',
                'icon'   => '<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>',
                'active' => str_starts_with($currentRoute, 'technician.tickets'),
            ])

            @include('layouts.partials.sidebar-item', [
                'route'  => 'manage.knowledge.index',
                'label'  => 'Kelola Knowledge Base',
                'icon'   => '<path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/><path d="M8 7h6"/><path d="M12 11h.01"/><path d="M16 11h.01"/>',
                'active' => str_starts_with($currentRoute, 'manage.knowledge'),
            ])
        @endif

        {{-- ── Admin: Master Data ─────────────────────────────────────────── --}}
        @if($role === 'admin')
            <p class="px-3 pt-4 pb-1 text-xs font-semibold text-slate-400 uppercase tracking-wider">Admin</p>

            @include('layouts.partials.sidebar-item', [
                'route'  => 'admin.users.index',
                'label'  => 'Manajemen User',
                'icon'   => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
                'active' => str_starts_with($currentRoute, 'admin.users'),
            ])

            @include('layouts.partials.sidebar-item', [
                'route'  => 'admin.departments.index',
                'label'  => 'Departemen',
                'icon'   => '<path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"/><path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"/><path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"/>',
                'active' => str_starts_with($currentRoute, 'admin.departments'),
            ])

            @include('layouts.partials.sidebar-item', [
                'route'  => 'admin.categories.index',
                'label'  => 'Kategori Tiket',
                'icon'   => '<path d="M12 2H2v10l9.29 9.29c.94.94 2.48.94 3.42 0l6.58-6.58c.94-.94.94-2.48 0-3.42L12 2Z"/><path d="M7 7h.01"/>',
                'active' => str_starts_with($currentRoute, 'admin.categories'),
            ])

            @include('layouts.partials.sidebar-item', [
                'route'  => 'admin.sla.index',
                'label'  => 'SLA Policy',
                'icon'   => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>',
                'active' => str_starts_with($currentRoute, 'admin.sla'),
            ])

            @include('layouts.partials.sidebar-item', [
                'route'  => 'admin.assets.index',
                'label'  => 'Aset IT',
                'icon'   => '<rect width="20" height="14" x="2" y="3" rx="2"/><path d="M8 21h8M12 17v4"/>',
                'active' => str_starts_with($currentRoute, 'admin.assets'),
            ])

            @include('layouts.partials.sidebar-item', [
                'route'  => 'admin.reports.index',
                'label'  => 'Laporan',
                'icon'   => '<path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/><path d="M12 18v-6"/><path d="M8 18v-1"/><path d="M16 18v-3"/>',
                'active' => str_starts_with($currentRoute, 'admin.reports'),
            ])
        @endif

        {{-- ── Semua Role: Knowledge Base ─────────────────────────────────── --}}
        <p class="px-3 pt-4 pb-1 text-xs font-semibold text-slate-400 uppercase tracking-wider">Lainnya</p>

        @include('layouts.partials.sidebar-item', [
            'route'  => 'knowledge.index',
            'label'  => 'Knowledge Base (Pusat Bantuan)',
            'icon'   => '<path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/><path d="M8 7h6"/>',
            'active' => str_starts_with($currentRoute, 'knowledge.'),
        ])

    </nav>

    {{-- User badge di bawah sidebar --}}
    <div class="px-3 py-3 border-t border-slate-100">
        <a href="{{ route('profile.show') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-50 transition-colors group">
            <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                <span class="text-sm font-semibold text-purple-600">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-slate-700 truncate group-hover:text-slate-900">{{ auth()->user()->name }}</p>
                <p class="text-xs text-slate-400 capitalize">{{ auth()->user()->role }}</p>
            </div>
        </a>
    </div>
</aside>
