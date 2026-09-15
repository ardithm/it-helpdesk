<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — ITDesk</title>
    <meta name="description" content="@yield('description', 'Sistem Helpdesk & IT Ticketing Berbasis Web')">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-800">
<div class="flex h-screen overflow-hidden" id="app-layout">

    {{-- ── Sidebar ──────────────────────────────────────────────────────────── --}}
    @include('layouts.partials.sidebar')

    {{-- ── Main Content ─────────────────────────────────────────────────────── --}}
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

        {{-- Topbar --}}
        @include('layouts.partials.topbar')

        {{-- Page Content --}}
        <main class="flex-1 overflow-y-auto p-6">

            {{-- Flash Messages --}}
            @include('layouts.partials.flash')

            @yield('content')
        </main>
    </div>
</div>

{{-- Mobile sidebar overlay --}}
<div id="sidebar-overlay" class="fixed inset-0 bg-black/30 z-30 hidden lg:hidden" onclick="toggleSidebar()"></div>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    }
</script>

@stack('scripts')
</body>
</html>
