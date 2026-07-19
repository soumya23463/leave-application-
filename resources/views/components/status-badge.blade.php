@props(['status'])

@php
    $s = strtolower((string) $status);
    $map = [
        'approved' => 'bg-green-100 text-green-800',
        'active'   => 'bg-green-100 text-green-800',
        'pending'  => 'bg-yellow-100 text-yellow-800',
        'rejected' => 'bg-red-100 text-red-800',
        'inactive' => 'bg-gray-200 text-gray-700',
    ];
    $classes = $map[$s] ?? 'bg-gray-100 text-gray-700';
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize $classes"]) }}>
    {{ $status }}
</span>
