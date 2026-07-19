<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Weekend Setting</h2>
            <a href="{{ route('weekend-settings.index') }}" class="text-sm text-gray-600 hover:underline">← Back</a>
        </div>
    </x-slot>

    <x-card title="Details">
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div class="sm:col-span-2">
                <dt class="text-gray-500">Weekend days</dt>
                <dd class="font-medium text-gray-800">{{ implode(', ', (array) $weekendSetting->days) }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Effective date</dt>
                <dd class="font-medium text-gray-800">{{ $weekendSetting->effective_date->format('d M Y') }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Status</dt>
                <dd><x-status-badge :status="$weekendSetting->status ? 'Active' : 'Inactive'" /></dd>
            </div>
        </dl>

        <div class="mt-6 flex items-center gap-3">
            <a href="{{ route('weekend-settings.edit', $weekendSetting) }}" class="inline-flex items-center px-4 py-2 bg-brand-600 text-white text-sm font-medium rounded-md hover:bg-brand-700">
                Edit
            </a>
        </div>
    </x-card>
</x-app-layout>
