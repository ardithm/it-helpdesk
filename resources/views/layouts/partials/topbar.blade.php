{{-- Topbar --}}
<header class="bg-white border-b border-slate-100 px-6 py-3 flex items-center justify-between flex-shrink-0">

    {{-- Left: Hamburger (mobile) + Page title --}}
    <div class="flex items-center gap-3">
        {{-- Hamburger toggle for mobile --}}
        <button onclick="toggleSidebar()" class="lg:hidden p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors" aria-label="Toggle menu">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/>
            </svg>
        </button>

        <div>
            <h1 class="text-lg font-semibold text-slate-800">@yield('page-title', 'Dashboard')</h1>
            @hasSection('page-subtitle')
                <p class="text-xs text-slate-500 mt-0.5">@yield('page-subtitle')</p>
            @endif
        </div>
    </div>

    {{-- Right: Actions --}}
    <div class="flex items-center gap-3">

        {{-- Role badge --}}
        <span class="hidden sm:inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-purple-50 text-xs font-medium text-purple-600 capitalize">
            <span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span>
            {{ auth()->user()->role }}
        </span>

        {{-- Profile link --}}
        <a href="{{ route('profile.show') }}" class="p-2 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors" title="Profil saya">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="8" r="5"/><path d="M20 21a8 8 0 1 0-16 0"/>
            </svg>
        </a>

        {{-- Logout --}}
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="p-2 rounded-lg text-slate-400 hover:text-red-500 hover:bg-red-50 transition-colors" title="Logout">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/>
                </svg>
            </button>
        </form>
    </div>
</header>
