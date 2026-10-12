@props([
    'user' => null,
    'size' => 'md',
])

@php
    $user = $user ?? auth()->user();

    $sizes = [
        'xs' => ['box' => 'w-8 h-8', 'text' => 'text-xs'],
        'sm' => ['box' => 'w-10 h-10', 'text' => 'text-sm'],
        'md' => ['box' => 'w-12 h-12', 'text' => 'text-base'],
        'lg' => ['box' => 'w-20 h-20', 'text' => 'text-2xl'],
    ];

    $dim = $sizes[$size] ?? $sizes['md'];
    $initials = strtoupper(substr($user->name ?? $user->email ?? 'BP', 0, 2));
@endphp

<span {{ $attributes->merge(['class' => 'avatar-ring ' . $dim['box']]) }}>
    @if($user->profile_picture_url)
        <img src="{{ $user->profile_picture_url }}" alt="{{ $user->name ?? 'Profile' }}" loading="lazy">
    @else
        <span class="avatar-initials {{ $dim['text'] }}">{{ $initials }}</span>
    @endif
</span>
