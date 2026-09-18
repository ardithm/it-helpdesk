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

        {{-- Notifications Dropdown --}}
        <div x-data="{ open: false }" class="relative">
            @php
                $unreadNotifications = auth()->user()->unreadNotifications;
                $allNotifications = auth()->user()->notifications()->take(5)->get();
            @endphp
            <button @click="open = !open" @click.outside="open = false" class="relative p-2 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors" title="Notifikasi">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/>
                </svg>
                @if($unreadNotifications->count() > 0)
                    <span class="absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-red-500 border-2 border-white rounded-full"></span>
                @endif
            </button>

            {{-- Dropdown Panel --}}
            <div x-show="open" 
                 x-transition.opacity.duration.200ms
                 style="display: none;"
                 class="absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden z-50">
                <div class="px-4 py-3 bg-slate-50/50 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-slate-800">Notifikasi</h3>
                    @if($unreadNotifications->count() > 0)
                        <form action="{{ route('notifications.markAllAsRead') }}" method="POST">
                            @csrf
                            <button type="submit" class="text-xs font-medium text-purple-600 hover:text-purple-700">Tandai sudah dibaca</button>
                        </form>
                    @endif
                </div>

                <div class="max-h-[300px] overflow-y-auto">
                    @forelse($allNotifications as $notification)
                        <a href="{{ route('notifications.markAsRead', $notification->id) }}" class="block px-4 py-3 border-b border-slate-50 hover:bg-slate-50 transition-colors {{ is_null($notification->read_at) ? 'bg-purple-50/30' : '' }}">
                            <div class="flex gap-3">
                                <div class="w-8 h-8 rounded-full bg-{{ $notification->data['color'] ?? 'purple' }}-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 text-{{ $notification->data['color'] ?? 'purple' }}-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <use href="#icon-{{ $notification->data['icon'] ?? 'bell' }}"></use>
                                        {{-- We just hardcode a bell if the svg `use` is tricky --}}
                                        <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-slate-800 mb-0.5 {{ is_null($notification->read_at) ? 'text-purple-700' : '' }}">{{ $notification->data['title'] }}</p>
                                    <p class="text-xs text-slate-500 line-clamp-2">{{ $notification->data['message'] }}</p>
                                    <p class="text-[10px] text-slate-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="px-4 py-8 text-center text-slate-500 text-sm">
                            Belum ada notifikasi.
                        </div>
                    @endforelse
                </div>
                <div class="px-4 py-2 bg-slate-50/50 border-t border-slate-100 text-center">
                    <a href="{{ route('notifications.index') }}" class="text-xs font-medium text-slate-500 hover:text-purple-600">Lihat Semua Notifikasi</a>
                </div>
            </div>
        </div>

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
