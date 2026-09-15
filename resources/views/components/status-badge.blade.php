{{-- Status Badge — design_style.md Section 4.C: minimalistic colored dots --}}
@props(['status'])

@php
    $map = [
        'open'        => ['dot' => 'bg-blue-500',    'text' => 'text-blue-600',   'label' => 'Open'],
        'assigned'    => ['dot' => 'bg-purple-500',  'text' => 'text-purple-600', 'label' => 'Assigned'],
        'in_progress' => ['dot' => 'bg-orange-400',  'text' => 'text-orange-500', 'label' => 'In Progress'],
        'waiting'     => ['dot' => 'bg-orange-400',  'text' => 'text-orange-500', 'label' => 'Waiting'],
        'resolved'    => ['dot' => 'bg-green-500',   'text' => 'text-green-600',  'label' => 'Resolved'],
        'closed'      => ['dot' => 'bg-green-500',   'text' => 'text-green-600',  'label' => 'Closed'],
    ];
    $s = $map[$status] ?? ['dot' => 'bg-slate-400', 'text' => 'text-slate-500', 'label' => ucfirst($status)];
@endphp

<span class="inline-flex items-center gap-1.5 text-xs font-medium {{ $s['text'] }}">
    <span class="w-2 h-2 rounded-full {{ $s['dot'] }} inline-block"></span>
    {{ $s['label'] }}
</span>
