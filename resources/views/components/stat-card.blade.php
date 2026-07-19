@props(['label', 'value', 'color' => 'brand', 'sub' => null])

@php
    $accent = [
        'brand'  => 'text-brand-600',
        'green'  => 'text-green-600',
        'yellow' => 'text-yellow-600',
        'red'    => 'text-red-600',
        'purple' => 'text-purple-600',
        'gray'   => 'text-gray-700',
    ][$color] ?? 'text-brand-600';
@endphp

<div class="bg-white shadow-sm rounded-lg p-5">
    <p class="text-sm font-medium text-gray-500">{{ $label }}</p>
    <p class="mt-2 text-3xl font-bold {{ $accent }}">{{ $value }}</p>
    @if ($sub)
        <p class="mt-1 text-xs text-gray-400">{{ $sub }}</p>
    @endif
</div>
