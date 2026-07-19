@props(['user' => null, 'size' => 9])

@php
    // Tailwind size classes (w-{n} h-{n}); default 2.25rem (size 9).
    $dim = "w-{$size} h-{$size}";
    $isAdmin = $user?->role === 'admin';
    $ring = $isAdmin ? 'from-violet-500 to-violet-700' : 'from-blue-500 to-blue-700';
@endphp

@if ($user?->avatar_url)
    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
         {{ $attributes->merge(['class' => "$dim rounded-full object-cover ring-2 ring-white shadow-sm"]) }}>
@else
    <span {{ $attributes->merge(['class' => "$dim rounded-full bg-gradient-to-br $ring text-white font-semibold inline-flex items-center justify-center shadow-sm"]) }}>
        {{ $user?->initials ?? '?' }}
    </span>
@endif
