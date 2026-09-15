{{-- Priority Badge — minimalistic dot style --}}
@props(['priority'])

@php
    $map = [
        'low'      => ['dot' => 'bg-slate-400',  'text' => 'text-slate-500',  'label' => 'Low'],
        'medium'   => ['dot' => 'bg-blue-500',   'text' => 'text-blue-600',   'label' => 'Medium'],
        'high'     => ['dot' => 'bg-orange-500',  'text' => 'text-orange-600', 'label' => 'High'],
        'critical' => ['dot' => 'bg-red-500',     'text' => 'text-red-600',    'label' => 'Critical'],
    ];
    $p = $map[$priority] ?? ['dot' => 'bg-slate-400', 'text' => 'text-slate-500', 'label' => ucfirst($priority)];
@endphp

<span class="inline-flex items-center gap-1.5 text-xs font-medium {{ $p['text'] }}">
    <span class="w-2 h-2 rounded-full {{ $p['dot'] }} inline-block"></span>
    {{ $p['label'] }}
</span>
