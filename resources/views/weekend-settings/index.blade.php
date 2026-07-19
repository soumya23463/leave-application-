<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Weekend Settings</h2>
            <a href="{{ route('weekend-settings.create') }}" class="inline-flex items-center px-4 py-2 bg-brand-600 text-white text-sm font-medium rounded-md hover:bg-brand-700">
                + New Setting
            </a>
        </div>
    </x-slot>

    <x-card>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b">
                        <th class="py-2 pr-4">Days</th>
                        <th class="py-2 pr-4">Effective Date</th>
                        <th class="py-2 pr-4">Status</th>
                        <th class="py-2 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($weekendSettings as $setting)
                        <tr>
                            <td class="py-2 pr-4">{{ implode(', ', (array) $setting->days) }}</td>
                            <td class="py-2 pr-4">{{ $setting->effective_date->format('d M Y') }}</td>
                            <td class="py-2 pr-4"><x-status-badge :status="$setting->status ? 'Active' : 'Inactive'" /></td>
                            <td class="py-2 text-right whitespace-nowrap">
                                <a href="{{ route('weekend-settings.edit', $setting) }}" class="text-brand-600 hover:underline">Edit</a>
                                <form method="POST" action="{{ route('weekend-settings.destroy', $setting) }}" class="inline"
                                      onsubmit="return confirm('Delete this weekend setting?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="ms-2 text-red-600 hover:underline">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-8 text-center text-gray-500">No weekend settings found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($weekendSettings->hasPages())
            <div class="mt-4">{{ $weekendSettings->links() }}</div>
        @endif
    </x-card>
</x-app-layout>
