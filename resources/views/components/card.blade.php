@props(['title' => null, 'actions' => null])

<div {{ $attributes->merge(['class' => 'bg-white shadow-sm sm:rounded-lg']) }}>
    @if ($title || $actions)
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800">{{ $title }}</h3>
            @if ($actions)
                <div class="flex items-center gap-2">{{ $actions }}</div>
            @endif
        </div>
    @endif
    <div class="p-6">
        {{ $slot }}
    </div>
</div>
