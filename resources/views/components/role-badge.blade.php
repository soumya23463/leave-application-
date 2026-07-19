@props(['role'])

@php
    $map = [
        'superadmin' => 'bg-rose-100 text-rose-700',
        'admin'      => 'bg-purple-100 text-purple-700',
        'employee'   => 'bg-blue-100 text-blue-700',
    ];
    $cls   = $map[$role] ?? 'bg-gray-100 text-gray-700';
    $label = $role === 'superadmin' ? 'Super Admin' : ucfirst($role);
@endphp

<span {{ $attributes->merge(['class' => "inline-block text-[10px] uppercase font-medium px-1.5 py-0.5 rounded $cls"]) }}>{{ $label }}</span>
