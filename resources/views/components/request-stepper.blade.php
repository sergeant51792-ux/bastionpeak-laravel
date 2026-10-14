@props([
    'steps' => [],
    'currentStep' => 0,
])

<div class="w-full">
    <ol class="flex items-center justify-between">
        @foreach($steps as $index => $step)
            @php
                $status = $index < $currentStep ? 'completed' : ($index === $currentStep ? 'current' : 'pending');
            @endphp
            <li class="flex flex-col items-center gap-2">
                <div class="flex items-center gap-2">
                    <span class="flex items-center justify-center w-8 h-8 rounded-full text-sm font-medium
                        @if($status === 'completed') bg-[var(--success)] text-white
                        @elseif($status === 'current') bg-[var(--accent)] text-white
                        @else bg-[var(--line)] text-[var(--text-muted)] border border-[var(--line)]
                        @endif">
                        @if($index < $currentStep)
                            <x-icon name="check" class="h-4 w-4" />
                        @else
                            {{ $index + 1 }}
                        @endif
                    </span>
                </div>
                <span class="text-xs font-medium text-[var(--text-muted)]">{{ $step }}</span>
            </li>
            @if($index < count($steps) - 1)
                <div class="flex-1 h-0.5 mx-2 {{ $index < $currentStep ? 'bg-[var(--success)]' : 'bg-[var(--line)]' }}"></div>
            @endif
        @endforeach
    </ol>
</div>
