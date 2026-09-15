@extends('layouts.auth')

@section('title', 'Masuk')
@section('description', 'Login ke ITDesk — Sistem Helpdesk & IT Ticketing')

@section('content')
<div class="min-h-screen bg-slate-50 flex">

    {{-- ── Kiri: Branding Panel ────────────────────────────────────────────── --}}
    <div class="hidden lg:flex lg:w-1/2 xl:w-3/5 relative bg-gradient-to-br from-purple-600 via-purple-700 to-violet-800 flex-col justify-between p-12 overflow-hidden">

        {{-- Dekorasi background --}}
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-24 -right-24 w-96 h-96 bg-white/5 rounded-full"></div>
            <div class="absolute top-1/3 -left-16 w-64 h-64 bg-white/5 rounded-full"></div>
            <div class="absolute bottom-0 right-1/4 w-48 h-48 bg-purple-500/30 rounded-full blur-2xl"></div>
            <div class="absolute top-1/2 right-0 w-72 h-72 bg-violet-900/40 rounded-full blur-3xl"></div>
        </div>

        {{-- Logo & Brand --}}
        <div class="relative z-10">
            <div class="flex items-center gap-3">
                {{-- Icon: Monitor --}}
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="20" height="14" x="2" y="3" rx="2"/><path d="M8 21h8M12 17v4"/>
                    </svg>
                </div>
                <span class="text-xl font-semibold text-white tracking-tight">ITDesk</span>
            </div>
        </div>

        {{-- Tagline & Fitur --}}
        <div class="relative z-10">
            <h1 class="text-3xl xl:text-4xl font-bold text-white leading-tight mb-4">
                Kelola Layanan IT<br>Lebih Terstruktur
            </h1>
            <p class="text-purple-200 text-base leading-relaxed mb-10 max-w-md">
                Platform helpdesk & ticketing terpusat untuk memantau, mengelola, dan menyelesaikan setiap permintaan layanan IT secara efisien.
            </p>

            {{-- Feature pills --}}
            <div class="flex flex-col gap-3">
                @php
                    $features = [
                        ['icon' => '<path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/>', 'text' => 'Pantau status tiket secara real-time'],
                        ['icon' => '<path d="M12 20h9"/><path d="M16.376 3.622a1 1 0 0 1 3.002 3.002L7.368 18.635a2 2 0 0 1-.855.506l-2.872.838a.5.5 0 0 1-.62-.62l.838-2.872a2 2 0 0 1 .506-.854z"/>', 'text' => 'Assignment & klasifikasi tiket otomatis'],
                        ['icon' => '<path d="M12 2v20M2 12h20"/><circle cx="12" cy="12" r="10"/>', 'text' => 'Monitor SLA & performa tim IT'],
                    ];
                @endphp
                @foreach ($features as $feature)
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-white/15 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-purple-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                {!! $feature['icon'] !!}
                            </svg>
                        </div>
                        <span class="text-purple-100 text-sm">{{ $feature['text'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Footer branding --}}
        <div class="relative z-10">
            <p class="text-purple-300 text-xs">
                &copy; {{ date('Y') }} ITDesk &mdash; Sistem Helpdesk & IT Ticketing
            </p>
        </div>
    </div>

    {{-- ── Kanan: Form Login ────────────────────────────────────────────────── --}}
    <div class="flex-1 flex items-center justify-center p-6 sm:p-10 lg:p-16">
        <div class="w-full max-w-sm">

            {{-- Mobile logo --}}
            <div class="flex items-center gap-2 mb-8 lg:hidden">
                <div class="w-8 h-8 bg-purple-600 rounded-lg flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="20" height="14" x="2" y="3" rx="2"/><path d="M8 21h8M12 17v4"/>
                    </svg>
                </div>
                <span class="text-lg font-semibold text-slate-800">ITDesk</span>
            </div>

            {{-- Header --}}
            <div class="mb-8">
                <h2 class="text-2xl font-semibold text-slate-800 mb-1">Selamat datang</h2>
                <p class="text-sm text-slate-500">Masuk untuk melanjutkan ke dashboard Anda</p>
            </div>

            {{-- Flash message: sukses logout --}}
            @if (session('success'))
                <div class="mb-5 flex items-center gap-2.5 bg-green-50 border border-green-200 text-green-700 rounded-xl px-4 py-3 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            {{-- Error global --}}
            @if ($errors->any())
                <div class="mb-5 flex items-start gap-2.5 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/><path d="m15 9-6 6M9 9l6 6"/>
                    </svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            {{-- Form --}}
            <form method="POST" action="{{ route('login.post') }}" class="space-y-5" id="login-form">
                @csrf

                {{-- Email --}}
                <div class="space-y-1.5">
                    <label for="email" class="block text-sm font-medium text-slate-700">
                        Email
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                            </svg>
                        </div>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            autocomplete="email"
                            value="{{ old('email') }}"
                            placeholder="nama@perusahaan.com"
                            required
                            class="w-full pl-10 pr-4 py-2.5 text-sm bg-white border @error('email') border-red-400 @else border-slate-200 @enderror rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-150"
                        >
                    </div>
                    @error('email')
                        <p class="text-xs text-red-500 flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="space-y-1.5">
                    <label for="password" class="block text-sm font-medium text-slate-700">
                        Password
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                        </div>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            autocomplete="current-password"
                            placeholder="Masukkan password"
                            required
                            class="w-full pl-10 pr-11 py-2.5 text-sm bg-white border @error('password') border-red-400 @else border-slate-200 @enderror rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-150"
                        >
                        {{-- Toggle show/hide password --}}
                        <button
                            type="button"
                            id="toggle-password"
                            aria-label="Tampilkan atau sembunyikan password"
                            class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-slate-600 transition-colors"
                        >
                            {{-- Eye icon (show) --}}
                            <svg id="icon-eye" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/>
                            </svg>
                            {{-- Eye-off icon (hide) --}}
                            <svg id="icon-eye-off" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" x2="22" y1="2" y2="22"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-xs text-red-500 flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Remember me --}}
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <div class="relative">
                            <input
                                id="remember"
                                name="remember"
                                type="checkbox"
                                class="sr-only peer"
                            >
                            <div class="w-4 h-4 border border-slate-300 rounded bg-white peer-checked:bg-purple-600 peer-checked:border-purple-600 transition-all duration-150 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-2.5 h-2.5 text-white opacity-0 peer-checked:opacity-100 transition-opacity" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 6 9 17l-5-5"/>
                                </svg>
                            </div>
                        </div>
                        <span class="text-sm text-slate-600 select-none group-hover:text-slate-800 transition-colors">Ingat saya</span>
                    </label>
                </div>

                {{-- Submit button --}}
                <button
                    type="submit"
                    id="btn-login"
                    class="w-full flex items-center justify-center gap-2 bg-purple-600 hover:bg-purple-700 active:bg-purple-800 text-white font-medium text-sm py-2.5 px-4 rounded-xl transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 disabled:opacity-60 disabled:cursor-not-allowed"
                >
                    {{-- Loading spinner (hidden by default) --}}
                    <svg id="btn-spinner" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 animate-spin hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 12a9 9 0 1 1-6.219-8.56"/>
                    </svg>
                    {{-- Login icon --}}
                    <svg id="btn-icon" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" x2="3" y1="12" y2="12"/>
                    </svg>
                    <span id="btn-text">Masuk</span>
                </button>

            </form>

            {{-- Footer --}}
            <p class="mt-8 text-center text-xs text-slate-400">
                Butuh bantuan? Hubungi
                <a href="mailto:admin@itdesk.local" class="text-purple-600 hover:text-purple-700 font-medium transition-colors">administrator</a>
            </p>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    // ─── Toggle Password Visibility ───────────────────────────────────────────
    const toggleBtn  = document.getElementById('toggle-password');
    const passInput  = document.getElementById('password');
    const iconEye    = document.getElementById('icon-eye');
    const iconEyeOff = document.getElementById('icon-eye-off');

    toggleBtn.addEventListener('click', () => {
        const isHidden = passInput.type === 'password';
        passInput.type     = isHidden ? 'text' : 'password';
        iconEye.classList.toggle('hidden', isHidden);
        iconEyeOff.classList.toggle('hidden', !isHidden);
    });

    // ─── Custom Checkbox Render ───────────────────────────────────────────────
    const rememberCheckbox = document.getElementById('remember');
    const checkIcon = rememberCheckbox.nextElementSibling.querySelector('svg');

    rememberCheckbox.addEventListener('change', () => {
        checkIcon.style.opacity = rememberCheckbox.checked ? '1' : '0';
    });

    // ─── Loading State on Submit ──────────────────────────────────────────────
    const form      = document.getElementById('login-form');
    const btnLogin  = document.getElementById('btn-login');
    const btnText   = document.getElementById('btn-text');
    const btnIcon   = document.getElementById('btn-icon');
    const btnSpinner = document.getElementById('btn-spinner');

    form.addEventListener('submit', () => {
        btnLogin.disabled = true;
        btnText.textContent = 'Memproses...';
        btnIcon.classList.add('hidden');
        btnSpinner.classList.remove('hidden');
    });
</script>
@endpush
