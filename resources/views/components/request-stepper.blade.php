@props([
    'steps' => [],
    'currentStep' => 0,
])

@php
    $stepColors = [
        'completed' => 'bg-emerald-500 text-white',
        'current' => 'bg-indigo-600 text-white',
        'pending' => 'bg-slate-800 text-slate-400 border border-slate-700',
    ];
@endphp

<div class="w-full">
    <ol class="flex items-center justify-between">
        @foreach($steps as $index => $step)
            @php
                $status = $index < $currentStep ? 'completed' : ($index === $currentStep ? 'current' : 'pending');
            @endphp
            <li class="flex flex-col items-center gap-2">
                <div class="flex items-center gap-2">
                    <span class="flex items-center justify-center w-8 h-8 rounded-full text-sm font-medium {{ $stepColors[$status] }}">
                        @if($index < $currentStep)
                            <x-icon name="check" class="h-4 w-4" />
                        @else
                            {{ $index + 1 }}
                        @endif
                    </span>
                </div>
                <span class="text-xs font-medium text-slate-400">{{ $step }}</span>
            </li>
            @if($index < count($steps) - 1)
                <div class="flex-1 h-0.5 mx-2 {{ $index < $currentStep ? 'bg-emerald-500' : 'bg-slate-800' }}"></div>
            @endif
        @endforeach
    </ol>
</div>
