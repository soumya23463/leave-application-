<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Holidays</h2>
            @if (isAdmin())
                <div class="flex items-center gap-2">
                    <a href="{{ route('holidays.create') }}" class="inline-flex items-center px-4 py-2 bg-brand-600 text-white text-sm font-medium rounded-md hover:bg-brand-700">
                        + New Holiday
                    </a>
                    <a href="{{ route('holidays.sample') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50">
                        Download sample
                    </a>
                </div>
            @endif
        </div>
    </x-slot>

    <x-card>
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-4">
            <form method="GET" class="flex items-center gap-2">
                <select name="status" onchange="this.form.submit()" class="border-gray-300 rounded-md shadow-sm text-sm">
                    <option value="">All statuses</option>
                    <option value="1" @selected(request('status') === '1')>Active</option>
                    <option value="0" @selected(request('status') === '0')>Inactive</option>
                </select>
                @if (request()->filled('status'))
                    <a href="{{ route('holidays.index') }}" class="text-sm text-gray-500 hover:underline">Reset</a>
                @endif
            </form>

            @if (isAdmin())
                <form method="POST" action="{{ route('holidays.import') }}" enctype="multipart/form-data" class="flex items-center gap-2">
                    @csrf
                    <input type="file" name="file" required accept=".csv,.txt,.xlsx,.xls" class="text-sm text-gray-600 file:mr-2 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-sm file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200" />
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-brand-600 text-white text-sm font-medium rounded-md hover:bg-brand-700">
                        Import CSV
                    </button>
                </form>
            @endif
        </div>

        @error('file')
            <div class="mb-4 rounded-md bg-red-50 border border-red-100 px-4 py-2 text-sm text-red-700">{{ $message }}</div>
        @enderror

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b">
                        <th class="py-2 pr-4">Name</th>
                        <th class="py-2 pr-4">Date</th>
                        <th class="py-2 pr-4">Description</th>
                        <th class="py-2 pr-4">Status</th>
                        @if (isAdmin())<th class="py-2 text-right">Actions</th>@endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($holidays as $holiday)
                        <tr>
                            <td class="py-2 pr-4">
                                <a href="{{ route('holidays.show', $holiday) }}" class="text-brand-600 hover:underline">{{ $holiday->name }}</a>
                            </td>
                            <td class="py-2 pr-4">{{ $holiday->date->format('d M Y') }}</td>
                            <td class="py-2 pr-4 text-gray-600">{{ \Illuminate\Support\Str::limit($holiday->description, 50) ?: '—' }}</td>
                            <td class="py-2 pr-4"><x-status-badge :status="$holiday->status ? 'Active' : 'Inactive'" /></td>
                            @if (isAdmin())
                                <td class="py-2 text-right whitespace-nowrap">
                                    <a href="{{ route('holidays.edit', $holiday) }}" class="text-gray-600 hover:underline">Edit</a>
                                    <form method="POST" action="{{ route('holidays.destroy', $holiday) }}" class="inline ms-2" onsubmit="return confirm('Delete this holiday?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                    </form>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr><td colspan="{{ isAdmin() ? 5 : 4 }}" class="py-8 text-center text-gray-500">No holidays found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($holidays->hasPages())
            <div class="mt-4">{{ $holidays->links() }}</div>
        @endif
    </x-card>
</x-app-layout>
