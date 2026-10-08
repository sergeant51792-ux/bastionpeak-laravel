@props([
    'status',
    'label' => null,
])

@php
    $label = $label ?? ucfirst(str_replace('_', ' ', $status));
    $colorMap = [
        'active' => 'text-emerald-400 bg-emerald-500/20',
        'frozen' => 'text-sky-400 bg-sky-500/20',
        'locked' => 'text-amber-400 bg-amber-500/20',
        'closed' => 'text-red-400 bg-red-500/20',
        'approved' => 'text-emerald-400 bg-emerald-500/20',
        'pending_review' => 'text-amber-400 bg-amber-500/20',
        'rejected' => 'text-red-400 bg-red-500/20',
        'reversed' => 'text-slate-400 bg-slate-500/20',
        'credited' => 'text-indigo-400 bg-indigo-500/20',
        'pending_match' => 'text-purple-400 bg-purple-500/20',
        'deactivated' => 'text-slate-400 bg-slate-500/20',
        'active_de' => 'text-emerald-400 bg-emerald-500/20',
    ];

    $iconMap = [
        'active' => 'check',
        'frozen' => 'snowflake',
        'locked' => 'lock',
        'closed' => 'x',
        'approved' => 'check',
        'pending_review' => 'clock',
        'rejected' => 'x',
        'reversed' => 'refresh-cw',
        'credited' => 'check',
        'pending_match' => 'clock',
        'deactivated' => 'minus-circle',
    ];
@endphp

<span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium {{ $colorMap[$status] ?? 'text-slate-400 bg-slate-500/20' }}">
    @if($iconMap[$status] ?? null)
        <x-icon name="{{ $iconMap[$status] }}" class="h-3.5 w-3.5" />
    @endif
    {{ $label }}
</span>
