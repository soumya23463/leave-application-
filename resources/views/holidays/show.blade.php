<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Holiday</h2>
            @if (isAdmin())
                <a href="{{ route('holidays.edit', $holiday) }}" class="inline-flex items-center px-4 py-2 bg-brand-600 text-white text-sm font-medium rounded-md hover:bg-brand-700">
                    Edit
                </a>
            @endif
        </div>
    </x-slot>

    <x-card :title="$holiday->name">
        <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
            <div>
                <dt class="text-gray-500">Name</dt>
                <dd class="mt-1 text-gray-800">{{ $holiday->name }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Date</dt>
                <dd class="mt-1 text-gray-800">{{ $holiday->date->format('j F, Y') }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Status</dt>
                <dd class="mt-1"><x-status-badge :status="$holiday->status ? 'Active' : 'Inactive'" /></dd>
            </div>
            <div class="sm:col-span-3">
                <dt class="text-gray-500">Description</dt>
                <dd class="mt-1 text-gray-800 whitespace-pre-line">{{ $holiday->description ?: '—' }}</dd>
            </div>
        </dl>

        <div class="mt-6">
            <a href="{{ route('holidays.index') }}" class="text-sm text-gray-600 hover:text-gray-800">&larr; Back to holidays</a>
        </div>
    </x-card>
</x-app-layout>
