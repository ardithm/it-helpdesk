{{-- Sidebar menu item component --}}
@props(['route', 'label', 'icon', 'active' => false])

<a
    href="{{ route($route) }}"
    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150
        {{ $active
            ? 'bg-purple-100 text-purple-600'
            : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700'
        }}"
>
    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-shrink-0 {{ $active ? 'text-purple-600' : 'text-slate-400' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        {!! $icon !!}
    </svg>
    <span>{{ $label }}</span>
</a>
