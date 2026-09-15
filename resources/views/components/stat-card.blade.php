{{-- Stat Card Component — design_style.md Section 4.B --}}
@props([
    'label'    => '',
    'value'    => 0,
    'icon'     => '',
    'color'    => 'purple',   // purple, blue, green, orange, red
    'subtitle' => null,
])

@php
    $colorMap = [
        'purple' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-600', 'icon' => 'text-purple-500'],
        'blue'   => ['bg' => 'bg-blue-100',   'text' => 'text-blue-600',   'icon' => 'text-blue-500'],
        'green'  => ['bg' => 'bg-green-100',   'text' => 'text-green-600',  'icon' => 'text-green-500'],
        'orange' => ['bg' => 'bg-orange-100',  'text' => 'text-orange-600', 'icon' => 'text-orange-500'],
        'red'    => ['bg' => 'bg-red-100',     'text' => 'text-red-600',    'icon' => 'text-red-500'],
    ];
    $c = $colorMap[$color] ?? $colorMap['purple'];
@endphp

<div class="bg-white rounded-2xl p-5 shadow-sm hover:shadow-md transition-shadow">
    <div class="flex items-start justify-between">
        <div>
            <p class="text-sm text-slate-500 font-medium">{{ $label }}</p>
            <p class="text-2xl font-semibold text-slate-800 mt-1">{{ $value }}</p>
            @if($subtitle)
                <p class="text-xs text-slate-400 mt-1">{{ $subtitle }}</p>
            @endif
        </div>
        <div class="w-10 h-10 {{ $c['bg'] }} rounded-xl flex items-center justify-center flex-shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 {{ $c['icon'] }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                {!! $icon !!}
            </svg>
        </div>
    </div>
</div>
